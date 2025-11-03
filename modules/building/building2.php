<?php 

class building2 extends building
{
    public $vp = 4;
    public $cost = 2*W+2*G;
    public $requirement = 0;
    public $virtue = 1;    
    
    public function getExtraVP()
    {
        $own = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select cathedral from player where player_id = {$this->player_id}");
        $other = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select max(cathedral) from player where player_id <> {$this->player_id}");
        
        return ($own>$other)?2:0;
    }
}