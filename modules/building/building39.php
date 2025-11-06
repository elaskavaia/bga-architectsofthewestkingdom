<?php 

class building39 extends building
{
    public $vp = 4;
    public $cost = 6*C+W;
    public $requirement = 0;
    
    public function instantFinal($player)
    {
        $nb = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select count(*) from debt where player_id = {$this->player_id} and paid = 1");
        $player->gain("building".$this->card_id,null,intdiv($nb,2)*V);
    }
}