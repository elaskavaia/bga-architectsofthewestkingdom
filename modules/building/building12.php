<?php 

class building12 extends building
{
    public $vp = 7;
    public $cost = 2*C+4*W+G;
    public $requirement = CAR;
    
    public function getExtraVP()
    {
        $own = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select res2 from player where player_id = {$this->player_id}");
        $other = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select max(res2) from player where player_id <> {$this->player_id}");
        return ($own>$other)?2:0;
    }
}