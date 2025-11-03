<?php 

class building42 extends building
{
    public $vp = 12;
    public $cost = 4*C+W+2*S+2*M;
    public $requirement = CAR+TIL+MAS;
    public $virtue = -1;
    
    public function instant($player)
    {
        ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "discardApprentice","noskip");
    }
}