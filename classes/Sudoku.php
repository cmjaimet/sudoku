<?php
require_once( 'Grid.php' );
require_once( 'Guess.php' );

class Sudoku {
	private $grid;
	private $grid_original = null;
	private $guess = null;
	private $debug = false;

	function __construct( GridInterface $grid ) {
		$this->grid = $grid;
		$this->debug = false;
	}

	public function play( $max_turns ) {
		echo $this->grid->get_grid_html(); // starting board
		for ( $x = 0; $x < $max_turns; $x ++  ) {
			$complete = $this->check_grid();
			if ( ( true === $this->grid->failed ) || ( false === $this->grid->changed ) ) {
				// ok now we start guessing
				$this->guess();
			} else {
				if ( true === $complete ) {
					break;
				}
			}
			// echo $this->grid->get_grid_html();
		}
		echo $this->grid->get_grid_html();
		echo '<p>You ' . ( true === $complete ? 'succeeded' : 'failed' ) . ' in ' . ( $x + 1 ) . ' turns</p>';
	}

	private function guess() {
		echo $this->grid->get_grid_html();
		if ( ! is_null( $this->grid_original ) ) {
			echo 'Guess failed so assume the opposite. ' . $this->guess->row . ', ' . $this->guess->col . ' === ' . $this->guess->other . '<br />';
			echo $this->grid->get_grid_html();
			// $this->grid = clone $this->grid_original; // we're in the middle of a guess and it failed so revert and guess the opposite
			$this->grid = unserialize( serialize( $this->grid_original ) );
			$this->grid_original = null; // reset
			$this->grid->set_cell( $this->guess->row, $this->guess->col, $this->guess->other );
		} else {
			echo 'No more changes found. Taking a guess.<br />';
			// copy the first grid away for safe-keeping while we see if the guess works - fails if we go through this again with the guess - so we would need deeper recursion technique - cloning is unreliable in PHP
			$this->grid_original = unserialize( serialize( $this->grid ) );
			$this->grid->is_guess = true;
			$this->guess = new Guess( $this->grid, $this->guess );
			$this->grid->set_cell( $this->guess->row, $this->guess->col, $this->guess->number );
			echo $this->grid->get_grid_html();
			// exit;
		}
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

			$key = sizeof( $this->grid->cells[ $row ][ $col ]->options );
			if ( 3 >= $key ) {
				if ( false === isset( $cell_group[ $key ] ) ) {
					$cell_group[ $key ] = array();
				}
				$cell_group[ $key ][] = $this->grid->cells[ $row ][ $col ]->options;
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
			$cell = $this->grid->cells[ $row ][ $col ]->options;
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
						foreach ( $this->grid->cells[ $temp_row ][ $temp_col ]->options as $key => $temp_number ) {
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
						if ( $this->grid->cells[ $temp_row ][ $temp_col ]->options === $cell ) {
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
						$other_cell = $this->grid->cells[ $other_row ][ $other_col ]->options;
						foreach ( $cell as $number ) {
							$this->grid->remove_cell_number( $other_row, $other_col, $number );
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
		$this->grid->changed = false;
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
				if ( 1 < sizeof( $this->grid->cells[ $row ][ $col ]->options ) ) {
					return false;
				}
			}
		}
		return true;
	}

}
