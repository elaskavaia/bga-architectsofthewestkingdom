<?php 

class building21 extends building
{
    public $vp = 6;
    public $cost = 3*C+2*S+G;
    public $requirement = TIL;
    
    public function getExtraVP()
    {
        $own = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select res1 from player where player_id = {$this->player_id}");
        $other = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select max(res1) from player where player_id <> {$this->player_id}");
        return ($own>$other)?2:0;
    }
}