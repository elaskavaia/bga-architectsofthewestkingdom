<?php 

class apprentice30 extends apprentice
{
    public $skill = MAS;
    public function getAdditionalBonus($location)
    {
        if($location == "quarry")
        {
            return 1;
        }
        return 0;
    }
}