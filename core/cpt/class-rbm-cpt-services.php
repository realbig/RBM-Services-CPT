<?php

/**
 * Class RBM_CPT_Services
 *
 * Creates the post type.
 *
 * @since {{VERSION}}
 */
class RBM_CPT_Services extends RBM_CPT {

	public $post_type = 'service';
	public $label_singular = null;
	public $label_plural = null;
	public $labels = array();
	public $icon = 'admin-post';
	public $post_args = array(
		'hierarchical' => false,
		'supports'     => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' ),
		'has_archive'  => true,
		'rewrite'      => array(
			'slug'       => 'service',
			'with_front' => false,
			'feeds'      => false,
			'pages'      => true
		),
	);

	/**
	 * RBM_CPT_Services constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		// This allows us to Localize the Labels
		$this->label_singular = __( 'Service', RBM_Services_CPT_ID );
		$this->label_plural   = __( 'Services', RBM_Services_CPT_ID );

		$this->labels = array(
			'menu_name' => __( 'Services', RBM_Services_CPT_ID ),
			'all_items' => __( 'All Services', RBM_Services_CPT_ID ),
		);

		parent::__construct();
		
	}
}