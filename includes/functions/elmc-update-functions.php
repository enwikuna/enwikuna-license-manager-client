<?php

defined( 'ABSPATH' ) || exit;

// You can use this file to add functions which should run during an update of the client in case you update something which
// e.g. requires some database updates or similar. After adding a new function here, you need to pass them to the class-elmc-install.php
// $version_updates array. The key is the new version and the value is an array of function names which should be called.
// This is an example how you can add a new function to the version_updates array:
//
// '1.2.0' => array(
//     'elmc_update_120_xxx',
// ),
//
// The function elmc_update_120_xxx() should be defined in this file then. You can add as many functions as you need. You always need to
// define the version inside the array which you want to update to! Do not take the old version as key!
//
//if ( ! function_exists( 'elmc_update_120_xxx' ) ) {
//	/**
//	 * Describe the function here
//	 */
//	function elmc_update_120_xxx() {
//	}
//}
