<?php 

class building43 extends building
{
    public $vp = 4;
    public $cost = C+5*W+S;
    
    public function instantFinal($player)
    {
        $nb =  ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select count(*) from building where card_location = 'cards{$this->player_id}'");
        $player->gainDirect(intdiv($nb,2), VIRTUE, "building".$this->card_id);
    }
    
}