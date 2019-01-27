<?php

interface SolverInterface {
    public static function solve(SudokuGrid $grid, int $rowIndex, int $columnIndex): ?SudokuGrid;
}