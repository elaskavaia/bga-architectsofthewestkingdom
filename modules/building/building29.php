<?php 

class building29 extends building
{
    public $vp = 8;
    public $cost = C+3*W+2*S;
    public $requirement = CAR+MAS;
    public $virtue = -2;
    
    public function instant($player)
    {
        $player->guardhouse2();
    }
}