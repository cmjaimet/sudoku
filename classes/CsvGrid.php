<?php
// namespace Games\Sudoku\Grid;

require_once( 'Cell.php' );
require_once( 'GridInterface.php' );

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

	public function get_grid_html() {
		$grid_out = '';
		$grid_out .= '<table style="border-spacing:0px;border:5px solid #000;">' . "\n";
		for ( $r = 0; $r < 9; $r ++ ) {
			$grid_out .= '<tr>' . "\n";
			for ( $c = 0; $c < 9; $c ++ ) {
				// escape out
				$cell = $this->cells[ $r ][ $c ];
				// echo gettype( $cell );
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

}
