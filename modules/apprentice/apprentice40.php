<?php 

class apprentice40 extends apprentice
{
    public $skill = CAR;
    public function getAdditionalBonus($location)
    {
        if($location == "forest")
        {
            return 1;
        }
        return 0;
    }
}