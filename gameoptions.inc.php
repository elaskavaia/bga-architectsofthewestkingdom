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
 * gameoptions.inc.php
 *
 * ArchitectsOfTheWestKingdom game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in architectsofthewestkingdom.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */


$game_options = array(
    
    100 => array(
        'name' => totranslate('Player board side'),
        'values' => array(
            1 => array( 'name' => totranslate('Symmetric Side'), 'description' => totranslate('Play with identical player boards') ),
            2 => array( 'name' => totranslate('Asymmetric Side'), 'description' => totranslate('Play with board with unique feature'), 'premium' => true, 'nobeginner' => true ),
        ),
        'default' => 1
    ),
    
    101 => array(
        'name' => totranslate('Live scoring'),
        'values' => array(
            1 => array( 'name' => totranslate('Off') ),
            2 => array( 'name' => totranslate('On') ),
        ),
        'default' => 1
    ),
    
);



