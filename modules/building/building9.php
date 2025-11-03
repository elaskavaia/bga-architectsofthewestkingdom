<?php 

class building9 extends building
{
    public $vp = 11;
    public $cost = 4*C+2*W+3*S+2*M;
    public $requirement = CAR+TIL+MAS;
    public $virtue = 1;
    
    public function getExtraVP()
    {
        $own = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select count(*) from debt player where player_id = {$this->player_id} and paid = 0");
        return ($own==0)?2:0;
    }
}