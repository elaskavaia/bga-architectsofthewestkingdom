<?php 

class building5 extends building
{
    public $vp = 8;
    public $cost = C+2*W+3*S;
    public $requirement = MAS;
    public $virtue = -2;
    
    public function instant($player)
    {
        $player->gain("building".$this->card_id,null,4*SI);
    }
}