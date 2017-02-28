<?php
/**
 * Provides helper functions.
 *
 * @since	  1.0.0
 *
 * @package	RBM_Services_CPT
 * @subpackage RBM_Services_CPT/core
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Returns the main plugin object
 *
 * @since		1.0.0
 *
 * @return		RBM_Services_CPT
 */
function RBMSERVICESCPT() {
	return RBM_Services_CPT::instance();
}