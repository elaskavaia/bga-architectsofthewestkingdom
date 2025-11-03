<?php 

class apprentice8 extends apprentice
{
    public $skill = CAR;
    public function getAdditionalBonus($location)
    {
        if($location == "debtrefund")
        {
            return 2*W;
        }
        return 0;
    }
}