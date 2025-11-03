<?php 

class building3 extends building
{
    public $vp = 7;
    public $cost = 2*C+4*S+M;
    public $requirement = MAS;
    
    public function getExtraVP()
    {
        $own = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select res3 from player where player_id = {$this->player_id}");
        $other = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select max(res3) from player where player_id <> {$this->player_id}");
        return ($own>$other)?2:0;
    }
}