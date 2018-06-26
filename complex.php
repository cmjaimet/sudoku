<?php

require_once( 'Grid.php' );

class Sudoku {
	private $grid;

	function __construct( Grid $grid ) {
		$this->grid = $grid;
	}

	public function play( $max_turns ) {
		$game_grid = $this->grid->get_grid_html();
		echo $game_grid;
		for ( $x = 0; $x < $max_turns; $x ++  ) {
			$complete = $this->grid->check_grid();
			if ( $complete ) {
				break;
			}
		}
		//echo '<br />'. ( true === $complete ? $this->grid->raw_data : '');
		echo $this->grid->get_grid_html();
		echo '<p>You ' . ( true === $complete ? 'succeeded' : 'failed' ) . ' in ' . $x . ' turns</p>';
	}

}

for ( $x = 0; $x < 1; $x ++ ) {
	$grid = new Grid( 'data/grids_elusive.txt', $x );
	$game = new Sudoku( $grid );
	$game->play( 30 );
}
