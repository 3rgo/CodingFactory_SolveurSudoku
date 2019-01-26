<?php

class SudokuGrid
{
    public $data;

    public static function loadFromFile($filepath){
        return new SudokuGrid(json_decode(file_get_contents($filepath)));
    }

    public function __construct(array $data){
        $this->data = $data;
    }

    public function row(int $rowIndex): array {
        if($rowIndex < 0 || $rowIndex > 8){
            throw new InvalidArgumentException("Invalid row index given : $rowIndex (Must be between 0 and 8)");
        }
        return $this->data[$rowIndex];
    }

    public function column(int $columnIndex): array {
        if($columnIndex < 0 || $columnIndex > 8){
            throw new InvalidArgumentException("Invalid column index given : $columnIndex (Must be between 0 and 8)");
        }
        return array_map(function($row) use($columnIndex) {
            return $row[$columnIndex];
        }, $this->data);
    }

    public function square(int $squareIndex): array {
        if($squareIndex < 0 || $squareIndex > 8){
            throw new InvalidArgumentException("Invalid square index given : $squareIndex (Must be between 0 and 8)");
        }
        return  array_merge(
            ...array_map(
                function($row) use ($squareIndex) {
                    return array_slice($row, ($squareIndex % 3) * 3, 3);
                },
                array_slice($this->data, intdiv($squareIndex, 3) * 3, 3)
            )
        );
    }

    public function display(): string {
        return implode(PHP_EOL, array_map(function($row){
            return implode(" ", $row);
        }, $this->data));
    }

    public function isValid(): bool {
        if(!$this->isFilled()){ return false; }

        $reference = range(1, 9);

        foreach(range(0, 8) as $index){
            if(array_diff($reference, $this->row($index))
                || array_diff($reference, $this->column($index))
                || array_diff($reference, $this->square($index))) {
                return false;
            }
        }
        return true;
    }

    public function isFilled(): bool {
        return !in_array(0, array_merge(...$this->data));
    }
}