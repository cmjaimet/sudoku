<?php
// namespace Games\Sudoku\Grid;

require_once( 'Cell.php' );

class Grid {
	public $cells;
	public $raw_data;
	public $changed_grid;
	private $cell_group;

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

	private function get_cell_groups( $row, $col ) {
		$cell_group = array();
		$mode = ( is_null( $row ) ) ? 'col' : 'row';
		for ( $other = 0; $other < 9; $other ++ ) {
			if ( 'col' === $mode ) {
				$row = $other;
			} else {
				$col = $other;
			}
			$key = sizeof( $this->cells[ $row ][ $col ]->options );
			if ( 3 >= $key ) {
				if ( false === isset( $cell_group[ $key ] ) ) {
					$cell_group[ $key ] = array();
				}
				$cell_group[ $key ][] = $this->cells[ $row ][ $col ]->options;
			}
		}
		return $cell_group;
	}

	private function get_cells_in_row( $row ) {
		$cell_group = array();
		for ( $col = 0; $col < 9; $col ++ ) {
			$cell_group[] = array( $row, $col );
		}
		return $cell_group;
	}

	private function get_cells_in_col( $col ) {
		$cell_group = array();
		for ( $row = 0; $row < 9; $row ++ ) {
			$cell_group[] = array( $row, $col );
		}
		return $cell_group;
	}

	private function get_cells_in_box( $box ) {
		$cell_group = array();
		$box_row = intval( $box / 3 ) * 3;
		$box_col = ( $box % 3 ) * 3;
		for ( $r = 0; $r < 3; $r ++ ) {
			for ( $c = 0; $c < 3; $c ++ ) {
				$cell_row = $r + $box_row;
				$cell_col = $c + $box_col;
				$cell_group[] = array( $cell_row, $cell_col );
			}
		}
		return $cell_group;
	}

	private function get_cell_coords_from_box( $box, $r, $c ) {
		return array( $cell_row, $cell_col );
	}

	private function check_cell_group() {
		for ( $x = 0; $x < sizeof( $this->cell_group ); $x++ ) {
			$row = $this->cell_group[$x][0];
			$col = $this->cell_group[$x][1];
			$cell = $this->cells[ $row ][ $col ]->options;
			$cell_size = sizeof( $cell );
			if ( 3 >= $cell_size ) {
				$other_cells = $this->cell_group;
				unset( $other_cells[ $x ] );
				$continue = false;
				if ( 3 === $cell_size ) {
					// only continue if there are 2 more like this or containing at least two of these numbers in its array
					$count_continue = 0;
					foreach ( $other_cells as $temp_key => $temp_coords ) {
						$temp_row = $temp_coords[0];
						$temp_col = $temp_coords[1];
						$count_number = 0;
						$count_all = true;
						foreach ( $this->cells[ $temp_row ][ $temp_col ]->options as $key => $temp_number ) {
							if ( ! in_array( $temp_number, $cell ) ) {
								$count_all = false;
								break;
							}
						}
						if (true === $count_all ) {
							$count_continue ++;
							unset( $other_cells[ $temp_key ] );
							if ( 2 === $count_continue ) {
								$continue = true;
								break;
							}
						}
					}
				} elseif ( 2 === $cell_size ) {
					// only continue if there's another like this
					foreach ( $other_cells as $key => $temp_coords ) {
						$temp_row = $temp_coords[0];
						$temp_col = $temp_coords[1];
						if ( $this->cells[ $temp_row ][ $temp_col ]->options === $cell ) {
							unset( $other_cells[ $key ] );
							$continue = true;
							break;
						}
					}
				} else {
					$continue = true;
				}
				if ( true === $continue ) {
					foreach ( $other_cells as $key => $other_coords ) {
						$other_row = $other_coords[0];
						$other_col = $other_coords[1];
						$other_cell = $this->cells[ $other_row ][ $other_col ]->options;
						foreach ( $cell as $number ) {
							$this->remove_cell_number( $other_row, $other_col, $number );
						}
					}
				}
			}
		}
	}

	private function check_row( $row ) {
		$this->cell_group = $this->get_cells_in_row( $row );
		$this->check_cell_group();
	}

	private function check_col( $col ) {
		$this->cell_group = $this->get_cells_in_col( $col );
		$this->check_cell_group();
	}

	private function check_box( $box ) {
		$this->cell_group = $this->get_cells_in_box( $box );
		$this->check_cell_group();
	}

	public function check_grid() {
		$this->changed_grid = false;
		for ( $row = 0; $row < 9; $row ++ ) {
			$this->check_row( $row );
		}
		for ( $col = 0; $col < 9; $col ++ ) {
			$this->check_col( $col );
		}
		for ( $box = 0; $box < 9; $box ++ ) {
			$this->check_box( $box );
		}
		for ( $row = 0; $row < 9; $row ++ ) {
			for ( $col = 0; $col < 9; $col ++ ) {
				if ( 1 < sizeof( $this->cells[ $row ][ $col ]->options ) ) {
					return false;
				}
			}
		}
		return true;
	}

	private function remove_cell_number( $row, $col, $number ) {
		if ( false !== ( $key = array_search( $number, $this->cells[ $row ][ $col ]->options ) ) ) {
			unset( $this->cells[ $row ][ $col ]->options[ $key ] );
			$this->changed_grid = true;
		}
		$this->cells[ $row ][ $col ]->options = array_values( $this->cells[ $row ][ $col ]->options );
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
