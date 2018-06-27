<?php

class Guess {
	public $row = 0;
	public $col = 0;
	public $number = 0;
	public $other = 0;

	function __construct( $grid, $guess ) {
		$this->grid = $grid;
		if ( isset( $guess->row ) ) {
			$this->row = $guess->row;
			$this->col = $guess->col;
		}
		$this->get_guess();
	}

	private function get_guess() {
		for ( $row = $this->row; $row < 9; $row ++ ) {
			for ( $col = $this->col; $col < 9; $col ++ ) {
				if ( 2 === sizeof( $this->grid->cells[ $row ][ $col ]->options ) ) {
					$this->row = $row;
					$this->col = $col;
					$this->number = $this->grid->cells[ $row ][ $col ]->options[0]; // pick the first number in the array to guess
					$this->other = $this->grid->cells[ $row ][ $col ]->options[1];
					echo '<br />Guessing: '.$this->grid->cells[ $row ][ $col ]->options[0].'  in ('.$row.','.$col.')<br />';
					return;
				}
			}
		}
	}

}
