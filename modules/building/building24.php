<?php 

class building24 extends building
{
    public $vp = 14;
    public $cost = 4*S+2*G+2*M;
    public $requirement = CAR+TIL+MAS;
    public $virtue = -1;
    
    public function instantFinal($player)
    {
        $player->pay(null,"building".$this->card_id,2*V);
    }

}