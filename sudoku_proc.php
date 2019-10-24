<?php

/**
 * Charge un fichier en fournissant son chemin
 * @param string $filepath Chemin du fichier
 * @return array|null Un tableau si le fichier existe et est valide, null sinon
 */
function loadFromFile(string $filepath): ?array {
    if(!file_exists($filepath)){
        return null;
    }
    return json_decode(file_get_contents($filepath));
}

/**
 * Retourne la valeur d'une cellule
 * @param int $rowIndex Index de ligne
 * @param int $columnIndex Index de colonne
 * @return int Valeur
 */
function get(array $grid, int $rowIndex, int $columnIndex): int {
    return $grid[$rowIndex][$columnIndex];
}

/**
 * Affecte une valeur dans une cellule
 * @param int $rowIndex Index de ligne
 * @param int $columnIndex Index de colonne
 * @param int $value Valeur
 */
function set(array &$grid, int $rowIndex, int $columnIndex, int $value): void {
    $grid[$rowIndex][$columnIndex] = $value;
}

/**
 * Retourne les données d'une ligne à partir de son index
 * @param int $rowIndex Index de ligne (entre 0 et 8)
 * @return array Chiffres de la ligne demandée
 */
function row(array $grid, int $rowIndex): array {
    if($rowIndex < 0 || $rowIndex > 8){
        throw new InvalidArgumentException("Invalid row index given : $rowIndex (Must be between 0 and 8)");
    }
    return $grid[$rowIndex];
}

/**
 * Retourne les données d'une colonne à partir de son index
 * @param int $columnIndex Index de colonne (entre 0 et 8)
 * @return array Chiffres de la colonne demandée
 */
function column(array $grid, int $columnIndex): array {
    if($columnIndex < 0 || $columnIndex > 8){
        throw new InvalidArgumentException("Invalid column index given : $columnIndex (Must be between 0 and 8)");
    }
    return array_map(function($row) use($columnIndex) {
        return $row[$columnIndex];
    }, $grid);
}

/**
 * Retourne les données d'un bloc à partir de son index
 * L'indexation des blocs est faite de gauche à droite puis de haut en bas
 * @param int $squareIndex Index de bloc (entre 0 et 8)
 * @return array Chiffres du bloc demandé
 */
function square(array $grid, int $squareIndex): array {
    if($squareIndex < 0 || $squareIndex > 8){
        throw new InvalidArgumentException("Invalid square index given : $squareIndex (Must be between 0 and 8)");
    }
    return  array_merge(
        ...array_map(
            function($row) use ($squareIndex) {
                return array_slice($row, ($squareIndex % 3) * 3, 3);
            },
            array_slice($grid, intdiv($squareIndex, 3) * 3, 3)
        )
    );
}

/**
 * Génère l'affichage de la grille
 * @return string
 */
function display(array $grid): string {
    return implode(PHP_EOL, array_map(function($row){
        return implode(" ", $row);
    }, $grid));
}

/**
 * Teste si la valeur peut être ajoutée aux coordonnées demandées
 * @param int $rowIndex Index de ligne
 * @param int $columnIndex Index de colonne
 * @param int $value Valeur
 * @return bool Résultat du test
 */
function isValueValidForPosition(array $grid, int $rowIndex, int $columnIndex, int $value): bool {
    $squareIndex = (intdiv($rowIndex, 3) * 3) + intdiv($columnIndex, 3);
    return !in_array($value, row($grid, $rowIndex))
        && !in_array($value, column($grid, $columnIndex))
        && !in_array($value, square($grid, $squareIndex));
}

/**
 * Retourne les coordonnées de la prochaine cellule à partir des coordonnées actuelles
 * (Le parcours est fait de gauche à droite puis de haut en bas)
 * @param int $rowIndex Index de ligne
 * @param int $columnIndex Index de colonne
 * @return array Coordonnées suivantes au format [indexLigne, indexColonne]
 */
function getNextRowColumn(array $grid, int $rowIndex, int $columnIndex): array {
    $columnIndex++;
    if($columnIndex >= 9){
        $columnIndex = 0;
        $rowIndex++;
    }
    return [$rowIndex, $columnIndex];
}

/**
 * Teste si la grille est valide
 * @return bool
 */
function isValid(array $grid): bool {
    $reference = range(1, 9);

    foreach(range(0, 8) as $index){
        if(array_diff($reference, row($grid, $index))
            || array_diff($reference, column($grid, $index))
            || array_diff($reference, square($grid, $index))) {
            return false;
        }
    }
    return true;
}

function solve(array $originalGrid, int $rowIndex, int $columnIndex): ?array {
    // Si la grille est déjà valide, on la retourne
    if(isValid($originalGrid)){
        return $originalGrid;
    }
    // Copie de l'objet original (nécessaire pour pouvoir faire machine arrière en cas d'impasse)
    $grid = array_values($originalGrid);
    list($nextRow, $nextColumn) = getNextRowColumn($grid, $rowIndex, $columnIndex);

    if(get($grid, $rowIndex, $columnIndex) === 0)
    {
        // On prend un tableau contenant les valeurs possibles pour une cellule
        $validNumbers = range(1,9);

        while(true) {
            if(count($validNumbers) == 0){
                // Aucun numéro valide pour cette cellule
                // Donc la grille est insolvable
                return null;
            }

            // On prend le prochain numéro (peu importe l'ordre)
            $currentNumber = array_pop($validNumbers);

            if(isValueValidForPosition($grid, $rowIndex, $columnIndex, $currentNumber)) {
                // Si on peut placer le numéro dans la cellule, on le place
                set($grid, $rowIndex, $columnIndex, $currentNumber);

                // Si la grille est valide (on vient de placer le dernier numéro manquant), on la retourne
                if(isValid($grid)) {
                    // La grille est terminée
                    return $grid;
                } else {
                    // On teste le résultat, car s'il est non-nul (une solution a été trouvée)
                    // On peut s'arrêter ici et retourner le résultat
                    // Sinon, ca veut dire que le numéro courant ne permet pas d'obtenir de solution
                    $result = solve($grid, $nextRow, $nextColumn);
                    if($result !== null){
                        return $result;
                    }
                }
            }
        }
    }
    else
    {
        // La cellule actuelle est déjà remplie, on passe à la cellule suivante
        return solve($grid, $nextRow, $nextColumn);
    }
}

$dir = __DIR__ . '/grids';
$files = array_values(array_filter(scandir($dir), function($f){ return $f != '.' && $f != '..'; }));

foreach($files as $file){
    $filepath = realpath($dir . '/' . $file);
    echo("Chargement du fichier $file" . PHP_EOL);
    $grid = loadFromFile($filepath);
    echo(display($grid) . PHP_EOL);
    $startTime = microtime(true);
    echo("Début de la recherche de solution" . PHP_EOL);
    $solvedGrid = solve($grid, 0, 0);
    if($solvedGrid === null){
        echo("Echec, grille insolvable" . PHP_EOL);
    } else {
        echo("Reussite :" . PHP_EOL);
        echo(display($solvedGrid) . PHP_EOL);
    }

    $duration = round((microtime(true) - $startTime) * 1000);
    echo("Durée totale : $duration ms" . PHP_EOL);
    echo("--------------------------------------------------" . PHP_EOL);
}