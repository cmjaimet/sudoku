<?php
require_once( 'classes/Sudoku.php' );

require_once( 'classes/StringGrid.php' );

for ( $x = 0; $x < 1; $x ++ ) {
	$grid = new StringGrid( 'data/grids_no.txt', $x );
	$game = new Sudoku( $grid );
	$game->play( 25 );
}
