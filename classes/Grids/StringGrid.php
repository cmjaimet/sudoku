<?php
// namespace Games\Sudoku\Grid;

require_once( 'classes/Grid.php' );

class StringGrid extends Grid implements GridInterface {
	public $cells;

	function __construct( $filename, $puzzle_number ) {
		$grids_raw = file_get_contents( $filename );
		$this->create( $grids_raw, $puzzle_number );
	}

	public function create( $grids_raw, $puzzle_number ) {
		$puzzles = explode( "\n", $grids_raw );
		if ( false === isset( $puzzles[ $puzzle_number ] ) ) {
			die();
		}
		$puzzle_data = $puzzles[ $puzzle_number ];
		for ( $row = 0; $row < 9; $row ++ ) {
			for ( $col = 0; $col < 9; $col ++ ) {
				$cell_value = substr( $puzzle_data, 0, 1 );
				$puzzle_data = substr( $puzzle_data, 1, 100 );
				$this->cells[ $row ][ $col ] = new Cell( $cell_value );
			}
		}
	}

}
