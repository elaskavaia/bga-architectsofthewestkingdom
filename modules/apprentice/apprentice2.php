<?php 

class apprentice2 extends apprentice
{
    public $skill = TIL;
    public $virtue = 1;
    
    public function getAdditionalBonus($location)
    {
        if($location == "cathedral")
        {
            return 2*C;
        }
        return 0;
    }
}