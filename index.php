<?php
require_once( 'Sudoku.php' );

for ( $x = 0; $x < 1; $x ++ ) {
	$grid = new Grid( 'data/grids_no.txt', $x );
	$game = new Sudoku( $grid );
	$game->play( 25 );
}
