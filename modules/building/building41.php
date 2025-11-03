<?php 

class building41 extends building
{
    public $vp = 3;
    public $cost = 3*W+S;
    
    public function instant($player)
    {
        ;
        $arg = $player->argguildhall(null,null);
        if(count($arg['selectable'])>1)
        {
            $worker_id = self::getUniqueValueFromDB( "select min(id) from worker where player_id = {$player->player_id} and location like 'reserve%'");
            self::DbQuery("update worker set location = 'guildhall', location_arg=0 where id = {$worker_id}");
            $worker = self::getObjectFromDB("SELECT * FROM worker WHERE id = {$worker_id}");
            ArchitectsOfTheWestKingdom::$instance->notifyAllPlayers( "move", '', array(
                'mobile' => "worker_".$worker['id'],
                'parent' => $worker['location'],
                'position' => 'last'
            ) ); 
            $nbmeeplesLeft = self::getUniqueValueFromDB( "select count(*) from worker where player_id = {$player->player_id} and  location like 'reserve%'");
            ArchitectsOfTheWestKingdom::$instance->notifyAllPlayers( "counter", clienttranslate('${player_name} places a worker on ${location}'), array(
                'player_id' => $player->player_id,
                'player_name' => $player->player_name,
                'id' => "res_".$player->player_id."_8",
                'nb' => $nbmeeplesLeft,
                'location' => "guildhall"
            ) );
            
            $player->guildhallSelect($worker_id);
        }
    }
}