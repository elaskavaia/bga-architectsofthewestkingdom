<?php 

class building23 extends building
{
    public $vp = 8;
    public $cost = 4*S+3*G;
    public $requirement = CAR+TIL;
    
    public function instant($player)
    {
        $player->gain("building".$this->card_id,null,2*B);
    }
}