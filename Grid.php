<?php
// namespace Games\Sudoku\Grid;

require_once( 'Cell.php' );

class Grid {
	public $cells;
	public $raw_data;

	function __construct( $filename, $puzzle_number ) {
		$grids_raw = file_get_contents( $filename );
		$this->get_grid_from_qqwing( $grids_raw, $puzzle_number );
	}

	private function get_grid_from_csv( $grid_raw ) {
		$grid_rows = explode( "\n", $grid_csv );
		for ( $row = 0; $row < 9; $row ++ ) {
			$grid_row = explode( ',', $grid_rows[ $row ] );
			for ( $col = 0; $col < 9; $col ++ ) {
				$this->cells[ $row ][ $col ] = new Cell( $grid_row[ $col ] );
			}
		}
	}

	private function get_grid_from_qqwing( $grids_raw, $puzzle_number ) {
		$puzzles = explode( "\n", $grids_raw );
		if ( false === isset( $puzzles[ $puzzle_number ] ) ) {
			die();
		}
		$puzzle_data = $puzzles[ $puzzle_number ];
		$this->raw_data = $puzzle_data;
		for ( $row = 0; $row < 9; $row ++ ) {
			for ( $col = 0; $col < 9; $col ++ ) {
				$cell_value = substr( $puzzle_data, 0, 1 );
				$puzzle_data = substr( $puzzle_data, 1, 100 );
				$this->cells[ $row ][ $col ] = new Cell( $cell_value );
			}
		}
	}

	public function get_cell_options( $row, $col ) {
		// get the array of possible integers for this cell
		$cell_options = $this->cells[ $row ][ $col ]->options;
		if ( 1 < sizeof( $cell_options ) ) {
			// there is more than one value possible for this cell
			$cell_options = $this->check_row( $row, $col, $cell_options );
			$cell_options = $this->check_col( $row, $col, $cell_options );
			$cell_options = $this->check_box( $row, $col, $cell_options );
			// if ( 1 === sizeof( $cell_options ) ) {
			// 	$cell_options = $cell_options[0];
			// }
		}
		return $cell_options;
	}

	/**
	* @param array $other_cells Array of Cell objects
	* @param array $cell_options Array of integers possible in this cell
	*/
	private function check_options_array( $other_cells, $cell_options ) {
		$sizetwo = array();
		foreach ( $other_cells as $key => $cell ) {
			// skip current cell
			if ( 1 === sizeof( $cell->options ) ) {
				$cell_val = (int) $cell->options[0];
				if ( false !== ( $key = array_search( $cell_val, $cell_options ) ) ) {
    			unset( $cell_options[ $key ] );
				}
			} elseif ( 2 === sizeof( $cell->options ) ) {
				if ( false !== ( $key = array_search( $cell->options, $sizetwo ) ) ) {
					// remove both $cell->options[0], $cell->options[1] from $cell_options
					if ( false !== ( $key = array_search( $cell->options[0], $cell_options ) ) ) {
	    			unset( $cell_options[ $key ] );
					}
					if ( false !== ( $key = array_search( $cell->options[1], $cell_options ) ) ) {
	    			unset( $cell_options[ $key ] );
					}
				} else {
					$sizetwo[] = $cell->options;
				}
			}
		}
		$cell_options = array_values( $cell_options );
		return $cell_options;
	}

	private function check_row( $row, $col, $cell_options ) {
		$other_cells = $this->cells[ $row ]; // now this is an array of objects
		unset( $other_cells[ $col ] ); // remove the current cell
		$other_cells = array_values( $other_cells );
		$cell_options = $this->check_options_array( $other_cells, $cell_options );
		return $cell_options;
	}

	private function check_col( $row, $col, $cell_options ) {
		$other_cells = array_column( $this->cells, $col ); // now this is an array of objects
		unset( $other_cells[ $row ] ); // remove the current cell
		$other_cells = array_values( $other_cells );
		$cell_options = $this->check_options_array( $other_cells, $cell_options );
		return $cell_options;
	}

	private function check_box( $row, $col, $cell_options ) {
		$other_cells = array();
		$box_row = intval( $row / 3 ) * 3;
		$box_col = intval( $col / 3 ) * 3;
		for ( $r = 0; $r < 3; $r ++ ) {
			for ( $c = 0; $c < 3; $c ++ ) {
				$cell_row = $r + $box_row;
				$cell_col = $c + $box_col;
				if ( ( $cell_row !== $row ) || ( $cell_col !== $col ) ) {
					// echo '--'.$cell_row, $cell_col,$row, $col,'<br/>';
					$cell_value = $this->cells[ $cell_row ][ $cell_col ];
					$other_cells[] = $cell_value; // now this is an array of objects
				}
			}
		}
		$cell_options = $this->check_options_array( $other_cells, $cell_options );
		return $cell_options;
	}

	public function check_grid() {
		$success = true;
		for ( $row = 0; $row < 9; $row ++ ) {
			for ( $col = 0; $col < 9; $col ++ ) {
				$this->cells[ $row ][ $col ]->options = $this->get_cell_options( $row, $col );
				if ( $success && ( 1 < sizeof( $this->cells[ $row ][ $col ]->options ) ) ) {
					$success = false;
				}
			}
		}
		return $success;
	}

	public function get_grid_html() {
		$grid_out = '';
		$grid_out .= '<table style="border-spacing:0px;border:3px solid #000;">' . "\n";
		for ( $r = 0; $r < 9; $r ++ ) {
			$grid_out .= '<tr>' . "\n";
			for ( $c = 0; $c < 9; $c ++ ) {
				// escape out
				$cell = $this->cells[ $r ][ $c ];
				// echo gettype( $cell );
				$token = ( 9 === sizeof( $cell->options ) ) ? ' ' : implode( ',', $cell->options );
				$css = '';
				if ( 0 === $c % 3 ) {
					$css .= 'border-left:3px solid #000;';
				}
				if ( 0 === $r % 3 ) {
					$css .= 'border-top:3px solid #000;';
				}
				$grid_out .= '<td style="border:1px solid #000;padding:10px;'. $css . '">' . $token . '</td>' . "\n";
			}
			$grid_out .= '</tr>' . "\n";
		}
		$grid_out .= '</table>' . "\n";
		return $grid_out;
	}

}
