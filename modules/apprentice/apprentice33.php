<?php 

class apprentice33 extends apprentice
{
    public $skill = CAR;
    public $virtue = -1;
    
    public function getAdditionalBonus($location)
    {
        if($location == "blackmarketa"
            || $location == "blackmarketb"
            || $location == "blackmarketc")
        {
            return W;
        }
        return 0;
    }
}