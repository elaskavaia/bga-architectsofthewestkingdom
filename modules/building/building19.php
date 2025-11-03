<?php 

class building19 extends building
{
    public $vp = 6;
    public $cost = 2*C+W+4*S;
    public $requirement = CAR;
    public $virtue = 1;
    
    public function instantFinal($player)
    { 
        $own = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select cathedral from player where player_id = {$this->player_id}");
        $other = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select max(cathedral) from player where player_id <> {$this->player_id}");
        if($own>$other)
        {
            $player->gain("building".$this->card_id,null,2*V);
        }
    }
    
}