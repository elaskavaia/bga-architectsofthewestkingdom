<?php 

class building36 extends building
{
    public $vp = 9;
    public $cost = 2*W+3*S+2*M;
    public $requirement = TIL+MAS;
    public $virtue = 1;
    
    public function instant($player)
    {
        $players = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb( "select * from player order by player_no desc" );
        foreach($players as $player)
        {
            $obj = new ARCPlayer($player['player_id']);
            $obj->gain(null,"prison",V);            
        }
    }
}