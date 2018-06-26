<?php

class Sudoku {
	private $grid = array();

	function __construct( $filename ) {
		$grid_csv = file_get_contents( $filename );
		$grid = $this->get_grid_from_csv( $grid_csv );
	}

	public function get_cell_options( $row, $col ) {
		$cell_options = $this->grid[ $row ][ $col ];
		if ( 'array' === gettype( $cell_options ) ) {
			$cell_options = $this->check_row( $row, $cell_options );
			$cell_options = $this->check_col( $col, $cell_options );
			$cell_options = $this->check_box( $row, $col, $cell_options );
			if ( 1 === sizeof( $cell_options ) ) {
				$cell_options = $cell_options[0];
			}
		}
		return $cell_options;
	}

	private function check_options_array( $other_cells, $cell_options ) {
		foreach ( $other_cells as $key => $cell ) {
			if ( 'array' !== gettype( $cell ) ) {
				$cell_val = (int) $cell;
				if ( false !== ( $key = array_search( $cell_val, $cell_options ) ) ) {
    			unset( $cell_options[ $key ] );
				}
			}
		}
		$cell_options = array_values( $cell_options );
		return $cell_options;
	}

	private function check_row( $row, $cell_options ) {
		$other_cells = $this->grid[ $row ];
		$cell_options = $this->check_options_array( $other_cells, $cell_options );
		return $cell_options;
	}

	private function check_col( $col, $cell_options ) {
		$other_cells = array_column( $this->grid, $col );
		$cell_options = $this->check_options_array( $other_cells, $cell_options );
		return $cell_options;
	}

	private function check_box( $row, $col, $cell_options ) {
		$other_cells = array();
		$row = intval( $row / 3 ) * 3;
		$col = intval( $col / 3 ) * 3;
		for ( $r = 0; $r < 3; $r ++ ) {
			for ( $c = 0; $c < 3; $c ++ ) {
				$cell_value = $this->grid[ $r + $row ][ $c + $col ];
				if ( 'array' !== gettype( $cell_value ) ) {
					$other_cells[] = $cell_value;
				}
			}
		}
		$cell_options = $this->check_options_array( $other_cells, $cell_options );
		return $cell_options;
	}

	private function get_grid_from_csv( $grid_csv ) {
		$grid_rows = explode( "\n", $grid_csv );
		$cell_unknown = range( 1, 9 );
		for ( $row = 0; $row < 9; $row ++ ) {
			$grid_row = explode( ',', $grid_rows[ $row ] );
			for ( $col = 0; $col < 9; $col ++ ) {
				$cell = ( 0 === ( int ) $grid_row[ $col ] ) ? $cell_unknown : ( int ) $grid_row[ $col ];
				$this->grid[ $row ][ $col ] = $cell;
			}
		}
	}

	private function check_grid() {
		$success = true;
		for ( $row = 0; $row < 9; $row ++ ) {
			for ( $col = 0; $col < 9; $col ++ ) {
				$this->grid[ $row ][ $col ] = $this->get_cell_options( $row, $col );
				if ( $success && ( 'array' === gettype( $this->grid[ $row ][ $col ] ) ) ) {
					$success = false;
				}
			}
		}
		return $success;
	}

	public function play( $max_turns ) {
		$game_grid = $this->get_grid_html();
		echo $game_grid;
		for ( $x = 0; $x < $max_turns; $x ++  ) {
			$complete = $this->check_grid();
			if ( $complete ) {
				echo '<p>You did it in ' . ( $x + 1 ) . ' turns</p>';
				$game_grid = $this->get_grid_html();
				echo $game_grid;
				return;
			}
		}
		echo '<p>You failed in ' . ( $x + 1 ) . ' turns</p>';
		$game_grid = $this->get_grid_html();
		echo $game_grid;
	}

	public function get_grid_html() {
		$grid_out = '';
		$grid_out .= '<table style="border-spacing:0px;border:3px solid #000;">' . "\n";
		for ( $r = 0; $r < 9; $r ++ ) {
			$grid_out .= '<tr>' . "\n";
			for ( $c = 0; $c < 9; $c ++ ) {
				// escape out
				$cell = $this->grid[ $r ][ $c ];
				// echo gettype( $cell );
				$token = ( 'array' === gettype( $cell ) ) ? '' : $cell;
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

$grid_start = 'grid1.csv';
$game = new Sudoku( $grid_start );
$game->play( 20 );
