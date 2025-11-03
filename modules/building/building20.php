<?php 

class building20 extends building
{
    public $vp = 5;
    public $cost = 3*C+3*S;
    public $requirement = 0;
    public $virtue = 1;
    
    public function instant($player)
    {
        $debt = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM debt WHERE player_id = {$this->player_id} and paid = 0 limit 1");
        if($debt != null)
        {
            ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "building20");
        }
    }
    
    function arg($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may discard an unpaid debt');
        $ret['titleyou'] = clienttranslate('${you} may discard an unpaid debt');
        
        $player = new ARCPlayer($this->player_id);        
        $debt = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM debt WHERE player_id = {$this->player_id} and paid = 0 limit 1");
        if($debt != null)
        {
            $ret['selectable'][] = 'Discard';
            $ret['buttons'][] = 'Discard';
        }
        $ret['buttons'][] = 'Skip';
        $ret['selectable']['Skip'] = array();
        
        return $ret;
    }
    
    function do($parg1, $parg2, $varg1, $varg2)
    {
        if($varg1 != "Skip")
        {
            $debt = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM debt WHERE player_id = {$this->player_id} and paid = 0 limit 1");
            if($debt != null)
            {
                
                $player = new ARCPlayer($this->player_id);                   
                
                ArchitectsOfTheWestKingdom::$instance->DbQuery("delete from debt where id = {$debt['id']}");
                ArchitectsOfTheWestKingdom::$instance->notifyAllPlayers( "counter", clienttranslate('${player_name} destroys one unpaid debt'), array(
                    'id' => "res_".$player->player_id."_13",
                    'nb' => self::getUniqueValueFromDB("SELECT count(*) FROM debt WHERE player_id = {$this->player_id} and paid = 0"),
                    'player_id' => $player->player_id,
                    'player_name' => $player->player_name,
                ) );
                
                $player->updateVP();
            }
        }
    }
}