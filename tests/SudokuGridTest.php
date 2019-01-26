<?php

use PHPUnit\Framework\TestCase;

final class SudokuGridTest extends TestCase
{
    protected $grid;
    protected $data;

    protected function setUp() {
        $this->data = json_decode(
            file_get_contents(
                realpath(rtrim(__DIR__, '/') . '/..' . '/grids' . '/full.json')
            )
        );
        $this->grid = new SudokuGrid($this->data);
    }


    public function testValidData(): void
    {
        $this->assertEquals($this->data, $this->grid->data);
    }

    public function testLoadFromFile(): void
    {
        $newGrid = SudokuGrid::loadFromFile(realpath(rtrim(__DIR__, '/') . '/../grids/full.json'));
        $this->assertEquals($this->grid->data, $newGrid->data);
    }

    public function testRows(): void
    {
        foreach(range(0,8) as $index){
            $this->assertEquals($this->data[$index], $this->grid->row($index));
        }
    }

    public function testColumns(): void
    {
        foreach(range(0,8) as $index){
            $computedColumn = [];
            foreach($this->data as $row){
                $computedColumn[] = $row[$index];
            }
            $this->assertEquals($computedColumn, $this->grid->column($index));
        }
    }

    public function testSquares(): void
    {
        foreach(range(0,8) as $squareIndex){
            $computedSquare = [];
            foreach($this->data as $rowIndex => $row){
                foreach($row as $columnIndex => $cell){
                    if($rowIndex >= (intdiv($squareIndex,3)*3)
                        && $rowIndex <= ((intdiv($squareIndex,3)*3)+2)
                        && $columnIndex >= (($squareIndex % 3) * 3)
                        && $columnIndex <= ((($squareIndex % 3) * 3) + 2)){
                        $computedSquare[] = $cell;
                    }
                }
            }
            $this->assertEquals($computedSquare, $this->grid->square($squareIndex));
        }
    }

    public function testIsFilled(): void
    {
        $this->assertTrue($this->grid->isFilled());
        $newGrid = SudokuGrid::loadFromFile(realpath(rtrim(__DIR__, '/') . '/../grids/level1.json'));
        $this->assertFalse($newGrid->isFilled());
    }

    public function testIsValid(): void
    {
        $this->assertTrue($this->grid->isValid());
        $newGrid = SudokuGrid::loadFromFile(realpath(rtrim(__DIR__, '/') . '/../grids/level1.json'));
        $this->assertFalse($newGrid->isValid());
    }
}