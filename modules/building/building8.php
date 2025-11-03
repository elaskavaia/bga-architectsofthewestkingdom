<?php 

class building8 extends building
{
    public $vp = 6;
    public $cost = W+3*S+G;
    public $requirement = TIL;
    
    public function instant($player)
    {        
        ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "gain","building".$this->card_id,json_encode([G,3*SI]));
    }
}