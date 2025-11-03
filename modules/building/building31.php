<?php 

class building31 extends building
{
    public $vp = 8;
    public $cost = 4*W+2*S+M;
    public $requirement = CAR+TIL;
    
    public function instant($player)
    {
        ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "building31");
    }
    
    function arg($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may discard a building card to gain 2 <div class="arcicon res4"></div>');
        $ret['titleyou'] = clienttranslate('${you} may discard a building card to gain 2 <div class="arcicon res4"></div>');
        
        $sql = "SELECT * from building where card_location = 'hand{$this->player_id}'";
        $buildings = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb( $sql );
        foreach($buildings as $building)
        {
            $ret['selectable']["building".$building['card_id']] = array();
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
            $player->discardBuilding("unique", $parg2, $varg1, $varg2);       
            $player->gain("lowerlane".$this->player_id,null,2*G);              
        }
    }
}