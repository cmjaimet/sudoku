<?php
require_once( 'Grid.php' );

class Sudoku {
	private $grid;

	function __construct( Grid $grid ) {
		$this->grid = $grid;
	}

	public function play( $max_turns ) {
		echo $this->grid->get_grid_html(); // starting board
		for ( $x = 0; $x < $max_turns; $x ++  ) {
			$complete = $this->grid->check_grid();
			if ( true === $complete ) {
				break;
			}
			if ( false === $this->grid->changed_grid ) {
				echo 'No more changes found.';
				break;
			}
			echo $this->grid->get_grid_html();
		}
		echo $this->grid->get_grid_html();
		echo '<p>You ' . ( true === $complete ? 'succeeded' : 'failed' ) . ' in ' . ( $x + 1 ) . ' turns</p>';
	}

}
