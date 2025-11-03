<?php 

class apprentice3 extends apprentice
{
    public $skill = MAS;
    public $virtue = 1;
    
    public function getAdditionalBonus($location)
    {
        if($location == "cathedral")
        {
            return 2*SI;
        }
        return 0;
    }
}