<?php 

class apprentice10 extends apprentice
{
    public $skill = CAR;
     public $virtue = -1;
     
     public function getCostReduction($location)
     {
         if($location == "blackmarketa"
             || $location == "blackmarketb"
             || $location == "blackmarketc")
         {
             return V;
         }
         return 0;
     }
}