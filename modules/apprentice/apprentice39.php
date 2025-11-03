<?php 

class apprentice39 extends apprentice
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