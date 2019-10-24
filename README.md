# CodingFactory_SolveurSudoku
Inspiré de https://github.com/ryanmk54/recursive-sudoku-solver

## Architecture des dossiers
- `grids` : Contient des grilles d’exemple de plusieurs difficultés, ainsi qu’une grille déjà complétée
- `src` : Contient les classes et interfaces
- `tests` : Contient les tests unitaires de la classe SudokuGrid

## Mise en place du projet
 - Dans le terminal, se positionner dans le dossier concerné, et exécuter `composer install` afin de télécharger PHPUnit
 - Pour exécuter les tests unitaires et vérifier que votre classe SudokuGrid est correcte, exécuter cette commande à la racine du dossier : `./vendor/bin/phpunit --bootstrap vendor/autoload.php tests`
 - Executer `solve.php` pour lancer la résolution