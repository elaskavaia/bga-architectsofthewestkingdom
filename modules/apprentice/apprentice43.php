<?php 

class apprentice43 extends apprentice
{
    public $skill = CAR;
    public $virtue = -1;
    
    public function addPending($location)
    {
        if($location == "blackmarketreset")
        {
            $player = new ARCPlayer($this->player_id);
            $player->gain("apprentice".$this->card_id, null, 2*SI);
        }
    }
}