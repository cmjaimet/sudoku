<?php
interface GridInterface {
	// missing functions that I may actually move into separate classes anyways
	function create( $grids_raw, $puzzle_number );

	function get_grid_html();

}
