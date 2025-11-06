<?php 

class building35 extends building
{
    public $vp = 5;
    public $cost = 4*C+2*W+G;
    public $requirement = MAS;
    public $virtue = -1;
    
    public function getExtraVP()
    {
        return ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select count(*) from debt where player_id = {$this->player_id} and paid = 1");
    }
}