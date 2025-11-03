<?php 

class building32 extends building
{
    public $vp = 6;
    public $cost = 3*W+S+2*M;
    public $requirement = TIL;
    
    public function getExtraVP()
    {
        return intdiv(ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select virtue from player where player_id = {$this->player_id}"),4);
    }
}