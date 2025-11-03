<?php 

class building13 extends building
{
    public $vp = 3;
    public $cost = 2*C+3*W+G;
    public $requirement = 0;
    
    
    public function getExtraVP()
    {
        return ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select count(*) from building where card_location = 'cards{$this->player_id}'");
    }
}