<?php 

class building10 extends building
{
    public $vp = 11;
    public $cost = 3*C+4*W+G+M;
    public $requirement = CAR+TIL+MAS;
    public $virtue = -1;
    
    public function instant($player)
    {
        $players = self::getCollectionFromDb( "select * from player order by player_no desc" );
        foreach($players as $player)
        {
            $nbmeeplesLeft = self::getUniqueValueFromDB( "select count(*) from worker where player_id = {$player['player_id']} and  location = 'prison'");
            if($nbmeeplesLeft>=3)
            {
                $obj = new ARCPlayer($player['player_id']);
                $obj->pay(null,"prison",V);
            }
        }
    }
}