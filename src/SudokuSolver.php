<?php

class SudokuSolver implements SolverInterface
{
    public static function solve(SudokuGrid $originalGrid, int $rowIndex = 0, int $columnIndex = 0): ?SudokuGrid {
        // Si la grille est déjà valide, on la retourne
        if($originalGrid->isValid()){
            return $originalGrid;
        }
        // Copie de l'objet original (nécessaire pour pouvoir faire machine arrière en cas d'impasse)
        $grid = new SudokuGrid($originalGrid->data);
        list($nextRow, $nextColumn) = $grid->getNextRowColumn($rowIndex, $columnIndex);

        if($grid->get($rowIndex, $columnIndex) === 0)
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

                if($grid->isValueValidForPosition($rowIndex, $columnIndex, $currentNumber)) {
                    // Si on peut placer le numéro dans la cellule, on le place
                    $grid->set($rowIndex, $columnIndex, $currentNumber);

                    // Si la grille est valide (on vient de placer le dernier numéro manquant), on la retourne
                    if($grid->isValid()) {
                        // La grille est terminée
                        return $grid;
                    } else {
                        // On teste le résultat, car s'il est non-nul (une solution a été trouvée)
                        // On peut s'arrêter ici et retourner le résultat
                        // Sinon, ca veut dire que le numéro courant ne permet pas d'obtenir de solution
                        $result = self::solve($grid, $nextRow, $nextColumn);
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
            return self::solve($grid, $nextRow, $nextColumn);
        }
    }
}
