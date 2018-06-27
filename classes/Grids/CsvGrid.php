<?php
// namespace Games\Sudoku\Grid;

require_once( 'classes/Grid.php' );

class Grid implements GridInterface {
	public $cells;

	function __construct( $filename, $puzzle_number ) {
		$grids_raw = file_get_contents( $filename );
		$this->create( $grids_raw );
	}

	public function create( $grid_raw, $puzzle_number = 0 ) {
		$grid_rows = explode( "\n", $grid_csv );
		for ( $row = 0; $row < 9; $row ++ ) {
			$grid_row = explode( ',', $grid_rows[ $row ] );
			for ( $col = 0; $col < 9; $col ++ ) {
				$this->cells[ $row ][ $col ] = new Cell( $grid_row[ $col ] );
			}
		}
	}

}
