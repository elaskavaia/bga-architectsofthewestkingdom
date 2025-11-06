<?php

//Gatekeeper
class apprentice9 extends apprentice
{
    public $skill = MAS;
    public $virtue = -1;

    public function addPending($location)
    {
        if ($location == "blackmarketreset") {
            $sql = "SELECT * from worker where location = 'prison' and player_id = {$this->player_id} LIMIT 2";
            $prisonners = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb($sql);

            $target = "reserve_" . $this->player_id;

            foreach ($prisonners as $worker) {
                ArchitectsOfTheWestKingdom::$instance->notify->all("move", '', array(
                    'mobile' => "worker_" . $worker['id'],
                    'parent' => "{$target}",
                    'position' => 'last'
                ));
                ArchitectsOfTheWestKingdom::$instance->DbQuery("update worker set location = '{$target}' where id = {$worker['id']}");

                $nbmeeplesLeft = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB("select count(*) from worker where player_id = {$this->player_id} and  location like 'reserve%'");
                ArchitectsOfTheWestKingdom::$instance->notify->all("counter", clienttranslate('${player_name} releases 1 worker from prison (Gatekeeper)'), array(
                    'player_id' => $this->player_id,
                    'id' => "res_" . $this->player_id . "_8",
                    'nb' => $nbmeeplesLeft
                ));
            }
        }
    }
}
