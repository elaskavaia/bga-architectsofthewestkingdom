<?php 

class building15 extends building
{
    public $vp = 10;
    public $cost = 2*C+6*W+2*G;
    public $requirement = CAR+TIL+MAS;
    
    public function instant($player)
    {
        ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "building15",null,null,-1);
    }
    
    function arg($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may capture or release workers');
        $ret['titleyou'] = clienttranslate('${you} may capture or release workers');
        
        $player = new ARCPlayer($this->player_id);
        
        $ret['selectable'] =  $player->argtowncenter(null, null)['selectable'];
        if(ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select count(*) from worker where location like 'prison_%' and player_id={$this->player_id}") > 0)
        {
            $ret['selectable']["actguardhouse3"] = array();
        }
        
        $ret['buttons'][] = 'Skip';
        $ret['selectable']['Skip'] = array();
        
        return $ret;
    }
    
    function do($parg1, $parg2, $varg1, $varg2)
    {
        if($varg1 != "Skip")
        {
            $player = new ARCPlayer($this->player_id);
            
            if ($varg1 == "actguardhouse3") {
                $player->guardhouse3($parg1, $parg2, "nocost", $varg2);
            }
            else
            {
                $workerId = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
                $worker = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM worker WHERE id=".$workerId);
                $player->towncenter($parg1, $parg2, $varg1, $varg2);                
            }
        }
    }
}