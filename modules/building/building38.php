<?php 

class building38 extends building
{
    public $vp = 9;
    public $cost = 3*W+3*S+2*M;
    public $requirement = TIL+MAS;
    
    public function instant($player)
    {
        $nb = intdiv(ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select virtue from player where player_id = {$this->player_id}"),4);
        $player->gain("building".$this->card_id,null,$nb*G);
    }
}