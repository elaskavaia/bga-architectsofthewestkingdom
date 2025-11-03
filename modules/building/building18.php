<?php 

class building18 extends building
{
    public $vp = 10;
    public $cost = 2*C+4*W+G+2*M;
    public $requirement = CAR+TIL+MAS;
    
    public function instantFinal($player)
    {
        $nb = $player->resources[GOLD];
        
        if($player->type == 1)
        {
            $nb += $player->resources[MARBLE];
        }
        
        $player->gainDirect(intdiv($nb,2), VIRTUE, "building".$this->card_id);
    }
}