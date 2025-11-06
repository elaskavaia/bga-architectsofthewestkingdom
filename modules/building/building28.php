<?php 

class building28 extends building
{
    public $vp = 10;
    public $cost = S+G+3*M;
    public $requirement = CAR+TIL;
    public $virtue = -2;
    
    public function getExtraVP()
    {
        return -1*ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select count(*) from debt where player_id = {$this->player_id} and paid = 0");
    }
}