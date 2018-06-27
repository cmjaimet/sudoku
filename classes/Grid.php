<?php
// namespace Games\Sudoku\Grid;

require_once( 'classes/Cell.php' );
require_once( 'interfaces/GridInterface.php' );

class Grid implements GridInterface {
	public $cells;
	public $changed;
	public $failed = false;
	public $is_guess = false;

	function __construct( $filename, $puzzle_number ) {
		$this->create( null, 0 );
	}

	public function __clone() {
	}

	public function create( $grids_raw, $puzzle_number ) {
		for ( $row = 0; $row < 9; $row ++ ) {
			for ( $col = 0; $col < 9; $col ++ ) {
				$this->cells[ $row ][ $col ] = new Cell();
			}
		}
	}

	public function set_cell( $row, $col, $number ) {
		$this->cells[ $row ][ $col ]->options = array( $number );
	}

	public function get_grid_html() {
		$grid_out = '';
		$grid_out .= '<table style="border-spacing:0px;border:5px solid #000;">' . "\n";
		for ( $r = 0; $r < 9; $r ++ ) {
			$grid_out .= '<tr>' . "\n";
			for ( $c = 0; $c < 9; $c ++ ) {
				// escape out
				$cell = $this->cells[ $r ][ $c ];
				$token = ( 9 === sizeof( $cell->options ) ) ? ' ' : implode( ',', $cell->options );
				$css = '';
				if ( 0 === $c % 3 ) {
					$css .= 'border-left:5px solid #000;';
				}
				if ( 0 === $r % 3 ) {
					$css .= 'border-top:5px solid #000;';
				}
				$grid_out .= '<td style="border:1px solid #000;padding:10px;'. $css . '">' . $token . '</td>' . "\n";
			}
			$grid_out .= '</tr>' . "\n";
		}
		$grid_out .= '</table>' . "\n";
		return $grid_out;
	}

	public function remove_cell_number( $row, $col, $number ) {
		if ( false !== ( $key = array_search( $number, $this->cells[ $row ][ $col ]->options ) ) ) {
			unset( $this->cells[ $row ][ $col ]->options[ $key ] );
			$this->cells[ $row ][ $col ]->options = array_values( $this->cells[ $row ][ $col ]->options );
			// echo $this->get_grid_html();
			if ( 0 === sizeof( $this->cells[ $row ][ $col ]->options ) ) {
				$this->failed = true;
			}
			$this->changed = true;
		}
		$this->cells[ $row ][ $col ]->options = array_values( $this->cells[ $row ][ $col ]->options );
	}

}
