<?php
// namespace Games\Sudoku\Cell;

class Cell {
	public $options;

	function __construct( $value = '' ) {
		if ( 0 === intval( $value ) ) {
			$this->options = range( 1, 9 );
		} else {
			$this->options = array( intval( $value ) );
		}
	}

}
