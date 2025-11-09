<?php


class ARCPlayer
{
    public $player_id;
    public $player_no;
    public $player_name;
    public $player_score;
    public $type;
    public $virtue;
    public $cathedral;
    public $resources;

    /**
     * Constructor - initializes player object with data from database
     * @param int $player_id Player ID
     */
    public function __construct($player_id)
    {
        $this->player_id = $player_id;
        $p = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM player WHERE player_id = {$player_id}");
        $this->player_no = $p['player_no'];
        $this->player_id = $p['player_id'];
        $this->player_name = $p['player_name'];
        $this->player_score = $p['player_score'];
        $this->type = $p['type'];
        $this->virtue = $p['virtue'];
        $this->cathedral = $p['cathedral'];
        $this->resources = array();
        for ($i = 1; $i <= 6; $i++) {
            $this->resources[$i] = $p['res' . $i];;
        }
    }


    /**
     * Prepares confirmation dialog arguments with warning if no workers left
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     */
    function argconfirmation($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must confirm their action');
        $ret['titleyou'] = clienttranslate('${you} must confirm your action');

        $workerleft = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location = 'reserve_{$this->player_id}'");

        if ($workerleft == 0) {
            $ret['titleyou'] = clienttranslate('<i class="fa fa-exclamation-triangle"></i>&nbsp;WARNING : You do not have any worker left&nbsp;<i class="fa fa-exclamation-triangle"></i>');
        }

        $ret['buttons'][] = 'Confirm';
        $ret['selectable']['Confirm'] = array();

        if ($this->isUndoAvailable()) {
            $ret['buttons'][] = 'Undo';
            $ret['selectable']['Undo'] = array();
        }
        return $ret;
    }

    function isUndoAvailable()
    {
        return ArchitectsOfTheWestKingdom::$instance->getGameStateValue('no_undo') == 0;
    }

    /**
     * Handles confirmation action (empty implementation)
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     * @param mixed $varg1 Value argument 1
     * @param mixed $varg2 Value argument 2
     */
    function confirmation($parg1, $parg2, $varg1, $varg2) {}

    /**
     * Prepares available actions for the action round phase
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     */
    function argactionRound($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['unselectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must play a worker');
        $ret['titleyou'] = clienttranslate('${you} must play a worker');

        foreach (['quarry', 'forest', 'silversmith', 'mines1', 'storehouse', "workshop1", "workshop2"] as $action) {
            $index = (int) filter_var($action, FILTER_SANITIZE_NUMBER_INT);
            $act = str_replace($index, "", $action);
            if ($this->checkCost($this->getCost($act, $index))) {
                $ret['selectable']["act" . $action] = array();
            }
        }

        $argguildhall = $this->argguildhall($parg1, $parg2);
        if (!$argguildhall['void']) {
            $ret['selectable']["actguildhall"] = $argguildhall;
        } else {
            $ret['unselectable']["actguildhall"] = $argguildhall;
        }


        if ($this->checkCost($this->getCost("towncenter", 1))) {
            $ret['selectable']["acttowncenter"] = array();
        } else {
            $ret['unselectable']["acttowncenter"] = clienttranslate('Not enough coins');
        }

        if (ArchitectsOfTheWestKingdom::$instance->getGameStateValue('tax') > 0) {
            $ret['selectable']["acttaxstand"] = array();
        } else {
            $ret['unselectable']["acttaxstand"] = clienttranslate('No tax coins to collect');
        }

        if (ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location = 'prison_{$this->player_id}'") > 0) {
            $ret['selectable']["actguardhouse1"] = array();
        } else {
            $ret['unselectable']["actguardhouse1"] = clienttranslate('No workers in your dungeon');
        }

        if (ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location = 'prison' and player_id={$this->player_id}") > 0) {
            $ret['selectable']["actguardhouse2"] = array();
        } else {
            $ret['unselectable']["actguardhouse2"] = clienttranslate('No workers in the prison');
        }

        if (ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location like 'prison_%' and player_id={$this->player_id}") > 0) {
            $ret['selectable']["actguardhouse3"] = array();
        } else {
            $ret['unselectable']["actguardhouse3"] = clienttranslate('No your workers in other players\' dungeons');
        }

        if (ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from debt where paid = 0 and player_id={$this->player_id}") > 0) {
            if ($this->checkCost($this->getCost("guardhouse", 4))) {
                $ret['selectable']["actguardhouse4"] = array();
            } else {
                $ret['unselectable']["actguardhouse4"] = clienttranslate('Not enough resources to pay off debt');
            }
        } else {
            $ret['unselectable']["actguardhouse4"] = clienttranslate('No outstanding debts');
        }

        if (ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location='mines' and player_id={$this->player_id}") > 0) {
            $ret['selectable']["actmines2"] = array();
        }

        if ($this->virtue < 10) {
            if (ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location='blackmarketa'") == 0 && $this->checkCost($this->getCost("blackmarketa"))) {
                $ret['selectable']["actblackmarketa"] = array();
            }
            if (ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location='blackmarketb'") == 0 && $this->checkCost($this->getCost("blackmarketb"))) {
                $ret['selectable']["actblackmarketb1"] = array();
                $ret['selectable']["actblackmarketb2"] = array();
            }
            if (ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location='blackmarketc'") == 0 && $this->checkCost($this->getCost("blackmarketc"))) {
                $ret['selectable']["actblackmarketc"] = array();
            }
        } else {
            $ret['unselectable']["actblackmarketa"] = clienttranslate('Virtue too high to access the Black Market');
            $ret['unselectable']["actblackmarketb1"] = clienttranslate('Virtue too high to access the Black Market');
            $ret['unselectable']["actblackmarketb2"] = clienttranslate('Virtue too high to access the Black Market');
            $ret['unselectable']["actblackmarketc"] = clienttranslate('Virtue too high to access the Black Market');
        }

        return $ret;
    }

    /**
     * Executes the selected action during action round
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     * @param string $varg1 Selected action
     * @param mixed $varg2 Value argument 2
     */
    function actionRound($parg1, $parg2, $varg1, $varg2)
    {
        ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "confirmation");
        ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "discardBuilding");

        $index = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
        $action = str_replace($index, "", substr($varg1, 3));

        $worker_id = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select min(id) from worker where player_id = {$this->player_id} and location like 'reserve%'");
        ArchitectsOfTheWestKingdom::$instance->DbQuery("update worker set location = '{$action}', location_arg=0 where id = {$worker_id}");

        $worker = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM worker WHERE id = {$worker_id}");
        ArchitectsOfTheWestKingdom::$instance->notify->all("move", '', array(
            'mobile' => "worker_" . $worker['id'],
            'parent' => $worker['location'],
            'position' => 'last'
        ));

        $nbmeeplesLeft = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where player_id = {$this->player_id} and  location like 'reserve%'");
        ArchitectsOfTheWestKingdom::$instance->notify->all("counter", clienttranslate('${player_name} places a worker on ${location}'), array(
            'player_id' => $this->player_id,
            'player_name' => $this->player_name,
            'id' => "res_" . $this->player_id . "_8",
            'nb' => $nbmeeplesLeft,
            'location' => $action
        ));

        $nbmeeplesOnLocation = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*)  from worker where player_id = {$this->player_id} and location = '{$action}'");

        $this->pay(null, $varg1, $this->getCost($action, $index));

        switch ($action) {
            case "guardhouse":
                ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "addguardhouse", $nbmeeplesOnLocation - 1);
                switch ($index) {
                    case 1:
                        $this->guardhouse1();
                        break;
                    case 2:
                        $this->guardhouse2();
                        break;
                    case 3:
                        ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "guardhouse3");
                        break;
                    case 4:
                        $this->debtRefund();
                        break;
                }
                break;
            case "workshop":
                if ($index == 1) {
                    ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "workshop", min($nbmeeplesOnLocation, 4));
                    ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "discardApprentice");
                } else {
                    $nbcards = intdiv($nbmeeplesOnLocation, 2) + 1;
                    $cards = ArchitectsOfTheWestKingdom::$instance->buildings->pickCardsForLocation($nbcards, 'deck', 'hand' . $this->player_id);
                    foreach ($cards as $card_id => $card) {
                        $building = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM building WHERE card_id = {$card['id']}");
                        ArchitectsOfTheWestKingdom::$instance->notify->player($this->player_id, "newbuilding", '', array(
                            'card' => $building
                        ));
                    }
                    ArchitectsOfTheWestKingdom::$instance->notify->all("counter", clienttranslate('${player_name} gains ${nbdiff} <div class="arcicon res9"></div>'), array(
                        'player_id' => $this->player_id,
                        'player_name' => $this->player_name,
                        'id' => "res_" . $this->player_id . "_7",
                        'nb' => ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from building where card_location = 'hand{$this->player_id}'"),
                        'nbdiff' => $nbcards
                    ));
                    ArchitectsOfTheWestKingdom::$instance->setGameStateValue('no_undo', 1);
                }
                break;
            case "storehouse":
                if (ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*)  from apprentice where card_location = 'cards{$this->player_id}' and card_type = 42") > 0) {
                    $nbmeeplesOnLocation++;
                }

                ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "storehouse", $nbmeeplesOnLocation);
                break;
            case "mines":
                $bonus = $this->getAdditionalBonus("mines");
                $this->gain($varg1, null, $bonus);
                if ($index == 1) {
                    $this->gainDirect($nbmeeplesOnLocation + 1, CLAY, $varg1);
                } else {
                    $this->gainDirect(intdiv($nbmeeplesOnLocation, 2), GOLD, $varg1);
                }
                break;
            case "quarry":
                $bonus = $this->getAdditionalBonus("quarry");
                $this->gainDirect($nbmeeplesOnLocation + $bonus, STONE, $varg1);
                break;
            case "taxstand":
                $tax = ArchitectsOfTheWestKingdom::$instance->getGameStateValue('tax');
                ArchitectsOfTheWestKingdom::$instance->setGameStateValue('tax', 0);
                ArchitectsOfTheWestKingdom::$instance->notify->all("counterid", '', array(
                    'id' => "taxcpt",
                    'nb' => 0
                ));


                $bonus = $this->getAdditionalBonus("taxstand");
                $this->gainDirect($tax, SILVER, "taxcoin");
                if ($bonus > 0) {
                    $this->gainDirect($bonus, GOLD, "taxcoin");
                }

                break;
            case "forest":
                $bonus = $this->getAdditionalBonus($action);
                $this->gainDirect($nbmeeplesOnLocation + $bonus, WOOD, $varg1);
                break;
            case "silversmith":
                $bonus = $this->getAdditionalBonus($action);
                $this->gainDirect($nbmeeplesOnLocation + 1 + $bonus, SILVER, $varg1);
                break;
            case "towncenter":
                ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "towncenter", null, null, 0, $nbmeeplesOnLocation);
                break;
            case "guildhall":
                $this->guildhallSelect($worker_id);
                break;
            case "blackmarketa":
                $nbplayers = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location like 'blackmarket%'");
                if ($nbplayers == 3) {
                    ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "blackmarketreset");
                }
                $gain = M + G;
                if (ArchitectsOfTheWestKingdom::$instance->blackmarkets->getCardOnTop("deck") != null) {
                    $gain = ArchitectsOfTheWestKingdom::$instance->blackmarket1[ArchitectsOfTheWestKingdom::$instance->blackmarkets->getCardOnTop("deck")['type']];
                }
                $bonus = $this->getAdditionalBonus($action);
                $this->gain($varg1, null, $gain + $bonus);

                if ($this->type == 3) {
                    $this->gain(null, null, B);
                }

                break;
            case "blackmarketb":
                $nbplayers = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location like 'blackmarket%'");
                if ($nbplayers == 3) {
                    ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "blackmarketreset");
                }
                if ($index == 1) {
                    ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "pickApprentice");
                    ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "discardApprentice");
                } else {
                    ArchitectsOfTheWestKingdom::$instance->setGameStateValue('no_undo', 1);
                    ArchitectsOfTheWestKingdom::$instance->buildings->pickCardsForLocation(5, 'deck', "selectCards{$this->player_id}");
                    ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "selectBuilding", "flush");
                }
                $bonus = $this->getAdditionalBonus($action);
                $this->gain($varg1, null, $bonus);
                if ($this->type == 3) {
                    $this->gain(null, null, B);
                }
                break;
            case "blackmarketc":
                $nbplayers = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location like 'blackmarket%'");
                if ($nbplayers == 3) {
                    ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "blackmarketreset");
                }

                $gain = 2 * M + S + W;
                if (ArchitectsOfTheWestKingdom::$instance->blackmarkets->getCardOnTop("discard") != null) {
                    $gain = ArchitectsOfTheWestKingdom::$instance->blackmarket2[ArchitectsOfTheWestKingdom::$instance->blackmarkets->getCardOnTop("discard")['type']];
                }
                $bonus = $this->getAdditionalBonus($action);
                $this->gain($varg1, null, $gain + $bonus);
                if ($this->type == 3) {
                    $this->gain(null, null, B);
                }
                break;
        }
    }

    /**
     * Handles worker placement at guildhall and triggers special events
     * @param int $worker_id Worker ID to place
     */
    function guildhallSelect($worker_id)
    {
        $action = "guildhall";
        $nbplayers = max(2, ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from player"));
        $nbmeeplesOnLocation = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*)  from worker where location = 'guildhall'");
        $nbres1 = $nbplayers * 2 + 3;
        $nbres2 = $nbplayers * 3 + 4;

        $nbresApp1 = 3;
        $nbresApp2 = $nbplayers + 1 + 3;
        $nbresApp3 = ($nbplayers + 1) * 2 + 3;

        $nbmax = ($nbplayers + 1) * 4;

        ArchitectsOfTheWestKingdom::$instance->DbQuery("update worker set location = '{$action}', location_arg={$nbmeeplesOnLocation} where id = {$worker_id}");

        if ($nbmeeplesOnLocation == $nbresApp1 || $nbmeeplesOnLocation == $nbresApp2 || $nbmeeplesOnLocation == $nbresApp3) {
            $apprentice = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM apprentice WHERE card_location = 'phapprentice1'");
            ArchitectsOfTheWestKingdom::$instance->apprentices->insertCardOnExtremePosition($apprentice['card_id'], "deck", false);
            ArchitectsOfTheWestKingdom::$instance->notify->all("remove", '', array(
                'id' => "apprentice" . $apprentice['card_id']
            ));

            $apprentice = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM apprentice WHERE card_location = 'phapprentice2'");
            ArchitectsOfTheWestKingdom::$instance->apprentices->insertCardOnExtremePosition($apprentice['card_id'], "deck", false);
            ArchitectsOfTheWestKingdom::$instance->notify->all("remove", '', array(
                'id' => "apprentice" . $apprentice['card_id']
            ));

            for ($i = 3; $i <= 8; $i++) {
                $apprentice = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM apprentice WHERE card_location = 'phapprentice{$i}'");
                $newapprenticeIndex = intval($i) - 2;

                ArchitectsOfTheWestKingdom::$instance->DbQuery("update apprentice set card_location = 'phapprentice{$newapprenticeIndex}' where card_id = {$apprentice['card_id']}");

                ArchitectsOfTheWestKingdom::$instance->notify->all("move", '', array(
                    'mobile' => "apprentice" . $apprentice['card_id'],
                    'parent' => "phapprentice{$newapprenticeIndex}",
                    'position' => 'last'
                ));
            }

            $newloc = 7;
            ArchitectsOfTheWestKingdom::$instance->apprentices->pickCardForLocation('deck', 'phapprentice' . $newloc);
            ArchitectsOfTheWestKingdom::$instance->setGameStateValue('no_undo', 1);
            $apprentice = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM apprentice WHERE card_location = 'phapprentice{$newloc}' ");
            ArchitectsOfTheWestKingdom::$instance->notify->all("newapprentice", '', array(
                'card' => $apprentice
            ));

            $newloc = 8;
            ArchitectsOfTheWestKingdom::$instance->apprentices->pickCardForLocation('deck', 'phapprentice' . $newloc);
            ArchitectsOfTheWestKingdom::$instance->setGameStateValue('no_undo', 1);
            $apprentice = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM apprentice WHERE card_location = 'phapprentice{$newloc}' ");
            ArchitectsOfTheWestKingdom::$instance->notify->all("newapprentice", '', array(
                'card' => $apprentice
            ));
        } else if ($nbmeeplesOnLocation == $nbres1 || $nbmeeplesOnLocation == $nbres2) {
            ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "blackmarketreset");
        } else if ($nbmeeplesOnLocation == $nbmax) {
            ArchitectsOfTheWestKingdom::$instance->notify->all("popup", clienttranslate('<b>END OF THE GAME : </b> each player (including the current player) has 1 final turn before the game ends'), array(
                "msg" => clienttranslate("<b>END OF THE GAME</b>")
            ));
        }
        ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "guildhall");
    }

    /**
     * Prepares additional guardhouse action options
     * @param int $parg1 Number of actions left
     * @param mixed $parg2 Parameter 2
     */
    function argaddguardhouse($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may use additional guardhouse actions (#nb# left)');
        $ret['titleyou'] = clienttranslate('${you} may use additional guardhouse actions (#nb# left)');
        $ret['nb'] = $parg1;

        $number = (int) $parg1;

        if ($number > 0) {
            if (ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location = 'prison_{$this->player_id}'") > 0) {
                $ret['selectable']["actguardhouse1"] = array();
            }

            if (ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location = 'prison' and player_id={$this->player_id}") > 0) {
                $ret['selectable']["actguardhouse2"] = array();
            }

            if (ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location like 'prison_%' and player_id={$this->player_id}") > 0) {
                $ret['selectable']["actguardhouse3"] = array();
            }

            if ($this->checkCost($this->getCost("guardhouse", 4)) && ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from debt where paid = 0 and player_id={$this->player_id}") > 0) {
                $ret['selectable']["actguardhouse4"] = array();
            }
        }
        $ret['buttons'][] = 'Skip';
        $ret['selectable']['Skip'] = array();

        if ($this->isUndoAvailable()) {
            $ret['buttons'][] = 'Undo';
            $ret['selectable']['Undo'] = array();
        }

        return $ret;
    }

    /**
     * Executes additional guardhouse actions
     * @param int $parg1 Number of actions left
     * @param mixed $parg2 Parameter 2
     * @param string $varg1 Selected action
     * @param mixed $varg2 Value argument 2
     */
    function addguardhouse($parg1, $parg2, $varg1, $varg2)
    {
        if ($varg1 != "Skip") {
            $index = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
            ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "addguardhouse", intval($parg1) - 1);
            switch ($index) {
                case 1:
                    $this->guardhouse1();
                    break;
                case 2:
                    $this->guardhouse2();
                    break;
                case 3:
                    ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "guardhouse3");
                    break;
                case 4:
                    $this->pay(null, $varg1, $this->getCost("guardhouse", 4));
                    $this->debtRefund();
                    break;
            }
        }
    }

    /**
     * Prepares options for guardhouse action 3 (release workers from other boards)
     */
    function argguardhouse3()
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may release their workers from other players\' Boards');
        $ret['titleyou'] = clienttranslate('${you} may release your workers from other players\' Boards');

        $cost = $this->getCost("guardhouseb");
        if ($this->checkCost($cost)) {
            $ret['buttons'][] = 'res' . $cost;
            $ret['selectable']['res' . $cost] = array();
        }
        $ret['buttons'][] = 'resgh3';
        $ret['selectable']['resgh3'] = array();

        $ret['buttons'][] = 'Skip';
        $ret['selectable']['Skip'] = array();

        if ($this->isUndoAvailable()) {
            $ret['buttons'][] = 'Undo';
            $ret['selectable']['Undo'] = array();
        }


        return $ret;
    }

    /**
     * Executes guardhouse action 3 - releases workers from other players' boards
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     * @param string $varg1 Payment choice
     * @param mixed $varg2 Value argument 2
     */
    function guardhouse3($parg1, $parg2, $varg1, $varg2)
    {
        if ($varg1 != "Skip") {
            if ($varg1 == 'nocost') {
            } else if ($varg1 == 'resgh3') {
                $this->pay(null, "actguardhouse3", V + D);
            } else {
                $this->pay(null, "actguardhouse3", $varg1);
            }

            $sql = "SELECT * from worker where location like 'prison_%' and player_id = {$this->player_id}";
            $workers = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

            $target = "reserve_{$this->player_id}";
            foreach ($workers as $worker) {
                ArchitectsOfTheWestKingdom::$instance->notify->all("move", '', array(
                    'mobile' => "worker_" . $worker['id'],
                    'parent' => "{$target}",
                    'position' => 'last'
                ));
            }
            ArchitectsOfTheWestKingdom::$instance->DbQuery("update worker set location = '{$target}', location_arg=0 where location like 'prison_%' and player_id = {$worker['player_id']}");

            ArchitectsOfTheWestKingdom::$instance->notify->all("note", clienttranslate('${player_name} releases their workers from prisons'), array(
                'player_id' => $this->player_id,
                'player_name' => $this->player_name
            ));

            $nbmeeplesLeft = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where player_id = {$this->player_id} and  location like 'reserve%'");
            ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                'id' => "res_" . $this->player_id . "_8",
                'nb' => $nbmeeplesLeft
            ));

            $players = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb("select * from player order by player_no desc");
            foreach ($players as $player) {
                ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                    'id' => "res_" . $player['player_id'] . "_12",
                    'nb' => ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("SELECT count(*) FROM worker WHERE location = 'prison_{$player['player_id']}'")
                ));
            }
        }
    }



    /**
     * Executes guardhouse action 1 - sends captured workers to prison and gains silver
     */
    function guardhouse1()
    {
        $this->getAdditionalBonus("guardhouse1");

        if ($this->type == 9) {
            $this->gain(null, null, 2 * SI);
        }

        $sql = "SELECT * from worker where location = 'prison_{$this->player_id}'";
        $prisonners = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);
        $this->gainDirect(count($prisonners), SILVER, "prison");
        $target = "prison";

        foreach ($prisonners as $worker) {
            ArchitectsOfTheWestKingdom::$instance->notify->all("move", '', array(
                'mobile' => "worker_" . $worker['id'],
                'parent' => "{$target}",
                'position' => 'last'
            ));
        }
        ArchitectsOfTheWestKingdom::$instance->DbQuery("update worker set location = '{$target}', location_arg=0 where location = 'prison_{$this->player_id}'");

        ArchitectsOfTheWestKingdom::$instance->notify->all("counter", clienttranslate('${player_name} sends captured workers to prison'), array(
            'id' => "res_" . $this->player_id . "_12",
            'nb' => 0,
            'player_id' => $this->player_id,
            'player_name' => $this->player_name
        ));

        $players = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb("select * from player where player_id <> {$this->player_id}");
        foreach ($players as $player) {
            $obj = new ARCPlayer($player['player_id']);
            $obj->updateVP();
        }
    }

    /**
     * Executes guardhouse action 2 - releases own workers from prison
     */
    function guardhouse2()
    {
        $sql = "SELECT * from worker where location = 'prison' and player_id = {$this->player_id}";
        $prisonners = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

        $target = "reserve_" . $this->player_id;

        foreach ($prisonners as $worker) {
            ArchitectsOfTheWestKingdom::$instance->notify->all("move", '', array(
                'mobile' => "worker_" . $worker['id'],
                'parent' => "{$target}",
                'position' => 'last'
            ));
        }
        ArchitectsOfTheWestKingdom::$instance->DbQuery("update worker set location = '{$target}', location_arg=0 where location = 'prison' and player_id = {$this->player_id}");

        $nbmeeplesLeft = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where player_id = {$this->player_id} and  location like 'reserve%'");
        ArchitectsOfTheWestKingdom::$instance->notify->all("counter", clienttranslate('${player_name} releases their workers from Prison'), array(
            'player_id' => $this->player_id,
            'player_name' => $this->player_name,
            'id' => "res_" . $this->player_id . "_8",
            'nb' => $nbmeeplesLeft
        ));

        $this->updateVP();
    }

    /**
     * Handles black market reset - moves workers to prison, flips cards, applies penalties
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     * @param mixed $varg1 Value argument 1
     * @param mixed $varg2 Value argument 2
     */
    function blackmarketreset($parg1, $parg2, $varg1, $varg2)
    {
        ArchitectsOfTheWestKingdom::$instance->notify->all("popup", clienttranslate('<b>Black Market Reset</b>'), array(
            'msg' => clienttranslate('<b>Black Market Reset</b>')
        ));

        //Step 1 - send all worker from blackmarket to prison
        $sql = "SELECT * from worker where location like 'blackmarket%'";
        $workers = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

        $target = "prison";
        foreach ($workers as $worker) {
            ArchitectsOfTheWestKingdom::$instance->notify->all("move", '', array(
                'mobile' => "worker_" . $worker['id'],
                'parent' => "{$target}",
                'position' => 'last'
            ));
        }
        ArchitectsOfTheWestKingdom::$instance->DbQuery("update worker set location = '{$target}', location_arg=0 where location like 'blackmarket%'");

        //Step 2 - draw new market cards
        $card = ArchitectsOfTheWestKingdom::$instance->blackmarkets->pickCardForLocation('deck', 'discard');
        ArchitectsOfTheWestKingdom::$instance->blackmarkets->playCard($card['id']);
        ArchitectsOfTheWestKingdom::$instance->setGameStateValue('no_undo', 1);


        ArchitectsOfTheWestKingdom::$instance->notify->all("blackmarket", '', array(
            'blackmarket1' => ArchitectsOfTheWestKingdom::$instance->blackmarkets->getCardOnTop("deck"),
            'blackmarket2' => ArchitectsOfTheWestKingdom::$instance->blackmarkets->getCardOnTop("discard")
        ));

        //Step 3 activate blackmarketreset abilities
        $arr = array();
        $players = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb("select * from player order by player_no desc");
        foreach ($players as $player) {
            $obj = new ARCPlayer($player['player_id']);
            $obj->addPending("blackmarketreset");
            if ($obj->type == 7) {
                //ArchitectsOfTheWestKingdom::$instance->addPending($player['player_id'], "fara");
                $this->fara(null, null, "Confirm", null);
            }
        }

        //Step 4
        $max = 0;
        $arr = array();
        $arrnames = array();
        foreach ($players as $player) {
            $nbmeeplesLeft = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where player_id = {$player['player_id']} and  location = 'prison'");
            if ($nbmeeplesLeft >= 3) {
                $obj = new ARCPlayer($player['player_id']);
                $obj->pay(null, "prison", V);
            }
            if ($nbmeeplesLeft > $max) {
                $max = $nbmeeplesLeft;
                $arr = array();
                $arrnames = array();
            }
            if ($nbmeeplesLeft == $max) {
                $arr[] = $player;
                $arrnames[] = $player['player_name'];
            }
        }

        if ($max > 0) {
            ArchitectsOfTheWestKingdom::$instance->notify->all("msg", clienttranslate('Most workers in prison : ${max}'), array(
                'max' => implode(',', $arrnames)
            ));


            foreach ($arr as $player) {
                $obj = new ARCPlayer($player['player_id']);
                $obj->pay(null, "prison", D);
            }
        }
    }

    /**
     * Pays off one debt and gains virtue bonus
     */
    function debtRefund()
    {
        $debt = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM debt WHERE player_id = {$this->player_id} and paid = 0 limit 1");
        if ($debt != null) {
            ArchitectsOfTheWestKingdom::$instance->DbQuery("update debt set paid = 1 where id = {$debt['id']}");
            ArchitectsOfTheWestKingdom::$instance->notify->all("counter", clienttranslate('${player_name} pays off one debt'), array(
                'player_id' => $this->player_id,
                'player_name' => $this->player_name,
                'id' => "res_" . $this->player_id . "_13",
                'nb' => ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("SELECT count(*) FROM debt WHERE player_id = {$this->player_id} and paid = 0")
            ));

            ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                'id' => "res_" . $this->player_id . "_14",
                'nb' => ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("SELECT count(*) FROM debt WHERE player_id = {$this->player_id} and paid = 1")
            ));


            $bonus = $this->getAdditionalBonus("debtrefund");
            $this->gain("res_" . $this->player_id . "_13", null, V + $bonus);
        }
    }


    /**
     * Prepares guildhall options - building construction or cathedral work
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     */
    function argguildhall($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['unselectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may construct a Building or work on the Cathedral');
        $ret['titleyou'] = clienttranslate('${you} may construct a Building or work on the Cathedral');

        $sql = "SELECT * from building where card_location = 'hand{$this->player_id}'";
        $buildings = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);
        $appreq = $this->getAppRequirement();

        foreach ($buildings as $building) {
            $objbuilding = building::instantiate($building);
            if (($appreq & $objbuilding->requirement) == $objbuilding->requirement) {
                if ($this->type == 0) {
                    if ($this->checkCost($this->minusCost($objbuilding->cost, W)) || $this->checkCost($this->minusCost($objbuilding->cost, S))) {
                        $ret['selectable']["building" . $building['card_id']] = array();
                    } else {
                        $ret['unselectable']["building" . $building['card_id']] = clienttranslate('Not enough resources');
                    }
                } else if ($this->checkCost($objbuilding->cost)) {
                    $ret['selectable']["building" . $building['card_id']] = array();
                } else {
                    $ret['unselectable']["building" . $building['card_id']] = clienttranslate('Not enough resources');
                }
            } else {
                $ret['unselectable']["building" . $building['card_id']] = clienttranslate('Not enough skills');
            }
        }
        if ($this->cathedral >= 5) {
            $ret['unselectable']['actcathedral'] = clienttranslate('Cathedral is already completed');
        } else   if ($this->virtue <= 4) {
            $ret['unselectable']['actcathedral'] = clienttranslate('Virtue is too low to work on the Cathedral');
        } else {
            $spotFilled = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from player where cathedral = 1+ {$this->cathedral}");
            $spotstot = ArchitectsOfTheWestKingdom::$instance->cathedralSpots[$this->cathedral + 1];

            if (count($buildings) == 0 && $this->type != 6) {
                $ret['unselectable']['actcathedral'] = clienttranslate('You don\'t have a building to discard to work on cathedral');
            } else  if ($spotFilled >= $spotstot) {
                $ret['unselectable']['actcathedral'] = clienttranslate('All spots in Cathedral are filled');
            } else {
                $costs = ArchitectsOfTheWestKingdom::$instance->cathedralCosts[$this->cathedral + 1];
                if ($this->type == 0) {
                    $reducs = array();
                    foreach ($costs as $cost) {
                        $reducs[] = $this->minusCost($cost, W);
                        $reducs[] = $this->minusCost($cost, S);
                    }
                    $costs = array_unique($reducs);
                }
                if (count($this->filterCosts($costs)) > 0) {
                    $ret['selectable']['actcathedral'] = array();
                } else {
                    $ret['unselectable']['actcathedral'] = clienttranslate('Not enough resources');
                }
            }
        }

        $ret['void'] = count($ret['selectable']) == 0;

        //undo is always available here

        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();




        return $ret;
    }

    /**
     * Executes guildhall action - constructs building or works on cathedral
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     * @param string $varg1 Selected building or cathedral
     * @param mixed $varg2 Value argument 2
     */
    function guildhall($parg1, $parg2, $varg1, $varg2)
    {
        if ($varg1 != "Skip") {
            if ($varg1 == "actcathedral") {
                $costs = ArchitectsOfTheWestKingdom::$instance->cathedralCosts[$this->cathedral + 1];
                if ($this->type == 0) {
                    $reducs = array();
                    foreach ($costs as $cost) {
                        $reducs[] = $this->minusCost($cost, W);
                        $reducs[] = $this->minusCost($cost, S);
                    }
                    $costs = array_unique($reducs);
                }
                ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "cathedral");
                ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "pay", json_encode($this->filterCosts($costs)), "actcathedral");
                if ($this->type != 6) {
                    ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "discardBuilding", "unique");
                }
            } else {
                $buildingId = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
                ArchitectsOfTheWestKingdom::$instance->buildings->insertCardOnExtremePosition($buildingId, "cards{$this->player_id}", false);


                ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                    'id' => "res_" . $this->player_id . "_7",
                    'nb' => ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from building where card_location = 'hand{$this->player_id}'")
                ));

                $building = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM building WHERE card_id = {$buildingId}");
                $objbuilding = building::instantiate($building);

                $players = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb("select * from player order by player_no desc");
                foreach ($players as $player) {
                    if ($player['player_id'] == $this->player_id) {
                        ArchitectsOfTheWestKingdom::$instance->notify->player($player['player_id'], "move", clienttranslate('${player_name} builds ${building}'), array(
                            'player_id' => $this->player_id,
                            'player_name' => $this->player_name,
                            'building' => $building['card_type'],
                            'mobile' => "building" . $buildingId,
                            'parent' => "cards" . $this->player_id,
                            'position' => 'last'
                        ));
                    } else {
                        ArchitectsOfTheWestKingdom::$instance->notify->player($player['player_id'], "newbuilding", clienttranslate('${player_name} builds ${building}'), array(
                            'player_id' => $this->player_id,
                            'player_name' => $this->player_name,
                            'building' => $building['card_type'],
                            'card' => $building
                        ));
                    }
                }

                if ($this->type == 0) {
                    $costs = array();
                    $costs[] = $this->minusCost($objbuilding->cost, S);
                    $costs[] = $this->minusCost($objbuilding->cost, W);
                    ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "pay", json_encode($this->filterCosts($costs)), "actcathedral");
                } else {
                    $this->pay(null, $varg1, $objbuilding->cost);
                }

                if ($objbuilding->virtue > 0) {
                    $this->gain(null, $varg1, $objbuilding->virtue * V);
                } else if ($objbuilding->virtue < 0) {
                    $this->pay(null, $varg1, -$objbuilding->virtue * V);
                }

                $objbuilding->instant($this);

                if ($this->type == 5) {
                    $this->gain(null, null, B);
                }
            }
        }
    }


    /**
     * Advances cathedral progress and draws reward
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     * @param mixed $varg1 Value argument 1
     * @param mixed $varg2 Value argument 2
     */
    function cathedral($parg1, $parg2, $varg1, $varg2)
    {
        $this->cathedral++;

        ArchitectsOfTheWestKingdom::$instance->DbQuery("UPDATE player SET cathedral = {$this->cathedral} where player_id = {$this->player_id}");
        ArchitectsOfTheWestKingdom::$instance->notify->all("move", clienttranslate('${player_name} advances work on the Cathedral'), array(
            'player_id' => $this->player_id,
            'player_name' => $this->player_name,
            'mobile' => "cathedral_" . $this->player_id,
            'parent' => "cathedral{$this->cathedral}",
            'position' => 'last'
        ));

        $bonus = $this->getAdditionalBonus("cathedral");
        $reward = ArchitectsOfTheWestKingdom::$instance->rewards->pickCardForLocation("deck", "discard");
        if ($reward != null) {
            ArchitectsOfTheWestKingdom::$instance->notify->all("reward", '', array(
                'reward' => $reward,
                'rewardnb' => ArchitectsOfTheWestKingdom::$instance->rewards->countCardInLocation("deck")
            ));
            $this->gain("reward" . $reward['id'], null, ArchitectsOfTheWestKingdom::$instance->rewardsGain[$reward['type']] + $bonus);

            ArchitectsOfTheWestKingdom::$instance->setGameStateValue('no_undo', 1);
        } else {
            $this->gain("phreward", null, V + $bonus);
        }
    }

    /**
     * Prepares worker selection when player has no workers in reserve
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     */
    function argnoworker($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must return one worker');
        $ret['titleyou'] = clienttranslate('${you} must return one worker');

        $workerleft = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where location = 'reserve_{$this->player_id}'");

        if ($workerleft == 0) {
            $workers = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb("select * from worker where player_id = {$this->player_id} and location not like 'reserve%' and location not like 'blackmarket%' and location not like 'prison%' and location <> 'guildhall'");
            if (count($workers) == 0) {
                $workers = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb("select * from worker where player_id = {$this->player_id} and location <> 'guildhall'");
            }

            foreach ($workers as $worker) {
                $ret['selectable']['worker_' . $worker['id']] = array();
            }
        }

        return $ret;
    }

    /**
     * Returns a worker to reserve when player has none left
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     * @param string $varg1 Selected worker ID
     * @param mixed $varg2 Value argument 2
     */
    function noworker($parg1, $parg2, $varg1, $varg2)
    {
        $nbplayers = max(2, ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from player"));
        $nbmeeplesOnLocation = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*)  from worker where location = 'guildhall'");
        $nbmax = ($nbplayers + 1) * 4;
        if ($nbmeeplesOnLocation < $nbmax) {
            ArchitectsOfTheWestKingdom::$instance->addPendingFirst($this->player_id, "noworker");
        }

        ArchitectsOfTheWestKingdom::$instance->incStat(1, 'turns_number', $this->player_id);

        if ($varg1 != NULL) {
            $workerId = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
            $worker = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM worker WHERE id=" . $workerId);
            $target = "reserve_{$this->player_id}";

            ArchitectsOfTheWestKingdom::$instance->notify->all("move", '', array(
                'mobile' => "worker_" . $workerId,
                'parent' => "{$target}",
                'position' => 'last'
            ));

            ArchitectsOfTheWestKingdom::$instance->DbQuery("update worker set location = '{$target}', location_arg=0 where id = {$workerId}");

            ArchitectsOfTheWestKingdom::$instance->notify->all("note", clienttranslate('${player_name} returns one worker from ${board}'), array(
                'player_id' => $this->player_id,
                'player_name' => $this->player_name,
                'board' => $worker['location']
            ));

            $nbmeeplesLeft = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where player_id = {$this->player_id} and  location like 'reserve%'");
            ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                'id' => "res_" . $this->player_id . "_8",
                'nb' => $nbmeeplesLeft
            ));
        } else {
            ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "actionRound");
        }
    }

    /**
     * Prepares town center capture options
     * @param string $parg1 First captured location
     * @param string $parg2 Second captured location
     */
    function argtowncenter($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may capture workers (#nb#/#nb2#))');
        $ret['titleyou'] = clienttranslate('${you} may capture workers (#nb#/#nb2#)');

        $pending =  ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT* FROM pending order by id desc limit 1");
        $capturedone = intval($pending['arg3']);

        if ($capturedone == -1) {
            $ret['nb'] = 1;
            $ret['nb2'] = 1;
        } else {
            $ret['nb'] = $capturedone + 1;
            $ret['nb2'] = intval($pending['arg4']);
        }

        $nbloc = 0;
        if ($parg1 != null) {
            $nbloc++;
        }
        if ($parg2 != null) {
            $nbloc++;
        }

        $allowAdditional = $nbloc == 0 || ($nbloc == 1 && ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from player") < 4);


        if ($capturedone == -1 || $this->checkCost($this->getCost("towncenter", ($capturedone > 0) ? 2 : 1))) {
            $workers = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb("select * from worker where location not like 'reserve%'  and location not like 'blackmarket%' and location not like 'prison%' and location <> 'guildhall'");
            foreach ($workers as $worker) {
                if ($allowAdditional || $worker['location'] == $parg1 || $worker['location'] == $parg2) {
                    $ret['selectable']['worker_' . $worker['id']] = array();
                }
            }
        }

        $ret['buttons'][] = 'Skip';
        $ret['selectable']['Skip'] = array();

        if ($this->isUndoAvailable()) {
            $ret['buttons'][] = 'Undo';
            $ret['selectable']['Undo'] = array();
        }

        return $ret;
    }

    /**
     * Executes town center action - captures workers from selected location
     * @param string $parg1 First captured location
     * @param string $parg2 Second captured location
     * @param string $varg1 Selected worker to capture
     * @param mixed $varg2 Value argument 2
     */
    function towncenter($parg1, $parg2, $varg1, $varg2)
    {
        if ($varg1 != "Skip") {

            $workerId = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
            $worker = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM worker WHERE id=" . $workerId);
            $sql = "SELECT * from worker where location = '{$worker['location']}' and player_id = {$worker['player_id']}";
            $workers = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);
            $other_player_name = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select player_name from player where player_id = {$worker['player_id']}");

            $pending =  ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT* FROM pending order by id desc limit 1");
            $capturedone = intval($pending['arg3']);
            if ($capturedone != -1) {
                $this->pay(null, "towncenter", $this->getCost("towncenter", ($capturedone > 0) ? 2 : 1));
            }

            $target = "prison_{$this->player_id}";

            if ($this->player_id == $worker['player_id']) {
                $target = "reserve_{$this->player_id}";
            }
            $nb = 0;
            foreach ($workers as $worker) {
                ArchitectsOfTheWestKingdom::$instance->notify->all("move", '', array(
                    'mobile' => "worker_" . $worker['id'],
                    'parent' => "{$target}",
                    'position' => 'last'
                ));
                $nb++;
            }
            ArchitectsOfTheWestKingdom::$instance->DbQuery("update worker set location = '{$target}', location_arg=0 where location = '{$worker['location']}' and player_id = {$worker['player_id']}");

            ArchitectsOfTheWestKingdom::$instance->notify->all("note", clienttranslate('${player_name} captures ${nb} worker(s) from ${other_player_name} on ${board}'), array(
                'player_id' => $this->player_id,
                'player_name' => $this->player_name,
                'other_player_name' => $other_player_name,
                'board' => $worker['location'],
                'nb' => $nb
            ));

            if ($this->player_id == $worker['player_id']) {
                $nbmeeplesLeft = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where player_id = {$this->player_id} and  location like 'reserve%'");
                ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                    'id' => "res_" . $this->player_id . "_8",
                    'nb' => $nbmeeplesLeft
                ));
            } else {
                ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                    'id' => "res_" . $this->player_id . "_12",
                    'nb' => ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("SELECT count(*) FROM worker WHERE location = 'prison_{$this->player_id}'")
                ));
            }

            if ($parg1 == null) {
                $parg1 = $worker['location'];
            } else if ($parg2 == null && $parg1 != $worker['location']) {
                $parg2 = $worker['location'];
            }

            if ($capturedone != -1 && intval($pending['arg4']) > $capturedone + 1) {
                ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "towncenter", $parg1, $parg2, $capturedone + 1, $pending['arg4']);
            }
        }
    }

    /**
     * Prepares workshop apprentice selection options
     * @param int $parg1 Maximum column accessible
     * @param mixed $parg2 Parameter 2
     */
    function argworkshop($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose an apprentice up to column #nb# or pay extra silver');
        $ret['titleyou'] = clienttranslate('${you} must choose an apprentice up to column #nb# or pay extra silver');
        $ret['nb'] = $parg1;

        $sql = "SELECT * from apprentice where card_location = 'cards{$this->player_id}'";
        $apprentices = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

        $max = 5;
        if ($this->type == 8) {
            $max = 6;
        }

        if (count($apprentices) < $max) {
            $nbmax = intval($parg1) + $this->resources[SILVER];
            $sql = "SELECT * from apprentice where card_location like 'phapprentice%'";
            $apprentices = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

            foreach ($apprentices as $apprentice) {
                $column = intdiv((int) filter_var($apprentice['card_location'], FILTER_SANITIZE_NUMBER_INT) + 1, 2);
                if ($column <= $nbmax) {
                    $ret['selectable']['apprentice' . $apprentice['card_id']] = array();
                }
            }
        }

        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();

        return $ret;
    }

    /**
     * Executes workshop action - hires apprentice with optional extra cost
     * @param int $parg1 Maximum column accessible
     * @param mixed $parg2 Parameter 2
     * @param string $varg1 Selected apprentice
     * @param mixed $varg2 Value argument 2
     */
    function workshop($parg1, $parg2, $varg1, $varg2)
    {
        if ($varg1 != "Skip") {
            $apprenticeId = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
            $apprenticeIndex = (int) filter_var(ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("SELECT card_location FROM apprentice WHERE card_id = {$apprenticeId}"), FILTER_SANITIZE_NUMBER_INT);
            $col = intdiv($apprenticeIndex + 1, 2);

            $extracoin = $col - intval($parg1);
            if ($extracoin > 0) {
                $this->pay(null, "workshop", SI * $extracoin);
                $row = ($apprenticeIndex + 1) % 2;
                for ($i = 0; $i < $extracoin; $i++) {
                    $index = $row + $i * 2 + 1;

                    $apprentice = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM apprentice WHERE card_location = 'phapprentice{$index}'");
                    $bonus = $apprentice['bonus'] + 1;

                    ArchitectsOfTheWestKingdom::$instance->DbQuery("update apprentice set bonus = {$bonus} where card_id = {$apprentice['card_id']}");
                    ArchitectsOfTheWestKingdom::$instance->notify->all("countermask", '', array(
                        'id' => $apprentice['card_id'],
                        'nb' => $bonus
                    ));
                }
            }

            $this->pickApprentice($parg1, $parg2, $varg1, $varg2);
        }
    }

    /**
     * Prepares building discard options when hand limit exceeded
     * @param string $parg1 Discard mode ("unique" or normal)
     * @param mixed $parg2 Parameter 2
     */
    function argdiscardBuilding($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        if ($parg1 == "unique") {
            $ret['title'] = clienttranslate('${actplayer} must discard one building');
            $ret['titleyou'] = clienttranslate('${you} must discard one building');
        } else {
            $ret['title'] = clienttranslate('${actplayer} must discard one building until he has no more than 6');
            $ret['titleyou'] = clienttranslate('${you} must discard one building until you have no more than 6');
        }

        $sql = "SELECT * from building where card_location = 'hand{$this->player_id}'";
        $buildings = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

        if (count($buildings) > 6 || $parg1 == "unique") {
            foreach ($buildings as $building) {
                $ret['selectable']["building" . $building['card_id']] = array();
            }
        } else {
            $ret['buttons'][] = 'Skip';
            $ret['selectable']['Skip'] = array();
        }

        if ($this->isUndoAvailable() && $parg1 == "unique") {
            $ret['buttons'][] = 'Undo';
            $ret['selectable']['Undo'] = array();
        }


        return $ret;
    }

    /**
     * Discards selected building from hand
     * @param string $parg1 Discard mode
     * @param mixed $parg2 Parameter 2
     * @param string $varg1 Selected building to discard
     * @param mixed $varg2 Value argument 2
     */
    function discardBuilding($parg1, $parg2, $varg1, $varg2)
    {
        if ($varg1 != "Skip") {
            $buildingId = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);

            ArchitectsOfTheWestKingdom::$instance->buildings->insertCardOnExtremePosition($buildingId, "deck", false);

            $building = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM building WHERE card_id = {$buildingId}");

            ArchitectsOfTheWestKingdom::$instance->notify->player($this->player_id, "remove", '', array(
                'id' => $varg1
            ));

            ArchitectsOfTheWestKingdom::$instance->notify->all("counter", clienttranslate('${player_name} discards ${building}'), array(
                'player_id' => $this->player_id,
                'player_name' => $this->player_name,
                'building' => $building['card_type'],
                'id' => "res_" . $this->player_id . "_7",
                'nb' => ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from building where card_location = 'hand{$this->player_id}'")
            ));

            if ($parg1 != "unique") {
                ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "discardBuilding");
            }
        }
    }

    /**
     * Prepares apprentice discard options when limit exceeded
     * @param string $parg1 Skip mode ("noskip" or normal)
     * @param mixed $parg2 Parameter 2
     */
    function argdiscardApprentice($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may discard an apprentice');
        $ret['titleyou'] = clienttranslate('${you} may discard an apprentice');

        $sql = "SELECT * from apprentice where card_location = 'cards{$this->player_id}'";
        $apprentices = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

        $max = 5;
        if ($this->type == 8) {
            $max = 6;
        }

        if (count($apprentices) >= $max || $parg1 == "noskip") {
            foreach ($apprentices as $apprentice) {
                $ret['selectable']["apprentice" . $apprentice['card_id']] = array();
            }
            if ($this->isUndoAvailable()) {
                $ret['buttons'][] = 'Undo';
                $ret['selectable']['Undo'] = array();
            }
        } else {

            $ret['buttons'][] = 'Skip';
            $ret['selectable']['Skip'] = array();
        }



        return $ret;
    }

    /**
     * Discards selected apprentice
     * @param string $parg1 Skip mode
     * @param mixed $parg2 Parameter 2
     * @param string $varg1 Selected apprentice to discard
     * @param mixed $varg2 Value argument 2
     */
    function discardApprentice($parg1, $parg2, $varg1, $varg2)
    {
        if ($varg1 != "Skip" && $varg1 != null) {
            $apprenticeId = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);

            ArchitectsOfTheWestKingdom::$instance->apprentices->insertCardOnExtremePosition($apprenticeId, "deck", false);

            $apprentice = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM apprentice WHERE card_id = {$apprenticeId}");

            ArchitectsOfTheWestKingdom::$instance->notify->all("remove", clienttranslate('${player_name} discards ${apprentice}'), array(
                'player_id' => $this->player_id,
                'player_name' => $this->player_name,
                'apprentice' => $apprentice['card_type'],
                'id' => $varg1
            ));
        }
    }

    /**
     * Prepares storehouse trading options
     * @param int $parg1 Number of trades left
     * @param mixed $parg2 Parameter 2
     */
    function argstorehouse($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose a trade (#nb# left)');
        $ret['titleyou'] = clienttranslate('${you} must choose a trade (#nb# left)');
        $ret['storehouse'] = true;
        $ret['nb'] = $parg1;

        $filtered = $this->filterCosts([2 * S, 2 * W, 2 * C, W + S, C + S, C + W]);
        foreach ($filtered as $cost) {
            $ret['buttons'][] = 'res' . $cost . "to" . V;
            $ret['selectable']['res' . $cost . "to" . V] = array();
        }

        $filtered = $this->filterCosts([3 * S, 2 * S + W, S + 2 * W, 3 * W]);
        foreach ($filtered as $cost) {
            $ret['buttons'][] = 'res' . $cost . "to" . M;
            $ret['selectable']['res' . $cost . "to" . M] = array();
        }

        $sql = "SELECT * from apprentice where card_location = 'cards{$this->player_id}'";
        $apprentices = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);
        foreach ($apprentices as $apprentice) {
            $bobj = apprentice::instantiate($apprentice);
            foreach ($bobj->getAdditionalStorehouse() as $cost => $gain) {
                if ($this->checkCost($cost)) {
                    $ret['buttons'][] = 'res' . $cost . "to" . $gain;
                    $ret['selectable']['res' . $cost . "to" . $gain] = array();
                }
            }
        }

        //remove inferior options
        foreach ($ret['buttons'] as $costid) {
            $cost = substr($costid, 3, strpos($costid, "to") - 3);
            $gain = substr($costid, strpos($costid, "to") + 2, 100);

            foreach ($ret['buttons'] as $costid2) {
                if ($costid != $costid2) {
                    $cost2 = substr($costid2, 3, strpos($costid2, "to") - 3);
                    $gain2 = substr($costid2, strpos($costid2, "to") + 2, 100);

                    if ($gain == $gain2) {
                        $higher = true;
                        for ($i = 1; $i <= 6 && $higher; $i++) {
                            if ((intdiv($cost, pow(10, $i)) % 10) > (intdiv($cost2, pow(10, $i)) % 10)) {
                                $higher = false;
                            }
                        }

                        if ($higher) {
                            $ret['buttons'] = \array_diff($ret['buttons'], [$costid2]);
                            unset($ret['selectable'][$costid2]);
                        }
                    }
                }
            }
        }


        $ret['buttons'][] = 'Skip';
        $ret['selectable']['Skip'] = array();


        if ($this->isUndoAvailable()) {
            $ret['buttons'][] = 'Undo';
            $ret['selectable']['Undo'] = array();
        }

        return $ret;
    }

    /**
     * Executes storehouse trade action
     * @param int $parg1 Number of trades left
     * @param mixed $parg2 Parameter 2
     * @param string $varg1 Selected trade (format: "res{cost}to{gain}")
     * @param mixed $varg2 Value argument 2
     */
    function storehouse($parg1, $parg2, $varg1, $varg2)
    {
        if ($varg1 != "Skip") {
            $from = explode("to", $varg1)[0];
            $to = explode("to", $varg1)[1];
            $this->pay(null, "storehouse", $from, $varg2);
            $this->gain("storehouse", null, intval($to));


            if ($parg1 > 1) {
                ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "storehouse", $parg1 - 1);
            }
        }
    }

    /**
     * Prepares building selection from multiple options
     * @param string $parg1 Selection mode ("flush" or normal)
     * @param mixed $parg2 Parameter 2
     */
    function argselectBuilding($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must keep one building');
        $ret['titleyou'] = clienttranslate('${you} must keep one building');

        $sql = "SELECT * from building where card_location = 'selectCards{$this->player_id}'";
        $buildings = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);
        $ret['selectCards'] = $buildings;

        foreach ($buildings as $building) {
            $ret['selectable']["building" . $building['card_id']] = array();
        }

        if ($this->isUndoAvailable() && $parg1 == "flush") {
            $ret['buttons'][] = 'Undo';
            $ret['selectable']['Undo'] = array();
        }


        return $ret;
    }

    /**
     * Selects one building from multiple options and discards the rest
     * @param string $parg1 Selection mode
     * @param mixed $parg2 Parameter 2
     * @param string $varg1 Selected building ID
     * @param mixed $varg2 Value argument 2
     */
    function selectBuilding($parg1, $parg2, $varg1, $varg2)
    {
        $buildingId = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);

        ArchitectsOfTheWestKingdom::$instance->buildings->insertCardOnExtremePosition($buildingId, "hand{$this->player_id}", false);

        $building = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM building WHERE card_id = {$buildingId}");
        ArchitectsOfTheWestKingdom::$instance->notify->player($this->player_id, "move", '', array(
            'mobile' => "building" . $buildingId,
            'parent' => "hand" . $this->player_id,
            'position' => 'last'
        ));

        if ($parg1 == "flush") {
            $sql = "SELECT * from building where card_location like 'selectCards%'";
            $buildings = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);
            foreach ($buildings as $building) {
                $buildingId = $building['card_id'];
                ArchitectsOfTheWestKingdom::$instance->buildings->insertCardOnExtremePosition($buildingId, "deck", false);
            }
        }
    }


    /**
     * Prepares apprentice hiring options from public display
     * @param string $parg1 Refill mode ("norefill" or normal)
     * @param mixed $parg2 Parameter 2
     */
    function argpickApprentice($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must hire an apprentice');
        $ret['titleyou'] = clienttranslate('${you} must hire an apprentice');

        $sql = "SELECT * from apprentice where card_location = 'cards{$this->player_id}'";
        $apprentices = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

        $max = 5;
        if ($this->type == 8) {
            $max = 6;
        }

        if (count($apprentices) < $max) {
            $sql = "SELECT * from apprentice where card_location like 'phapprentice%'";
            $apprentices = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

            foreach ($apprentices as $apprentice) {
                $ret['selectable']["apprentice" . $apprentice['card_id']] = array();
            }
        }
        if ($parg1 != "norefill") {
            $ret['buttons'][] = 'Skip';
            $ret['selectable']['Skip'] = array();

            if ($this->isUndoAvailable()) {
                $ret['buttons'][] = 'Undo';
                $ret['selectable']['Undo'] = array();
            }
        }

        return $ret;
    }

    /**
     * Hires apprentice from public display and refills if needed
     * @param string $parg1 Refill mode
     * @param mixed $parg2 Parameter 2
     * @param string $varg1 Selected apprentice ID
     * @param mixed $varg2 Value argument 2
     */
    function pickApprentice($parg1, $parg2, $varg1, $varg2)
    {
        if ($varg1 != 'Skip' && $varg1 != null) {
            $apprenticeId = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
            $apprenticeIndex = (int) filter_var(ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("SELECT card_location FROM apprentice WHERE card_id = {$apprenticeId}"), FILTER_SANITIZE_NUMBER_INT);

            ArchitectsOfTheWestKingdom::$instance->apprentices->insertCardOnExtremePosition($apprenticeId, "cards{$this->player_id}", false);

            $apprentice = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM apprentice WHERE card_id = {$apprenticeId}");
            ArchitectsOfTheWestKingdom::$instance->notify->all("move", clienttranslate('${player_name} hires ${apprentice}'), array(
                'player_id' => $this->player_id,
                'player_name' => $this->player_name,
                'apprentice' => $apprentice['card_type'],
                'mobile' => "apprentice" . $apprentice['card_id'],
                'parent' => "cards" . $this->player_id,
                'position' => 'last'
            ));

            if ($apprentice['bonus'] > 0) {
                $this->gain("apprentice" . $apprentice['card_id'], null, SI * $apprentice['bonus']);

                ArchitectsOfTheWestKingdom::$instance->DbQuery("update apprentice set bonus = 0 where card_id = {$apprentice['card_id']}");
                ArchitectsOfTheWestKingdom::$instance->notify->all("countermask", '', array(
                    'id' => $apprentice['card_id'],
                    'nb' => 0
                ));
            }

            $appobj = Apprentice::instantiate($apprentice);
            if ($appobj->virtue > 0) {
                $this->gain(null, $varg1, $appobj->virtue * V);
            } else if ($appobj->virtue < 0) {
                $this->pay(null, $varg1, -$appobj->virtue * V);
            }

            if ($parg1 != "norefill") {
                for ($i = $apprenticeIndex + 2; $i <= 8; $i += 2) {
                    $apprentice = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM apprentice WHERE card_location = 'phapprentice{$i}'");
                    $newapprenticeIndex = intval($i) - 2;

                    ArchitectsOfTheWestKingdom::$instance->DbQuery("update apprentice set card_location = 'phapprentice{$newapprenticeIndex}' where card_id = {$apprentice['card_id']}");

                    ArchitectsOfTheWestKingdom::$instance->notify->all("move", '', array(
                        'mobile' => "apprentice" . $apprentice['card_id'],
                        'parent' => "phapprentice{$newapprenticeIndex}",
                        'position' => 'last'
                    ));
                }

                $newloc = 8 - $apprenticeIndex % 2;
                ArchitectsOfTheWestKingdom::$instance->apprentices->pickCardForLocation('deck', 'phapprentice' . $newloc);
                ArchitectsOfTheWestKingdom::$instance->setGameStateValue('no_undo', 1);
                $apprentice = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM apprentice WHERE card_location = 'phapprentice{$newloc}' ");

                ArchitectsOfTheWestKingdom::$instance->notify->all("newapprentice", '', array(
                    'card' => $apprentice
                ));
            }

            $this->updateVP();
        }
    }

    /**
     * Calculates the cost for a specific action including reductions
     * @param string $action Action name
     * @param int $index Action variant index
     */
    function getCost($action, $index = 0)
    {
        $ret = 0;

        switch ($action) {
            case "blackmarketa":
                $ret = SI + V;
                break;
            case "blackmarketb":
                $ret = SI * 2 + V;
                break;
            case "blackmarketc":
                $ret = SI * 3 + V;
                break;
            case "taxstand":
                $ret = 2 * V;
                break;
            case "towncenter":
                if ($index == 1) {
                    $ret = TX;
                } else if ($index == 2) {
                    $ret = SI;
                }
                break;
            case "workshop":
                if ($index == 1) {
                    $ret = SI * 2 + TX * 2;
                }
                break;
            case "guardhouse":
                if ($index == 4) {
                    $ret = 3 * TX + 3 * SI;
                }
                break;
            case "guardhouseb":
                $ret = 2 * TX + 3 * SI;
                break;
        }

        $reduc = $this->getCostReduction($action);

        if ($this->virtue <= 1) {
            $reduc += TX * 2;
        } else if ($this->virtue <= 3) {
            $reduc += TX;
        }
        $ret = $this->minusCost($ret, $reduc);

        return $ret;
    }

    /**
     * Prepares resource gain selection options
     * @param mixed $parg1 Parameter 1
     * @param string $parg2 JSON encoded gain options
     */
    function arggain($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose what to gain');
        $ret['titleyou'] = clienttranslate('${you} must choose what to gain');

        foreach (json_decode($parg2) as $id => $cost) {
            $ret['buttons'][] = 'res' . $cost;
            $ret['selectable']['res' . $cost] = array();
        }

        if ($this->isUndoAvailable()) {
            $ret['buttons'][] = 'Undo';
            $ret['selectable']['Undo'] = array();
        }


        return $ret;
    }

    /**
     * Directly gains resources of specified type and amount
     * @param int $nb Number of resources to gain
     * @param int $type Resource type constant
     * @param string $source Source of the gain
     */
    function gainDirect($nb, $type, $source)
    {
        if ($nb > 0) {
            if ($type <= 6) {
                $this->resources[$type] += $nb;
                ArchitectsOfTheWestKingdom::$instance->DbQuery("update player set res{$type} = " . $this->resources[$type] . " where player_id = " . $this->player_id);

                ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                    'id' => "res_" . $this->player_id . "_" . $type,
                    'nb' => $this->resources[$type]
                ));

                $costinv = min($nb, 9) * pow(10, $type);
                ArchitectsOfTheWestKingdom::$instance->notify->all("gain", clienttranslate('${player_name} gains ${costnb} <div id="0" class="arcicon res${typeres}"></div>'), array(
                    'player_id' => $this->player_id,
                    'player_name' => $this->player_name,
                    'costnb' => $nb,
                    'costinv' => $costinv,
                    'source' => $source,
                    'target' => "playerboard" . $this->player_id,
                    'typeres' => $type
                ));
                $this->updateVP();
            } else if ($type == VIRTUE) {
                $this->virtue += $nb;
                if ($this->virtue > 14) {
                    for ($i = 0; $i < $this->virtue - 14; $i++) {
                        $debt = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM debt WHERE player_id = {$this->player_id} and paid = 0 limit 1");
                        if ($debt != null) {
                            ArchitectsOfTheWestKingdom::$instance->DbQuery("delete from debt where id = {$debt['id']}");

                            ArchitectsOfTheWestKingdom::$instance->notify->all("counter", clienttranslate('${player_name} destroys one unpaid debt'), array(
                                'id' => "res_" . $this->player_id . "_13",
                                'nb' => ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("SELECT count(*) FROM debt WHERE player_id = {$this->player_id} and paid = 0"),
                                'player_id' => $this->player_id,
                                'player_name' => $this->player_name,
                            ));
                        }
                    }
                    $this->virtue = 14;
                }

                ArchitectsOfTheWestKingdom::$instance->DbQuery("update player set virtue = " . $this->virtue . " where player_id = " . $this->player_id);


                if (strpos($source, "building") !== false) {
                    $buildingId = (int) filter_var($source, FILTER_SANITIZE_NUMBER_INT);
                    $building = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM building WHERE card_id = {$buildingId}");

                    ArchitectsOfTheWestKingdom::$instance->notify->all("move", clienttranslate('${player_name} gains ${costnb} <div id="0" class="arcicon res${typeres}"></div> from ${building}'), array(
                        'mobile' => "virtue_" . $this->player_id,
                        'parent' => "virtue" . $this->virtue,
                        'position' => 'last',
                        'player_id' => $this->player_id,
                        'player_name' => $this->player_name,
                        'costnb' => $nb,
                        'typeres' => 8,
                        'building' => $building['card_type'],
                    ));
                } else {

                    ArchitectsOfTheWestKingdom::$instance->notify->all("move", clienttranslate('${player_name} gains ${costnb} <div id="0" class="arcicon res${typeres}"></div>'), array(
                        'mobile' => "virtue_" . $this->player_id,
                        'parent' => "virtue" . $this->virtue,
                        'position' => 'last',
                        'player_id' => $this->player_id,
                        'player_name' => $this->player_name,
                        'costnb' => $nb,
                        'typeres' => 8
                    ));
                }
                $this->updateVP();
            }
        }
    }

    /**
     * Processes resource gains from encoded cost value
     * @param string $parg1 Source identifier
     * @param mixed $parg2 Parameter 2
     * @param int $varg1 Encoded resource gain value
     * @param mixed $varg2 Value argument 2
     */
    function gain($parg1, $parg2, $varg1, $varg2 = null)
    {

        if ($varg1 != null) {
            $varg1 = preg_replace("/[^0-9\.]/", '', $varg1);
            for ($type = 1; $type <= 10; $type++) {
                $nb = intdiv($varg1, pow(10, $type)) % 10;
                if ($nb > 0) {
                    if ($type <= 6) {
                        $this->resources[$type] += $nb;
                        ArchitectsOfTheWestKingdom::$instance->DbQuery("update player set res{$type} = " . $this->resources[$type] . " where player_id = " . $this->player_id);

                        ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                            'id' => "res_" . $this->player_id . "_{$type}",
                            'nb' => $this->resources[$type]
                        ));
                    } else if ($type == VIRTUE) {
                        $this->virtue += $nb;
                        if ($this->virtue > 14) {
                            for ($i = 0; $i < $this->virtue - 14; $i++) {
                                $debt = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM debt WHERE player_id = {$this->player_id} and paid = 0 limit 1");
                                if ($debt != null) {
                                    ArchitectsOfTheWestKingdom::$instance->DbQuery("delete from debt where id = {$debt['id']}");
                                    ArchitectsOfTheWestKingdom::$instance->notify->all("counter", clienttranslate('${player_name} destroys one unpaid debt'), array(
                                        'id' => "res_" . $this->player_id . "_13",
                                        'nb' => ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("SELECT count(*) FROM debt WHERE player_id = {$this->player_id} and paid = 0"),
                                        'player_id' => $this->player_id,
                                        'player_name' => $this->player_name,
                                    ));
                                }
                            }
                            $varg1 -= ($this->virtue - 14) * V;
                            $this->virtue = 14;
                            $this->updateVP();
                        }

                        ArchitectsOfTheWestKingdom::$instance->DbQuery("update player set virtue = " . $this->virtue . " where player_id = " . $this->player_id);
                        ArchitectsOfTheWestKingdom::$instance->notify->all("move", '', array(
                            'mobile' => "virtue_" . $this->player_id,
                            'parent' => "virtue" . $this->virtue,
                            'position' => 'last'
                        ));
                    } else if ($type == BUILDING) {
                        $nbcards = $nb;
                        $cards = ArchitectsOfTheWestKingdom::$instance->buildings->pickCardsForLocation($nbcards, 'deck', 'hand' . $this->player_id);
                        ArchitectsOfTheWestKingdom::$instance->setGameStateValue('no_undo', 1);
                        foreach ($cards as $card_id => $card) {
                            $building = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM building WHERE card_id = {$card['id']}");
                            ArchitectsOfTheWestKingdom::$instance->notify->player($this->player_id, "newbuilding", '', array(
                                'card' => $building
                            ));
                        }
                        ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                            'id' => "res_" . $this->player_id . "_7",
                            'nb' => ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from building where card_location = 'hand{$this->player_id}'"),
                        ));
                    }
                }
            }

            if ($varg1 > 0) {
                $this->updateVP();

                if (strpos($parg1, "building") !== false) {
                    $buildingId = (int) filter_var($parg1, FILTER_SANITIZE_NUMBER_INT);
                    $building = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM building WHERE card_id = {$buildingId}");

                    ArchitectsOfTheWestKingdom::$instance->notify->all("gain", clienttranslate('${player_name} gains ${cost} from ${building}'), array(
                        'player_id' => $this->player_id,
                        'player_name' => $this->player_name,
                        'cost' => $varg1,
                        'costinv' => $varg1,
                        'building' => $building['card_type'],
                        'source' => $parg1,
                        'target' => "playerboard" . $this->player_id,
                    ));
                } else {
                    ArchitectsOfTheWestKingdom::$instance->notify->all("gain", clienttranslate('${player_name} gains ${cost}'), array(
                        'player_id' => $this->player_id,
                        'player_name' => $this->player_name,
                        'cost' => $varg1,
                        'costinv' => $varg1,
                        'source' => $parg1,
                        'target' => "playerboard" . $this->player_id,
                    ));
                }
            }
        }
    }

    /**
     * Prepares payment options from available costs
     * @param string $parg1 JSON encoded cost options
     * @param mixed $parg2 Parameter 2
     */
    function argpay($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose how to pay');
        $ret['titleyou'] = clienttranslate('${you} must choose how to pay');

        foreach (json_decode($parg1) as $id => $cost) {
            $ret['buttons'][] = 'res' . $cost;
            $ret['selectable']['res' . $cost] = array();
        }

        return $ret;
    }

    /**
     * Processes resource payments from encoded cost value
     * @param mixed $parg1 Parameter 1
     * @param string $parg2 Target identifier
     * @param int $varg1 Encoded cost value
     * @param mixed $varg2 Value argument 2
     */
    function pay($parg1, $parg2, $varg1, $varg2 = null)
    {
        if ($varg1 != null) {
            $varg1 = preg_replace("/[^0-9\.]/", '', $varg1);

            if ($this->type == 1) {
                $marble = intdiv($varg1, pow(10, MARBLE)) % 10;
                $varg1 = $varg1 - $marble * M + $marble * G;
            }


            for ($type = 1; $type <= 10; $type++) {
                $nb = intdiv($varg1, pow(10, $type)) % 10;
                if ($nb > 0) {
                    if ($type <= 6) {
                        if ($this->type == 1 && $type == GOLD && $this->resources[$type] < $nb) {
                            $left = $nb - $this->resources[$type];
                            $this->resources[$type] = 0;
                            $varg1 = $varg1 + ($left * M) - ($left * G);
                        } else {
                            $this->resources[$type] -= $nb;
                        }
                        ArchitectsOfTheWestKingdom::$instance->DbQuery("update player set res{$type} = " . $this->resources[$type] . " where player_id = " . $this->player_id);

                        ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                            'id' => "res_" . $this->player_id . "_{$type}",
                            'nb' => $this->resources[$type]
                        ));
                    } else if ($type == 7) //tax
                    {
                        $this->resources[SILVER] -= $nb;
                        ArchitectsOfTheWestKingdom::$instance->DbQuery("update player set res6 = " . $this->resources[SILVER] . " where player_id = " . $this->player_id);

                        ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                            'id' => "res_" . $this->player_id . "_" . SILVER,
                            'nb' => $this->resources[SILVER]
                        ));

                        $tax = ArchitectsOfTheWestKingdom::$instance->getGameStateValue('tax');
                        $tax += $nb;
                        ArchitectsOfTheWestKingdom::$instance->setGameStateValue('tax', $tax);

                        ArchitectsOfTheWestKingdom::$instance->notify->all("counterid", '', array(
                            'id' => "taxcpt",
                            'nb' => $tax
                        ));
                    } else if ($type == VIRTUE) //virtue
                    {
                        $this->virtue -= $nb;

                        if ($this->virtue < 0) {
                            $varg1 = $varg1 + V * $this->virtue;
                            $varg1 = $varg1 + D * -1 * $this->virtue;
                            $this->virtue = 0;
                        }

                        ArchitectsOfTheWestKingdom::$instance->DbQuery("update player set virtue = " . $this->virtue . " where player_id = " . $this->player_id);
                        ArchitectsOfTheWestKingdom::$instance->notify->all("move", '', array(
                            'mobile' => "virtue_" . $this->player_id,
                            'parent' => "virtue" . $this->virtue,
                            'position' => 'last'
                        ));
                    } else if ($type == DEBT) //Debt
                    {
                        for ($i = 0; $i < $nb; $i++) {
                            if ($this->type == 2) {
                                ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "hugo");
                            } else {
                                ArchitectsOfTheWestKingdom::$instance->DbQuery("insert into debt (player_id) VALUES ({$this->player_id})");

                                ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                                    'id' => "res_" . $this->player_id . "_13",
                                    'nb' => ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("SELECT count(*) FROM debt WHERE player_id = {$this->player_id} and paid = 0"),
                                    'player_id' => $this->player_id,
                                    'player_name' => $this->player_name
                                ));
                            }
                        }
                    }
                }
            }

            if ($varg1 > 0) {
                $this->updateVP();
                $btype = "";

                $txt = clienttranslate('${player_name} pays ${cost}');
                if ($varg1 == (intdiv($varg1, pow(10, DEBT)) % 10) * pow(10, DEBT)) {
                    $txt = clienttranslate('${player_name} gains ${cost}');
                } else if ($varg1 == (intdiv($varg1, pow(10, VIRTUE)) % 10) * pow(10, VIRTUE)) {

                    if (strpos($parg2, "building") !== false) {
                        $buildingId = (int) filter_var($parg2, FILTER_SANITIZE_NUMBER_INT);
                        $building = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM building WHERE card_id = {$buildingId}");
                        $btype = $building['card_type'];
                        $txt = clienttranslate('${player_name} loses ${cost} from ${building}');
                    } else {
                        $txt = clienttranslate('${player_name} loses ${cost}');
                    }
                }


                ArchitectsOfTheWestKingdom::$instance->notify->all("gain", $txt, array(
                    'player_id' => $this->player_id,
                    'player_name' => $this->player_name,
                    'cost' => $varg1,
                    'costinv' => $varg1,
                    'source' => "playerboard" . $this->player_id,
                    'building' => $btype,
                    'target' => $parg2,
                ));
            }
        }
    }

    /**
     * Prepares Hugo character ability options (avoid debt with silver)
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     */
    function arghugo($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may spend ${cost} to avoid one debt');
        $ret['titleyou'] = clienttranslate('${you} may spend ${cost} to avoid one debt');
        $ret['cost'] = 2 * SI;
        if ($this->resources[SILVER] >= 2) {
            $ret['buttons'][] = 'Confirm';
            $ret['selectable']['Confirm'] = array();
        }
        $ret['buttons'][] = 'Pass';
        $ret['selectable']['Pass'] = array();
        if ($this->isUndoAvailable()) {
            $ret['buttons'][] = 'Undo';
            $ret['selectable']['Undo'] = array();
        }
        return $ret;
    }

    /**
     * Executes Hugo character ability - pay silver to avoid debt
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     * @param string $varg1 Choice ("Confirm" or "Pass")
     * @param mixed $varg2 Value argument 2
     */
    function hugo($parg1, $parg2, $varg1, $varg2)
    {
        if ($varg1 == 'Confirm') {
            $this->pay(null, null, 2 * SI);
        } else {
            ArchitectsOfTheWestKingdom::$instance->DbQuery("insert into debt (player_id) VALUES ({$this->player_id})");

            ArchitectsOfTheWestKingdom::$instance->notify->all("counter", '', array(
                'id' => "res_" . $this->player_id . "_13",
                'nb' => ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("SELECT count(*) FROM debt WHERE player_id = {$this->player_id} and paid = 0"),
                'player_id' => $this->player_id,
                'player_name' => $this->player_name
            ));
        }
    }

    /**
     * Checks if player can afford the specified cost
     * @param int $cost Encoded cost value
     */
    function checkCost($cost)
    {
        $ok = true;
        for ($i = 1; $i <= 5 && $ok; $i++) {

            if ((intdiv($cost, pow(10, $i)) % 10) > $this->resources[$i]) {
                if ($this->type == 1 && ($i == GOLD || $i == MARBLE)) {
                } else {
                    $ok = false;
                }
            }
        }

        if ($this->type == 1) {
            $totcost = (intdiv($cost, pow(10, GOLD)) % 10) + (intdiv($cost, pow(10, MARBLE)) % 10);
            $totres = $this->resources[GOLD] + $this->resources[MARBLE];
            if ($totcost > $totres) {
                $ok = false;
            }
        }

        $silver = (intdiv($cost, pow(10, SILVER)) % 10) + (intdiv($cost, pow(10, TAX)) % 10);
        if ($silver > $this->resources[SILVER]) {
            $ok = false;
        }
        return $ok;
    }

    /**
     * Filters array of costs to only include affordable ones
     * @param array $costs Array of encoded cost values
     */
    function filterCosts($costs)
    {
        $filtered = array();

        foreach ($costs as $c => $cost) {
            if ($this->checkCost($cost)) {
                $filtered[] = $cost;
            }
        }
        return $filtered;
    }

    /**
     * Gets building requirements based on owned apprentices' skills
     */
    function getAppRequirement()
    {
        $ret = 0;

        $sql = "SELECT * from apprentice where card_location = 'cards{$this->player_id}'";
        $apprentices = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

        foreach ($apprentices as $apprentice) {
            $appobj = apprentice::instantiate($apprentice);
            if (($appobj->skill & CAR) == CAR && ($ret & CAR) == 0) {
                $ret += CAR;
            }
            if (($appobj->skill & TIL) == TIL && ($ret & TIL) == 0) {
                $ret += TIL;
            }
            if (($appobj->skill & MAS) == MAS && ($ret & MAS) == 0) {
                $ret += MAS;
            }
        }

        return $ret;
    }

    /**
     * Subtracts reduction from cost for each resource type
     * @param int $cost Encoded cost value
     * @param int $reduc Encoded reduction value
     */
    function minusCost($cost, $reduc)
    {
        $after = 0;
        for ($type = 1; $type <= 10; $type++) {
            $ct = intdiv($cost, pow(10, $type)) % 10;
            $rt = intdiv($reduc, pow(10, $type)) % 10;
            if ($ct - $rt > 0) {
                $ct -= $rt;
            } else {
                $ct = 0;
            }
            $after += pow(10, $type) * $ct;
        }
        return $after;
    }

    /**
     * Triggers final scoring effects from all owned buildings
     */
    function instantFinal()
    {
        $sql = "SELECT * from building where card_location = 'cards{$this->player_id}'";
        $buildings = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);
        foreach ($buildings as $building) {
            $bobj = building::instantiate($building);
            $bobj->instantFinal($this);
        }
        $this->updateVP();
    }

    /**
     * Calculates and updates player's victory points from all sources
     * @param bool $final Whether this is final scoring
     */
    function updateVP($final = false)
    {
        $ret = array();
        $ret[0] = $this->player_name;
        if (strlen($ret[0]) > 4) {
            $ret[0] = substr($ret[0], 0, 3) . ".";
        }

        $vp = 0;

        $sql = "SELECT * from building where card_location = 'cards{$this->player_id}'";
        $buildings = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

        $vpbuilding = 0;
        foreach ($buildings as $building) {
            $bobj = building::instantiate($building);
            $vpbuilding += $bobj->vp;
            $vpbuilding += $bobj->getExtraVP();
        }
        $vp += $vpbuilding;
        $ret[1] = $vpbuilding;

        ArchitectsOfTheWestKingdom::$instance->setStat($vpbuilding, 'buildings', $this->player_id);

        $vp += ArchitectsOfTheWestKingdom::$instance->virtue[$this->virtue];
        $ret[3] = ArchitectsOfTheWestKingdom::$instance->virtue[$this->virtue];
        ArchitectsOfTheWestKingdom::$instance->setStat(ArchitectsOfTheWestKingdom::$instance->virtue[$this->virtue], 'virtue', $this->player_id);

        $vp += ArchitectsOfTheWestKingdom::$instance->cathedralVP[$this->cathedral];
        $ret[2] = ArchitectsOfTheWestKingdom::$instance->cathedralVP[$this->cathedral];
        ArchitectsOfTheWestKingdom::$instance->setStat(ArchitectsOfTheWestKingdom::$instance->cathedralVP[$this->cathedral], 'cathedral', $this->player_id);

        $malus = -2;
        if (ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("SELECT count(*) from building where card_location = 'cards{$this->player_id}' and card_type = 27") > 0) {
            $malus = -1;
        }

        $vp += $malus * ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from debt where player_id = {$this->player_id} and paid = 0");
        $ret[4] = $malus * ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from debt where player_id = {$this->player_id} and paid = 0");
        ArchitectsOfTheWestKingdom::$instance->setStat($malus * ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from debt where player_id = {$this->player_id} and paid = 0"), 'debt', $this->player_id);


        $vp += $this->resources[MARBLE];
        $vp += $this->resources[GOLD];
        $ret[5] = $this->resources[MARBLE] + $this->resources[GOLD];
        ArchitectsOfTheWestKingdom::$instance->setStat($this->resources[MARBLE] + $this->resources[GOLD], 'resource', $this->player_id);

        $vp += intdiv($this->resources[SILVER], 10);
        $ret[6] = intdiv($this->resources[SILVER], 10);
        ArchitectsOfTheWestKingdom::$instance->setStat(intdiv($this->resources[SILVER], 10), 'silver', $this->player_id);

        $vp -= intdiv(intval(ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where player_id = {$this->player_id} and location ='prison'")), 2);
        $ret[7] = -1 * intdiv(intval(ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where player_id = {$this->player_id} and location ='prison'")), 2);
        ArchitectsOfTheWestKingdom::$instance->setStat(-1 * intdiv(intval(ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where player_id = {$this->player_id} and location ='prison'")), 2), 'prison', $this->player_id);

        $ret[8] = $vp;

        $aux = $this->virtue * 1000 + $this->resources[SILVER];

        if (ArchitectsOfTheWestKingdom::$instance->getGameStateValue('live_scoring') == 2 || $final) {
            ArchitectsOfTheWestKingdom::$instance->DbQuery("UPDATE player SET player_score = {$vp}, player_score_aux = {$aux} where player_id = {$this->player_id}");

            ArchitectsOfTheWestKingdom::$instance->notify->all("counterid", '', array(
                'id' => 'player_score_' . $this->player_id,
                'nb' => $vp
            ));
        }
        return $ret;
    }

    /**
     * Gets bonus resources from apprentices for specific location
     * @param string $location Location name
     */
    function getAdditionalBonus($location)
    {
        $ret = 0;

        $sql = "SELECT * from apprentice where card_location = 'cards{$this->player_id}'";
        $apprentices = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

        foreach ($apprentices as $apprentice) {
            $bobj = apprentice::instantiate($apprentice);
            $ret += $bobj->getAdditionalBonus($location);
        }

        return $ret;
    }

    /**
     * Gets cost reductions from apprentices and character abilities
     * @param string $location Location name
     */
    function getCostReduction($location)
    {
        $ret = 0;

        $sql = "SELECT * from apprentice where card_location = 'cards{$this->player_id}'";
        $apprentices = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

        foreach ($apprentices as $apprentice) {
            $bobj = apprentice::instantiate($apprentice);
            $ret += $bobj->getCostReduction($location);
        }

        if ($this->type == 4) {
            $ret += TX;
        }

        if ($this->type == 8 && $location == "workshop") {
            $ret += SI;
        }

        return $ret;
    }

    /**
     * Adds pending actions from apprentice abilities
     * @param string $location Location name
     */
    function addPending($location)
    {
        $ret = 0;

        $sql = "SELECT * from apprentice where card_location = 'cards{$this->player_id}' order by card_type desc";
        $apprentices = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

        foreach ($apprentices as $apprentice) {
            $bobj = apprentice::instantiate($apprentice);
            $bobj->addPending($location);
        }

        return $ret;
    }

    /**
     * Prepares Fara character ability options (release imprisoned worker)
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     */
    function argfara($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may release 1 imprisoned worker');
        $ret['titleyou'] = clienttranslate('${you} may release 1 imprisoned worker');

        $prisoners = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("SELECT count(*) from worker where location = 'prison' and player_id = {$this->player_id}");
        if ($prisoners > 0) {
            $ret['buttons'][] = 'Confirm';
            $ret['selectable']['Confirm'] = array();
            $ret['buttons'][] = 'Pass';
            $ret['selectable']['Pass'] = array();
        }
        return $ret;
    }

    /**
     * Executes Fara character ability - releases one worker from prison
     * @param mixed $parg1 Parameter 1
     * @param mixed $parg2 Parameter 2
     * @param string $varg1 Choice ("Confirm" or "Pass")
     * @param mixed $varg2 Value argument 2
     */
    function fara($parg1, $parg2, $varg1, $varg2)
    {
        if ($varg1 == 'Confirm') {
            $sql = "SELECT * from worker where location = 'prison' and player_id = {$this->player_id} LIMIT 1";
            $prisonners = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);
            $target = "reserve_" . $this->player_id;

            foreach ($prisonners as $worker) {
                ArchitectsOfTheWestKingdom::$instance->notify->all("move", '', array(
                    'mobile' => "worker_" . $worker['id'],
                    'parent' => "{$target}",
                    'position' => 'last'
                ));
                ArchitectsOfTheWestKingdom::$instance->DbQuery("update worker set location = '{$target}' where id = {$worker['id']}");
                $nbmeeplesLeft = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where player_id = {$this->player_id} and  location like 'reserve%'");
                ArchitectsOfTheWestKingdom::$instance->notify->all("counter", clienttranslate('${player_name} releases 1 worker from prison (Fara)'), array(
                    'player_id' => $this->player_id,
                    'player_name' => $this->player_name,
                    'id' => "res_" . $this->player_id . "_8",
                    'nb' => $nbmeeplesLeft
                ));
                break;
            }
        }
    }
}
