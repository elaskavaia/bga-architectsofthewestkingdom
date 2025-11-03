<?php 

class building4 extends building
{
    public $vp = 6;
    public $cost = 3*W+2*S+G;
    public $requirement = CAR;
    
    public function instant($player)
    {
        ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "pickApprentice");
        ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "discardApprentice");  
    }
}