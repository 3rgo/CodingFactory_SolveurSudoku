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
        $missingFileGrid = SudokuGrid::loadFromFile("a");
        $this->assertNull($missingFileGrid);
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
        foreach(range(1,4) as $index){
            $newGrid = SudokuGrid::loadFromFile(realpath(rtrim(__DIR__, '/') . "/../grids/grid$index.json"));
            $this->assertFalse($newGrid->isFilled());
        }
    }

    public function testIsValid(): void
    {
        $this->assertTrue($this->grid->isValid());
        foreach(range(1,4) as $index){
            $newGrid = SudokuGrid::loadFromFile(realpath(rtrim(__DIR__, '/') . "/../grids/grid$index.json"));
            $this->assertFalse($newGrid->isValid());
        }
    }

    public function testIsValueValidForPosition(): void
    {
        $grid = SudokuGrid::loadFromFile(realpath(rtrim(__DIR__, '/') . "/../grids/grid1.json"));
        $this->assertTrue($grid->isValueValidForPosition(0,0,1));
        $this->assertFalse($grid->isValueValidForPosition(0,0,2));
    }

    public function testGetNextRowColumn(): void
    {
        foreach(range(0, 9) as $i){
            $randomR = random_int(0, 8);
            $randomC = random_int(0, 8);
            list($r, $c) = $this->grid->getNextRowColumn($randomR, $randomC);
            $expectedC = ($randomC+1) % 9;
            $expectedR = $randomR + ($expectedC == 0 ? 1 : 0);
            $this->assertEquals($expectedR, $r);
            $this->assertEquals($expectedC, $c);
        }
    }
}