<?php 

class building14 extends building
{
    public $vp = 9;
    public $cost = 3*W+5*S+G;
    public $requirement = CAR+MAS;
    
    public function instant($player)
    {
        ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "towncenter",null,null,-1);
    }
}