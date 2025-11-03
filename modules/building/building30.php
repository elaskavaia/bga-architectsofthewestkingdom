<?php 

class building30 extends building
{
    public $vp = 12;
    public $cost = 5*W+2*S+2*G;
    public $requirement = CAR+TIL+MAS;
    public $virtue = -2;
    
    public function getExtraVP()
    {
        return intdiv(intval(ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select count(*) from worker where location = 'prison_{$this->player_id}'")),3);
    }
    
}