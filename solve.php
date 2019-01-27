<?php

require_once "vendor/autoload.php";

/**
 * Affiche la chaîne suivie d’un retour à la ligne
 * @param $str Chaîne à afficher
 * @return void
 */
function println(string $str){
    echo $str . PHP_EOL;
}
/**
 * Efface l’écran de la console
 * @return void
 */
function cls(){
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
        system('cls');
    } else {
        system('clear');
    }
}

cls();
$dir = __DIR__ . '/grids';
$files = array_values(array_filter(scandir($dir), function($f){ return $f != '.' && $f != '..'; }));

foreach($files as $file){
    $filepath = realpath($dir . '/' . $file);
    println("Chargement du fichier $file");
    $grid = SudokuGrid::loadFromFile($filepath);
    println($grid->display());
    $startTime = microtime(true);
    println("Début de la recherche de solution");
    $solvedGrid = SudokuSolver::solve($grid);
    if($solvedGrid === null){
        println("Echec, grille insolvable");
    } else {
        println("Reussite :");
        println($solvedGrid->display());
    }

    $duration = round((microtime(true) - $startTime) * 1000);
    println("Durée totale : $duration ms");
    println("--------------------------------------------------");
}