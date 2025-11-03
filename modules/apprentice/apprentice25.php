<?php 

class apprentice25 extends apprentice
{
    public $skill = TIL;
    public $virtue = -1;
    
    
    public function getAdditionalBonus($location)
    {
        if($location == "taxstand")
        {
            return 1;
        }
        return 0;
    }
        
}