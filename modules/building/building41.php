<?php 

//         	41 : {"name":_("Crane"), "most":false, "per":false, "tooltip":_("Immediately place another Worker in the Guildhall to either construct another Building or advance work on the Cathedral.") }, 
       
class building41 extends building
{
    public $vp = 3;
    public $cost = 3*W+S;
    
    public function instant($player)
    {
        $arg = $player->argguildhall(null,null);
        if(count($arg['selectable'])>0)
        {
            $worker_id = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select min(id) from worker where player_id = {$player->player_id} and location like 'reserve%'");
            if($worker_id != null)
            {
                ArchitectsOfTheWestKingdom::$instance->DbQuery("update worker set location = 'guildhall', location_arg=0 where id = {$worker_id}");
                $worker = ArchitectsOfTheWestKingdom::$instance->getObjectFromDB("SELECT * FROM worker WHERE id = {$worker_id}");
                ArchitectsOfTheWestKingdom::$instance->notify->all( "move", '', array(
                    'mobile' => "worker_".$worker['id'],
                    'parent' => $worker['location'],
                    'position' => 'last'
                ) ); 
                $nbmeeplesLeft = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "select count(*) from worker where player_id = {$player->player_id} and  location like 'reserve%'");
                ArchitectsOfTheWestKingdom::$instance->notify->all( "counter", clienttranslate('${player_name} places a worker on ${location}'), array(
                    'player_id' => $player->player_id,
                    'player_name' => $player->player_name,
                    'id' => "res_".$player->player_id."_8",
                    'nb' => $nbmeeplesLeft,
                    'location' => "guildhall"
                ) );
                
                $player->guildhallSelect($worker_id);
            } else {
                ArchitectsOfTheWestKingdom::$instance->notify->all("message",clienttranslate('${player_name} skips bonus: no workers available'));
            }
        }
    }
}