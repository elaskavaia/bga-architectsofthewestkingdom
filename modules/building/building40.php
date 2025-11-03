<?php 

class building40 extends building
{
    public $vp = 6;
    public $cost = 2*W+G+M;
    public $requirement = TIL;
    
    public function getExtraVP()
    {
        $own = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select res6 from player where player_id = {$this->player_id}");
        $other = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select max(res6) from player where player_id <> {$this->player_id}");
        return ($own>$other)?2:0;
    }
}