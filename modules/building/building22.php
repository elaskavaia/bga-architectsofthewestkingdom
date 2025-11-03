<?php 

class building22 extends building
{
    public $vp = 3;
    public $cost = 4*C+W;
    public $requirement = 0;    
    
    public function instant($player)
    {
        ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "gain","building".$this->card_id,json_encode([G,M]));
    }
}