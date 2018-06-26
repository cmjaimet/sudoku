<?php
echo '<pre>';
$other_cells = array(
	array(7),
	array(2,5,9),
	array(2,5,8,9),
	array(1,9),
	array(1,5,9),
	array(4),
	array(3),
	array(1,2,5,6,9)
);
print_r($other_cells);
$cell = array(1,5,9);
foreach ( $other_cells as $key => $cell_vals ) {
	$count_number = 0;
	$count_all = true;
	foreach ( $cell_vals as $key => $temp_number ) {
		if ( ! in_array( $temp_number, $cell ) ) {
			$count_all = false;
			break;
		}
	}
	if (true === $count_all ) {
		$count_continue ++;
		unset( $other_cells[ $key ] );
		if ( 2 === $count_continue ) {
			print_r($other_cells);
			//$continue = true;
			break;
		}
	}
}
