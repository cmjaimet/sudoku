<?php
require_once( 'Grid.php' );

class Sudoku {
	private $grid;

	function __construct( GridInterface $grid ) {
		$this->grid = $grid;
	}

	public function play( $max_turns ) {
		echo $this->grid->get_grid_html(); // starting board
		for ( $x = 0; $x < $max_turns; $x ++  ) {
			$complete = $this->check_grid();
			if ( true === $complete ) {
				break;
			}
			if ( false === $this->grid->changed_grid ) {
				echo 'No more changes found.';
				break;
			}
			// echo $this->grid->get_grid_html();
		}
		echo $this->grid->get_grid_html();
		echo '<p>You ' . ( true === $complete ? 'succeeded' : 'failed' ) . ' in ' . ( $x + 1 ) . ' turns</p>';
	}

d	private function get_cell_groups( $row, $col ) {
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

}
