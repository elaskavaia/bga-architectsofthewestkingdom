<?php 

class building37 extends building
{
    public $vp = 7;
    public $cost = C+3*W+S+G;
    public $requirement = CAR;
    
    public function instantFinal($player)
    {
        $nb = $player->resources[MARBLE];        
        
        if($player->type == 1)
        {
            $nb += $player->resources[GOLD];
        } 
        $player->gainDirect(intdiv($nb,2), VIRTUE, "building".$this->card_id);
    }
}