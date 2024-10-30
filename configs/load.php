<?php

function lass_class_file_autoloader( $class_name ) {
	
	/**
	 * If the class being requested does not start with our prefix,
	 * we know it's not one in our project
	 */
	if ( 0 !== strpos( $class_name, 'Lass_' ) ) {
		return;
	}
	
	$file_name = str_replace(
		array( 'Lass_', '_' ),      // Prefix | Underscores
		array( '', '-' ),         // Remove | Replace with hyphens
		$class_name // lowercase - disabled
	);
	
	// Compile our path from the current location
	$file = dirname( __DIR__ ) . '/classes/'. $file_name .'.php';
	
	// If a file is found
	if ( file_exists( $file ) ) {
		// Then load it up!
		require( $file );
	}
}

spl_autoload_register( 'lass_class_file_autoloader' );
