<?php 

class apprentice41 extends apprentice
{
    public $skill = TIL;
    public function getAdditionalBonus($location)
    {
        if($location == "guardhouse1")
        {
            $player = new ARCPlayer($this->player_id);
            $player->gain("apprentice".$this->card_id, null, G);
        }
    }
}