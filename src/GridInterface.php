<?php

interface GridInterface {
    /**
     * Charge un fichier en fournissant son chemin
     * @param string $filepath Chemin du fichier
     * @return SudokuGrid|null Une instance de la classe si le fichier existe et est valide, null sinon
     */
    public static function loadFromFile($filepath): ?SudokuGrid;

    /**
     * Instancie une grille à partir d'un tableau de données
     * @param array $data Tableau de données
     */
    public function __construct(array $data);

    /**
     * Retourne la valeur d'une cellule
     * @param int $rowIndex Index de ligne
     * @param int $columnIndex Index de colonne
     * @return int Valeur
     */
    public function get(int $rowIndex, int $columnIndex): int;

    /**
     * Affecte une valeur dans une cellule
     * @param int $rowIndex Index de ligne
     * @param int $columnIndex Index de colonne
     * @param int $value Valeur
     */
    public function set(int $rowIndex, int $columnIndex, int $value): void;

    /**
     * Retourne les données d'une ligne à partir de son index
     * @param int $rowIndex Index de ligne (entre 0 et 8)
     * @return array Chiffres de la ligne demandée
     */
    public function row(int $rowIndex): array;

    /**
     * Retourne les données d'une colonne à partir de son index
     * @param int $columnIndex Index de colonne (entre 0 et 8)
     * @return array Chiffres de la colonne demandée
     */
    public function column(int $columnIndex): array;

    /**
     * Retourne les données d'un bloc à partir de son index
     * L'indexation des blocs est faite de gauche à droite puis de haut en bas
     * @param int $squareIndex Index de bloc (entre 0 et 8)
     * @return array Chiffres du bloc demandé
     */
    public function square(int $squareIndex): array;

    /**
     * Génère l'affichage de la grille
     * @return string
     */
    public function display(): string;

    /**
     * Teste si la valeur peut être ajoutée aux coordonnées demandées
     * @param int $rowIndex Index de ligne
     * @param int $columnIndex Index de colonne
     * @param int $value Valeur
     * @return bool Résultat du test
     */
    public function isValueValidForPosition(int $rowIndex, int $columnIndex, int $value): bool;

    /**
     * Retourne les coordonnées de la prochaine cellule à partir des coordonnées actuelles
     * (Le parcours est fait de gauche à droite puis de haut en bas)
     * @param int $rowIndex Index de ligne
     * @param int $columnIndex Index de colonne
     * @return array Coordonnées suivantes au format [indexLigne, indexColonne]
     */
    public function getNextRowColumn(int $rowIndex, int $columnIndex): array;

    /**
     * Teste si la grille est valide
     * @return bool
     */
    public function isValid(): bool;

    /**
     * Teste si la grille est pleine
     * @return bool
     */
    public function isFilled(): bool;
}