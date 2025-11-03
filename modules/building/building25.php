<?php 

class building25 extends building
{
    public $vp = 7;
    public $cost = 2*W+2*S+3*G;
    public $requirement = MAS;
    
    public function instant($player)
    {
        $player->gain("building".$this->card_id,null,2*M);
    }
}