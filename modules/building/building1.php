<?php 

class building1 extends building
{
    public $vp = 3;
    public $cost = 4*C+2*W;
    public $requirement = 0;
    
    public function instant($player)
    {
        $player->gain("building".$this->card_id,null,4*S);
    }
}