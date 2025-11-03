<?php 

class building26 extends building
{
    public $vp = 12;
    public $cost = 3*C+4*S+G+2*M;
    public $requirement = CAR+TIL+MAS;
    public $virtue = -2;  
    
    public function instant($player)
    {
        ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "towncenter",null,null,-1);
        ArchitectsOfTheWestKingdom::$instance->addPending($this->player_id, "towncenter",null,null,-1);
    }
}