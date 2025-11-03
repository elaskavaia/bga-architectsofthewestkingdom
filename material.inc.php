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
 * material.inc.php
 *
 * ArchitectsOfTheWestKingdom game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */

if (!defined('SIDEA')) {
    
    define('SIDEA', 1);
    define('SIDEB', 2);
    
    define('CAR', 1);
    define('TIL', 2);
    define('MAS', 4);
    
    define('CLAY', 1);
    define('WOOD', 2);
    define('STONE', 3);
    define('GOLD', 4);
    define('MARBLE', 5);
    define('SILVER',6);
    define('TAX',7);
    define('VIRTUE',8);
    define('BUILDING',9);
    define('DEBT',10);
    
    define('C', 10);
    define('W', 100);
    define('S', 1000);
    define('G', 10000);
    define('M', 100000);
    define('SI', 1000000);
    define('TX', 10000000);
    define('V',  100000000);
    define('B',  1000000000);
    define('D',  10000000000);
    
}

$this->standardBox = [1,3,4,6,8];

$this->asymetricStart = [
    0 => [ "gain" => 6*SI+4*V+2*C, "gain2" => 0, "pay"=>0, "cards" => 0,"prisoners" => 6 ],
    1 => [ "gain" => 5*SI+5*V, "gain2" => 0, "pay"=>0, "cards" => 1, "prisoners" =>  4],
    2 => [ "gain" => 3*SI+2*V, "gain2" => 9*SI, "pay"=>0, "cards" => 2, "prisoners" => 10 ],
    3 => [ "gain" => 1*SI+3*V+M, "gain2" => 9*SI, "pay"=>D, "cards" => 0, "prisoners" => 8 ],
    4 => [ "gain" => 2*V+G, "gain2" => 9*V, "pay"=>0, "cards" => 1, "prisoners" => 0 ],
    5 => [ "gain" => SI+9*V+M+C, "gain2" => V, "pay"=>0, "cards" => 0, "prisoners" => 0 ],
    6 => [ "gain" => 4*SI+7*V+S, "gain2" => 0, "pay"=>0, "cards" => 0, "prisoners" => 0 ],
    7 => [ "gain" => 5*SI+6*V+S+W, "gain2" => 0, "pay"=>0, "cards" => 0, "prisoners" => 2 ],
    8 => [ "gain" => 3*SI+9*V+W, "gain2" => 0, "pay"=>0, "cards" => 0, "prisoners" => 0 ],
    9 => [ "gain" => 3*SI+8*V, "gain2" => 0, "pay"=>0, "cards" => 1, "prisoners" => 0 ],
];

$this->player_colors = [1=>"325089", 2=>"0a7134", 3=>"4e186f", 4=>"a3131e", 5=> "b2a80e"];
$this->virtue = [-9,-8,-7,-5,-3,-1,0,0,0,0,1,2,3,5,7];
$this->cathedralCosts = [[],[G],[4*S,3*S+W,2*S+2*W,S+3*W,4*W],[M], [8*S,7*S+W,6*S+2*W,5*S+3*W, 4*S+4*W,3*S+5*W,2*S+6*W,S+7*W, 8*W],[2*G+2*M]];
$this->cathedralVP = [0,2,4,7,12,20];
$this->cathedralSpots = [5,3,3,2,2,1];

$this->rewardsGain = [0,0,V+3*SI,V+2*W,V+2*S,V+B,2*V,V+3*SI,V+2*W,V+2*S,V+B,V+V,V+G];

$this->blackmarket1 = [
    1=> M,
    2=> G+G,
    3=> M+S,
    4=> G+W,
    5=> M+W,
    6=> M,
    7=> G+G,
    8=> M+S,
    9=> G+W,
    10=> M+W,
];

$this->blackmarket2 = [
    1=> M+M+W+W,
    2=> M+G+S+W,
    3=> M+M+S+S,
    4=> G+S+W+W,
    5=> M+W+S+S,
    6=> M+M+S+S,
    7=> M+W+S+S,
    8=> G+S+W+W,
    9=> M+G+S+W,
    10=> M+M+W+W,
];
