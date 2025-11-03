<?php 

class building33 extends building
{
    public $vp = 3;
    public $cost = 4*C+2*S;
    public $requirement = 0;
    
    public function instant($player)
    {
        $player->gain("building".$this->card_id,null,4*W);
    }
}