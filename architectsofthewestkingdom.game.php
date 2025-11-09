<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * ArchitectsOfTheWestKingdom implementation : © <Nicolas Gocel> <nicolas.gocel@gmail.com>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 * 
 * architectsofthewestkingdom.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 *
 */


require_once(APP_GAMEMODULE_PATH . 'module/table/table.game.php');

include('modules/arcplayer.php');
include('modules/building.php');
include('modules/apprentice.php');


class ArchitectsOfTheWestKingdom extends \Bga\GameFramework\Table
{
    public static ArchitectsOfTheWestKingdom $instance;
    public \Bga\GameFramework\Components\Deck $buildings;
    public \Bga\GameFramework\Components\Deck $apprentices;
    public \Bga\GameFramework\Components\Deck $rewards;
    public \Bga\GameFramework\Components\Deck $blackmarkets;
    public $standardBox;
    public $player_colors;
    public $asymetricStart;
    public $cathedralCosts;
    public $cathedralSpots;
    public $rewardsGain;
    public $virtue;
    public $cathedralVP;

    public $blackmarket1;
    public $blackmarket2;

    function __construct()
    {
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();
        self::$instance = $this;

        $this->initGameStateLabels(array(
            "tax" => 10,
            "no_undo" => 11,
            "finish" => 12,
            //      ...

            "board_side" => 100,
            "live_scoring" => 101,
        ));



        $this->apprentices = $this->deckFactory->createDeck('apprentice');
        $this->apprentices->autoreshuffle = true;

        $this->buildings = $this->deckFactory->createDeck('building');
        $this->buildings->autoreshuffle = true;

        $this->rewards = $this->deckFactory->createDeck('reward');

        $this->blackmarkets = $this->deckFactory->createDeck('blackmarket');
        $this->blackmarkets->autoreshuffle = true;

        $this->notify->addDecorator(function (string $message, array $args) {
            if (!isset($args['player_id'])) {
                $args['player_id'] = $this->getActivePlayerId();
            }
            if (isset($args['player_id']) && !isset($args['player_name']) && str_contains($message, '${player_name}')) {
                $args['player_name'] = $this->getPlayerNameById($args['player_id']);
            }
            return $args;
        });
    }

    protected function getGameName()
    {
        // Used for translations and stuff. Please do not modify.
        return "architectsofthewestkingdom";
    }

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame($players, $options = array())
    {
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = $this->getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar, type) VALUES ";
        $values = array();
        $index = 1;

        $indexrand = [1, 2, 3, 4, 5];
        shuffle($indexrand);

        foreach ($players as $player_id => $player) {
            if ($this->getGameStateValue('board_side') == 1) {
                $type = $this->standardBox[$index - 1] + 10;
                $color = $this->player_colors[$index];
            } else {
                $type = $this->standardBox[$indexrand[$index - 1] - 1];
                if ($type % 2 == 1) {
                    $type = $type - bga_rand(0, 1);
                } else {
                    $type = $type + bga_rand(0, 1);
                }

                $color = $this->player_colors[$indexrand[$index - 1]];
            }
            $index++;
            $values[] = "('" . $player_id . "','$color','" . $player['player_canal'] . "','" . addslashes($player['player_name']) . "','" . addslashes($player['player_avatar']) . "', {$type})";
        }
        $sql .= implode(',', $values);
        $this->DbQuery($sql);

        if ($this->getGameStateValue('board_side') == 1) {
            $this->reattributeColorsBasedOnPreferences($players, $gameinfos['player_colors']);
            $this->reloadPlayersBasicInfos();

            $players = $this->getCollectionFromDb("select * from player order by player_no desc");
            foreach ($players as $player_id => $player) {
                $color = $player['player_color'];
                $type = 10 + $this->standardBox[array_search($color, $this->player_colors) - 1];
                $this->DbQuery("update player set type = {$type} where player_id = {$player['player_id']}");
            }
        } else {

            $this->reloadPlayersBasicInfos();
        }

        /************ Start the game initialization *****/

        // Init global values with their initial values
        $this->setGameStateInitialValue('tax', 4);
        $this->setGameStateInitialValue('no_undo', 0);
        $this->setGameStateInitialValue('finish', 0);

        // Init game statistics
        $this->initStat('player', 'turns_number', 0);
        $this->initStat('player', 'buildings', 0);
        $this->initStat('player', 'cathedral', 0);
        $this->initStat('player', 'virtue', 0);
        $this->initStat('player', 'debt', 0);
        $this->initStat('player', 'resource', 0);
        $this->initStat('player', 'silver', 0);
        $this->initStat('player', 'prison', 0);

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas(): array
    {
        $result = array();

        $current_player_id = $this->getCurrentPlayerId();    // !! We must only return informations visible by this player !!

        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player.player_id id, player_no, player_score score, player_color color,type, res1, res2, res3, res4, res5, res6, 0 res7, res8, cathedral, virtue FROM player left join (select count(*) res8, player_id from worker where location like 'reserve%' group by player_id) as R on R.player_id = player.player_id";
        $result['players'] = $this->getCollectionFromDb($sql);
        foreach ($result['players'] as $player_id => $player) {
            $result['players'][$player_id]['res7']  = $this->getUniqueValueFromDB("select count(*) from building where card_location = 'hand{$player_id}'");
            $result['players'][$player_id]['res12']  = $this->getUniqueValueFromDB("select count(*) from worker where location = 'prison_{$player_id}'");
            $result['players'][$player_id]['res13']  = $this->getUniqueValueFromDB("select count(*) from debt where player_id = {$player_id} and paid = 0");
            $result['players'][$player_id]['res14']  = $this->getUniqueValueFromDB("select count(*) from debt where player_id = {$player_id} and paid = 1");
        }

        $result['side'] = $this->getGameStateValue('board_side') == SIDEA ? 'A' : 'B';
        $result['tax'] = $this->getGameStateValue('tax');

        $sql = "SELECT worker.*, player_color FROM `worker` inner join player on player.player_id = worker.player_id WHERE 1 order by location_arg, id";
        $result['workers'] = $this->getObjectListFromDB($sql);

        $sql = "SELECT * from apprentice where card_location <> 'deck'";
        $result['apprentices'] = $this->getCollectionFromDb($sql);

        $sql = "SELECT * from building where card_location <> 'deck' and card_location not like 'hand%' and card_location not like 'selectCards%'";
        $result['buildings'] = $this->getCollectionFromDb($sql);

        $sql = "SELECT * from building where card_location = 'hand{$current_player_id}'";
        $result['hand'] = $this->getCollectionFromDb($sql);

        $result['rewardnb'] = $this->rewards->countCardInLocation("deck");
        $result['blackmarket1'] = $this->blackmarkets->getCardOnTop("deck");
        $result['blackmarket2'] = $this->blackmarkets->getCardOnTop("discard");

        $score = array();

        if ($this->getGameStateValue('finish') == 1) {
            $players = $this->getCollectionFromDb("select * from player order by player_no desc");
            foreach ($players as $player_id => $player) { {
                    $p = new ARCPlayer($player['player_id']);
                    $score[] = $p->updateVP(true);
                }
            }
        }
        $result['score'] = $score;

        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {

        $nbplayers = max(2, $this->getUniqueValueFromDB("select count(*) from player"));
        $nbmeeplesOnLocation = $this->getUniqueValueFromDB("select count(*)  from worker where location = 'guildhall'");
        $nbmax = ($nbplayers + 1) * 4;
        return min(100, (100 * $nbmeeplesOnLocation) / $nbmax);
    }


    //////////////////////////////////////////////////////////////////////////////
    //////////// Utility functions
    ////////////    

    function checkArgs($arg1, $arg2)
    {
        $ret = $this->argPlayerTurn();

        if (!in_array($arg1, array_keys($ret['selectable'])) && !in_array($arg1, $ret['buttons'])) {
            throw new feException("Not a valid move");
        } else if ($arg2 != null && (!is_array($ret['selectable'][$arg1]['target']) || !in_array($arg2, $ret['selectable'][$arg1]['target']))) {
            throw new feException("Not a valid target");
        }
    }

    function getPlayerRelativePositions()
    {
        $result = array();

        $players = $this->loadPlayersBasicInfos();
        $nextPlayer = $this->createNextPlayerTable(array_keys($players));

        $current_player = $this->getCurrentPlayerId();

        if (!isset($nextPlayer[$current_player])) {
            // Spectator mode: take any player for south
            $player_id = $nextPlayer[0];
        } else {
            // Normal mode: current player is on south
            $player_id = $current_player;
        }
        $result[$player_id] = 0;

        for ($i = 1; $i < count($players); $i++) {
            $player_id = $nextPlayer[$player_id];
            $result[$player_id] = $i;
        }
        return $result;
    }

    function addPendingSub($player_id, $function, $sub, $arg = NULL, $arg2 = NULL, $arg3 = NULL)
    {
        $sql = "INSERT INTO pending (player_id, function, sub, arg, arg2, arg3) VALUES (" . $player_id . ", '" . $function . "', '" . $sub . "', '" . $arg . "', '" . $arg2 . "', '" . $arg3 . "')";
        $this->DbQuery($sql);
    }

    function addPending($player_id, $function, $arg = NULL, $arg2 = NULL, $arg3 = NULL, $arg4 = NULL)
    {
        $sql = "INSERT INTO pending (player_id, function, arg, arg2, arg3, arg4) VALUES (" . $player_id . ", '" . $function . "', '" . $arg . "', '" . $arg2 . "', '" . $arg3 . "', '" . $arg4 . "')";
        $this->DbQuery($sql);
    }

    function addPendingFirst($player_id, $function, $arg = NULL, $arg2 = NULL)
    {
        $minid = $this->getUniqueValueFromDB("select min(id) from pending") - 1;
        $sql = "INSERT INTO pending (id, player_id, function, arg, arg2) VALUES (" . $minid . "," . $player_id . ", '" . $function . "', '" . $arg . "', '" . $arg2 . "')";
        $this->DbQuery($sql);
    }

    /**
     * Call pending action
     * @param $execute - false - just check if execution required, true - execute
     */
    function callPending($pending, bool $execute, $arg1 = null, $arg2 = null)
    {
        if (class_exists($pending['function'])) {
            $obj = new $pending['function']();
            $obj->player_id = $this->getActivePlayerId();
            if ($pending['player_id'] != null) {
                $obj->player_id = $pending['player_id'];
            }
            $obj->player = new ARCPlayer($obj->player_id);

            $method = "";
            if (!$execute) {
                $name = "arg" . $method;
            } else {
                $name = "do" . $method;
            }
            $ret = $obj->$name($pending['arg'], $pending['arg2'], $arg1, $arg2);
        } else {
            $obj = $this;
            if ($pending['player_id'] != null) {
                $obj = new ARCPlayer($pending['player_id']);
            }

            $fname = "";
            if (!$execute) {
                $fname .= "arg";
            }
            $fname .= $pending['function'];

            $ret = null;
            if (method_exists($obj, $fname)) {
                $ret = $obj->$fname($pending['arg'], $pending['arg2'], $arg1, $arg2);
            }
        }
        return $ret;
    }

    function debug_drawBuilding(int $number)
    {
        $cards = $this->buildings->getCardsOfType($number);
        if (count($cards) == 0) throw new feException("Building not found $number");
        $this->buildings->insertCardOnExtremePosition(array_values($cards)[0]["id"], "deck", true);
        $cards = $this->buildings->pickCardsForLocation(1, 'deck', 'hand' . $this->getCurrentPlayerId());
        foreach ($cards as $card_id => $card) {
            $building = $this->getObjectFromDB("SELECT * FROM building WHERE card_id = {$card['id']}");
            $this->notify->player($this->getCurrentPlayerId(), "newbuilding", '', array(
                'card' => $building
            ));
        }
    }

    function debug_drawApprentice(int $number)
    {
        $cards = $this->apprentices->getCardsOfType($number);
        if (count($cards) == 0) throw new feException("Apprentice not found $number");
        $this->apprentices->insertCardOnExtremePosition(array_values($cards)[0]["id"], "deck", true);
        $cards = $this->apprentices->pickCardsForLocation(1, 'deck', 'cards' . $this->getCurrentPlayerId());
        foreach ($cards as $card_id => $card) {
            $apprentice = $this->getObjectFromDB("SELECT * FROM apprentice WHERE card_id = {$card['id']}");
            $this->notify->player($this->getCurrentPlayerId(), "newapprentice", '', array(
                'card' => $apprentice
            ));
        }
    }

    function debug_replaceCharacter(int $number)
    {
        $player_id = $this->getCurrentPlayerId();
        $this->DbQuery("update player set type = {$number} where player_id = {$player_id}");
    }
    function debug_gainResource(string $typestr, int $number = 5)
    {
        $player_id = $this->getCurrentPlayerId();
        if (is_numeric($typestr)) $type = (int) $typestr;
        else {
            $typestr = strtoupper($typestr);
            switch ($typestr) {
                case 'CLAY':
                    $type = CLAY;
                    break;
                case 'WOOD':
                    $type = WOOD;
                    break;
                case 'STONE':
                    $type = STONE;
                    break;
                case 'GOLD':
                    $type = GOLD;
                    break;
                case 'MARBLE':
                    $type = MARBLE;
                    break;
                case 'SILVER':
                    $type = SILVER;
                    break;
                case 'TAX':
                    $type = TAX;
                    break;
                case 'VIRTUE':
                    $type = VIRTUE;
                    break;
                case 'BUILDING':
                    $type = BUILDING;
                    break;
                case 'DEBT':
                    $type = DEBT;

                    break;
                default:
                    throw new feException("Invalid resource type $typestr");
            }
        }
        switch ($type) {
            case  DEBT;
                $obj = new ARCPlayer($player_id);
                $obj->pay("", "prison", $number * pow(10, $type), "prison");
                return;
        }
        $obj = new ARCPlayer($player_id);
        $obj->gainDirect($number, $type, "prison");
    }

    function debug_gainResourceAll(int $number = 5)
    {
        $player_id = $this->getCurrentPlayerId();
        $obj = new ARCPlayer($player_id);
        for ($type = 1; $type <= 10; $type++) {
            $obj->gainDirect($number, $type, "prison");
        }
    }

    function debugLog(string $message, array $args = [])
    {
        $this->notify->all("message", $message, $args);
    }


    //////////////////////////////////////////////////////////////////////////////
    //////////// Player actions
    //////////// 

    function actSelect($arg1, $arg2)
    {
        $this->checkAction('select');

        if ($this->gamestate->getCurrentMainState()['name'] == "playerDraft") {
            $player_id = $this->getCurrentPlayerId();
            $p = new ARCPlayer($player_id);
            $p->selectBuilding(null, null, $arg1, $arg2);

            ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                'id' => "res_" . $player_id . "_7",
                'nb' => $this->getUniqueValueFromDB("select count(*) from building where card_location = 'hand{$player_id}'")
            ));

            $this->giveExtraTime($this->getCurrentPlayerId());
            $this->gamestate->setPlayerNonMultiactive($this->getCurrentPlayerId(), 'next');
        } else {
            $this->checkArgs($arg1, $arg2);
            if ($arg1 == "Undo") {
                $this->undoRestorePoint();
                $this->gamestate->nextState('next');
                return;
            }

            $pending =  $this->getObjectFromDB("SELECT* FROM pending order by id desc limit 1");
            $this->callPending($pending, true, $arg1, $arg2);
            $this->DbQuery("delete from pending where id=" . $pending['id']);

            $this->giveExtraTime($this->getActivePlayerId());
            $this->gamestate->nextState('next');
        }
    }


    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state arguments
    ////////////

    function argPlayerTurn()
    {
        $pending =  $this->getObjectFromDB("SELECT* FROM pending order by id desc limit 1");
        $arg = $this->callPending($pending, false);

        return $arg;
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state actions
    ////////////

    function stSetup()
    {
        $players = $this->getCollectionFromDb("select * from player order by player_no desc");

        $sql = "INSERT INTO worker (player_id, location) VALUES ";
        $values = array();
        foreach ($players as $player_id => $player) {
            for ($i = 0; $i < 20; $i++) {
                $values[] = "('" . $player['player_id'] . "', 'reserve_{$player['player_id']}')";
            }
        }
        $sql .= implode(',', $values);
        $this->DbQuery($sql);

        //Apprentices
        $cards = array();
        for ($i = 1; $i <= 43; $i++) {
            $cards[] = array('type' => $i, 'type_arg' => 0, 'nbr' => 1);
        }
        $this->apprentices->createCards($cards, 'deck');
        $this->apprentices->shuffle('deck');
        for ($i = 1; $i <= 8; $i++) {
            $this->apprentices->pickCardForLocation('deck', 'phapprentice' . $i);
        }

        //Buildings
        $cards = array();
        for ($i = 1; $i <= 43; $i++) {
            $cards[] = array('type' => $i, 'type_arg' => 0, 'nbr' => 1);
        }
        $this->buildings->createCards($cards, 'deck');
        $this->buildings->shuffle('deck');

        foreach ($players as $player_id => $player) {
            $this->buildings->pickCardsForLocation(4, 'deck', 'selectCards' . $player['player_id']);
        }

        //Black market
        $cards = array();
        for ($i = 1; $i <= 2; $i++) {
            $cards[] = array('type' => $i, 'type_arg' => 0, 'nbr' => 1);
        }
        $this->blackmarkets->createCards($cards, 'deck');
        $this->blackmarkets->shuffle('deck');

        $cards = array();
        for ($i = 2; $i <= 12; $i++) {
            $cards[] = array('type' => $i, 'type_arg' => 0, 'nbr' => 1);
        }
        $this->rewards->createCards($cards, 'decktmp');
        $this->rewards->shuffle('decktmp');
        $nb = count($players) * 2 + 1;
        $this->rewards->pickCardsForLocation($nb, 'decktmp', 'deck');

        ArchitectsOfTheWestKingdom::$instance->addPending("NULL", "intialCards");
        ArchitectsOfTheWestKingdom::$instance->addPending("NULL", "refillApprentices");

        if ($this->getGameStateValue('board_side') == 1) {
            $players = $this->getCollectionFromDb("select * from player order by player_no asc");
            $this->DbQuery("update player set res6 = player_no+2, virtue = 7");
            foreach ($players as $player_id => $player) {
                $this->addPendingFirst($player['player_id'], "noworker");
                $this->addPending($player['player_id'], "pickApprentice", "norefill");
            }
        } else {
            foreach ($players as $player_id => $player) {
                $obj = new ARCPlayer($player['player_id']);
                $obj->gain(null, null, $this->asymetricStart[$player['type']]["gain"]);
                $obj->gain(null, null, $this->asymetricStart[$player['type']]["gain2"]);
                $obj->pay(null, null, $this->asymetricStart[$player['type']]["pay"]);

                $nbworkers = $this->asymetricStart[$player['type']]["prisoners"];

                if ($nbworkers > 0) {
                    $workers = $this->getCollectionFromDb("select * from worker where player_id = {$player['player_id']} limit {$nbworkers}");
                    $target = "prison";
                    foreach ($workers as $worker) {
                        ArchitectsOfTheWestKingdom::$instance->notify->all("move", '', array(
                            'mobile' => "worker_" . $worker['id'],
                            'parent' => "{$target}",
                            'position' => 'last'
                        ));
                        $this->DbQuery("update worker set location = '{$target}' where id = {$worker['id']}");
                    }
                    $nbmeeplesLeft = 20 - $nbworkers;
                    ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                        'id' => "res_" . $player['player_id'] . "_8",
                        'nb' => $nbmeeplesLeft
                    ));
                }
            }

            $player_no = $this->getUniqueValueFromDB("select player_no from player order by virtue desc limit 1");
            for ($i = 0; $i < count($players); $i++) {
                $p = $player_no + $i;

                if ($p > count($players)) {
                    $p -= count($players);
                }

                $player = $this->getObjectFromDB("SELECT * FROM player WHERE player_no = {$p}");
                $this->addPendingFirst($player['player_id'], "noworker");
                $this->addPending($player['player_id'], "pickApprentice", "norefill");
            }
        }
        $this->gamestate->nextState('next');
    }


    function intialCards($parg1, $parg2, $varg1, $varg2)
    {

        if ($this->getGameStateValue('board_side') != 1) {
            $players = $this->getCollectionFromDb("select * from player order by player_no desc");
            foreach ($players as $player_id => $player) {
                $obj = new ARCPlayer($player['player_id']);
                $obj->gain(null, null, B * $this->asymetricStart[$player['type']]["cards"]);
            }
        }
    }

    function refillApprentices($parg1, $parg2, $varg1, $varg2)
    {

        for ($newloc = 1; $newloc <= 8; $newloc++) {
            $nb = $this->getUniqueValueFromDB("select count(*) from apprentice where card_location = 'phapprentice{$newloc}'");
            if ($nb == 0) {
                ArchitectsOfTheWestKingdom::$instance->apprentices->pickCardForLocation('deck', 'phapprentice' . $newloc);
                ArchitectsOfTheWestKingdom::$instance->setGameStateValue('no_undo', 1);
                $apprentice = $this->getObjectFromDB("SELECT * FROM apprentice WHERE card_location = 'phapprentice{$newloc}' ");

                ArchitectsOfTheWestKingdom::$instance->notify->all("newapprentice", '', array(
                    'card' => $apprentice
                ));
            }
        }

        $this->gamestate->setAllPlayersMultiactive();
        $this->gamestate->nextState('draft');
    }

    function argDraft()
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $sql = "SELECT * from building where card_location like 'selectCards%'";
        $buildings = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);
        $ret['selectCards'] = $buildings;
        foreach ($buildings as $building) {
            $ret['selectable']["building" . $building['card_id']] = array();
        }

        return $ret;
    }

    function stDraft()
    {
        $players = $this->getCollectionFromDb("select * from player order by player_no desc");
        $nbcards = $this->getUniqueValueFromDB("select count(*) from building where card_location like 'hand%'");

        if ($nbcards > count($players) * 2) {

            $sql = "SELECT * from building where card_location like 'selectCards%'";
            $buildings = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);
            foreach ($buildings as $building) {
                $buildingId = $building['card_id'];
                ArchitectsOfTheWestKingdom::$instance->buildings->insertCardOnExtremePosition($buildingId, "deck", false);
            }


            ArchitectsOfTheWestKingdom::$instance->addPending("NULL", "intialResources");

            $this->gamestate->nextState('next');
        } else {
            $players = $this->loadPlayersBasicInfos();
            if (count($players) > 1) {
                $nextPlayer = $this->createNextPlayerTable(array_keys($players));

                $sql = "SELECT * from building where card_location like 'selectCards%'";
                $buildings = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);
                foreach ($buildings as $building) {
                    $currentplayer_id = (int) filter_var($building['card_location'], FILTER_SANITIZE_NUMBER_INT);
                    $nextplayer_id = $nextPlayer[$currentplayer_id];
                    $this->DbQuery("update building set card_location = 'selectCards{$nextplayer_id}' where card_id = {$building['card_id']}");
                }
            }

            $this->gamestate->setAllPlayersMultiactive();
            $this->gamestate->nextState('draft');
        }
    }

    function stPending()
    {

        $pending =  $this->getObjectFromDB("SELECT* FROM pending order by id desc limit 1");
        if ($pending == null) {
            //final bonus

            $score = array();
            $players = $this->getCollectionFromDb("select * from player order by player_no desc");
            foreach ($players as $player_id => $player) {
                $obj = new ARCPlayer($player['player_id']);
                $obj->instantFinal();
                $score[] = $obj->updateVP(true);
            }

            for ($i = 0; $i < count($score); $i++) {
                for ($j = 0; $j < count($score[$i]); $j++) {
                    ArchitectsOfTheWestKingdom::$instance->notify->all("finalscore", '', array(
                        'i' => $i,
                        'j' => $j,
                        'score' => $score[$i][$j]
                    ));
                }
            }
            $this->setGameStateValue('finish', 1);
            $this->gamestate->nextState('end');
        } else {
            $args = $this->callPending($pending, false);
            if ($args == null || count($args['selectable']) == 0) {
                //no args required, execute
                $this->callPending($pending, true);
                $this->DbQuery("delete from pending where id=" . $pending['id']);
                $this->gamestate->nextState('same');
            } else if (count($args['selectable']) == 1 && !array_key_exists('Pass', $args['selectable']) && !array_key_exists('Undo', $args['selectable'])) {
                //AUTO PLAY IF ONLY ONE CHOICE
                foreach ($args['selectable'] as $arg1 => $argnul) {
                    $this->callPending($pending, true, $arg1);
                }
                $this->DbQuery("delete from pending where id=" . $pending['id']);
                $this->gamestate->nextState('same');
            } else {

                $this->gamestate->changeActivePlayer($pending['player_id']);
                if ($pending["function"] == "actionRound") {
                    ArchitectsOfTheWestKingdom::$instance->setGameStateValue('no_undo', 0);
                    $this->undoSavepoint();
                }

                //player input required
                $this->gamestate->nextState('player');
            }
        }
    }


    //////////////////////////////////////////////////////////////////////////////
    //////////// Zombie
    ////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn($state, $active_player)
    {
        $statename = $state['name'];

        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $player_id = $this->getActivePlayerId();
                    $this->DbQuery("delete from pending where player_id = {$active_player}");
                    $this->gamestate->nextState("zombiePass");
                    break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive($active_player, '');

            return;
        }

        throw new feException("Zombie mode not supported at this game state: " . $statename);
    }

    ///////////////////////////////////////////////////////////////////////////////////:
    ////////// DB upgrade
    //////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */

    function upgradeTableDb($from_version)
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345

        // Example:
        //        if( $from_version <= 1404301345 )
        //        {
        //            // ! important ! Use DBPREFIX_<table_name> for all tables
        //
        //            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
        //            $this->applyDbUpgradeToAllDB( $sql );
        //        }
        //        if( $from_version <= 1405061421 )
        //        {
        //            // ! important ! Use DBPREFIX_<table_name> for all tables
        //
        //            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
        //            $this->applyDbUpgradeToAllDB( $sql );
        //        }
        //        // Please add your future database scheme changes here
        //
        //



        if ($from_version <= 2303031527) {
            // ! important ! Use DBPREFIX_<table_name> for all tables
            $sql = "ALTER TABLE DBPREFIX_pending ADD `arg4` varchar(50) NULL";
            $this->applyDbUpgradeToAllDB($sql);
        }
    }
}
