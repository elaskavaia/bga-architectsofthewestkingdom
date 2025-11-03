<?php 

class apprentice1 extends apprentice
{
    public $skill = CAR;
    public $virtue = 1;
    
    public function getAdditionalBonus($location)
    {
        if($location == "cathedral")
        {
            return B;
        }
        return 0;
    }
}