<?php 

class building7 extends building
{
    public $vp = 7;
    public $cost = W+3*S+2*G;
    public $requirement = MAS;
    
    public function instantFinal($player)
    {
        $nb = $player->resources[STONE] + $player->resources[WOOD] +$player->resources[CLAY];
        $player->gainDirect(intdiv($nb,4), VIRTUE, "building".$this->card_id);
    }
}