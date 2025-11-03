<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * ArchitectsOfTheWestKingdom implementation : © <Nicolas Gocel> <nicolas.gocel@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 * 
 * states.inc.php
 *
 * ArchitectsOfTheWestKingdom game states description
 *
 */

if ( !defined('STATE_PLAYER_TURN') )
{
    define("STATE_SETUP",2);
    define("STATE_DRAFT",3);
    define("STATE_PLAYER_DRAFT",4);
    define("STATE_PENDING",5);
    define("STATE_PLAYER_TURN",6);
    define("STATE_FAKE",7);
    define("STATE_END_GAME",99);
}

 
$machinestates = array(

    // The initial state. Please do not modify.
    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => 2 )
    ),
    
    // Note: ID=2 => your first state

    STATE_SETUP => array(
        "name" => "setup",
        "description" => '',
        "type" => "game",
        "action" => "stSetup",
        "transitions" => array("next" => STATE_PENDING )
    ),
    
    STATE_PLAYER_DRAFT => array(
        "name" => "playerDraft",
        "description" => clienttranslate('Initial draft : Other players must keep one building'),
        "descriptionmyturn" => clienttranslate('Initial draft : ${you} must keep one building'),
        'type' => 'multipleactiveplayer',
        "args" => "argDraft",
        "possibleactions" => array( "select"),
        "transitions" => array( "next" => STATE_DRAFT, "same" => STATE_DRAFT, "zombiePass" => STATE_DRAFT)
    ),
    
    STATE_DRAFT => array(
        "name" => "setup",
        "description" => '',
        "type" => "game",
        "action" => "stDraft",
        "transitions" => array("next" => STATE_PENDING, "draft" => STATE_PLAYER_DRAFT )
    ),
    
    STATE_PENDING=> array(
        "name" => "pending",
        "description" => '',
        "type" => "game",
        "action" => "stPending",
        "updateGameProgression" => true,
        "transitions" => array("end" => STATE_END_GAME, "player"=>STATE_PLAYER_TURN, "same" => STATE_PENDING, "draft" => STATE_PLAYER_DRAFT)
    ),
    
    STATE_PLAYER_TURN => array(
        "name" => "playerTurn",
        "description" => clienttranslate('${actplayer} must take an action or Pass'),
        "descriptionmyturn" => clienttranslate('${you} must take an action or pass'),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => array( "select"),
        "transitions" => array( "next" => STATE_PENDING, "zombiePass" => STATE_PENDING)
    ),
    
    
    STATE_FAKE => array(
        "name" => "fake",
        "description" => clienttranslate('FAKE END'),
        "descriptionmyturn" => clienttranslate('FAKE END'),
        "type" => "activeplayer",
        "possibleactions" => array( "select"),
        "transitions" => array( "next" => STATE_PENDING, "zombiePass" => STATE_PENDING)
    ),
    
   
    // Final state.
    // Please do not modify (and do not overload action/args methods).
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )

);



