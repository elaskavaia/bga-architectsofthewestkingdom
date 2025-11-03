<?php 

class building16 extends building
{
    public $vp = 7;
    public $cost = C+2*S+G;
    public $requirement = 0;
    public $virtue = -1;
        
    public function instant($player)
    {
        $player->pay(null, "building".$this->card_id,D);
    }
}