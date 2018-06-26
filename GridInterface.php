<?php
interface GridInterface {

	function get_grid_from_csv( $grid_raw );

	function get_grid_from_qqwing( $grids_raw, $puzzle_number );

	function get_cell_options( $row, $col );

	function get_cell_groups( $row, $col );

	function get_cells_in_row( $row );

	function get_cells_in_col( $col );

	function get_cells_in_box( $box );

	function get_cell_coords_from_box( $box, $r, $c );

	function check_cell_group();

	function check_row( $row );

	function check_col( $col );

	function check_box( $box );

	function check_grid();

	function remove_cell_number( $row, $col, $number );

}
