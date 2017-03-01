<?php
/*
Plugin Name: RBM Services CPT
Plugin URL: https://github.com/realbig/RBM-Services-CPT
Description: Services CPT moved from CPT-onomies
Version: 0.1.0
Text Domain: rbm-services-cpt
Author: Eric Defore
Author URL: http://realbigmarketing.com
Contributors: d4mation
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'RBM_Services_CPT' ) ) {

	/**
	 * Main RBM_Services_CPT class
	 *
	 * @since	  1.0.0
	 */
	class RBM_Services_CPT {
		
		/**
		 * @var			RBM_Services_CPT $plugin_data Holds Plugin Header Info
		 * @since		1.0.0
		 */
		public $plugin_data;
		
		/**
		 * @var			RBM_Services_CPT $admin_errors Stores all our Admin Errors to fire at once
		 * @since		1.0.0
		 */
		private $admin_errors;
		
		/**
		 * @var			RBM_Services_CPT $cpt Holds the CPT
		 * @since		1.0.0
		 */
		public $cpt;

		/**
		 * Get active instance
		 *
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  object self::$instance The one true RBM_Services_CPT
		 */
		public static function instance() {
			
			static $instance = null;
			
			if ( null === $instance ) {
				$instance = new static();
			}
			
			return $instance;

		}
		
		protected function __construct() {
			
			$this->setup_constants();
			$this->load_textdomain();
			
			if ( ! class_exists( 'RBM_CPTS' ) ||
			   ! class_exists( 'RBM_FieldHelpers' ) ) {
				
				$this->admin_errors[] = sprintf( _x( 'To use the %s Plugin, both %s and %s must be active as either a Plugin or a Must Use Plugin!', 'Missing Dependency Error', RBM_Services_CPT_ID ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '<a href="//github.com/realbig/rbm-field-helpers/" target="_blank">' . __( 'RBM Field Helpers', RBM_Services_CPT_ID ) . '</a>', '<a href="//github.com/realbig/rbm-cpts/" target="_blank">' . __( 'RBM Custom Post Types', RBM_Services_CPT_ID ) . '</a>' );
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_errors' ) );
				}
				
				return false;
				
			}
			
			$this->require_necessities();
			
			// Register our CSS/JS for the whole plugin
			add_action( 'init', array( $this, 'register_scripts' ) );
			
		}

		/**
		 * Setup plugin constants
		 *
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function setup_constants() {
			
			// WP Loads things so weird. I really want this function.
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			
			// Only call this once, accessible always
			$this->plugin_data = get_plugin_data( __FILE__ );
			
			if ( ! defined( 'RBM_Services_CPT_ID' ) ) {
				// Plugin Text Domain
				define( 'RBM_Services_CPT_ID', $this->plugin_data['TextDomain'] );
			}

			if ( ! defined( 'RBM_Services_CPT_VER' ) ) {
				// Plugin version
				define( 'RBM_Services_CPT_VER', $this->plugin_data['Version'] );
			}

			if ( ! defined( 'RBM_Services_CPT_DIR' ) ) {
				// Plugin path
				define( 'RBM_Services_CPT_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'RBM_Services_CPT_URL' ) ) {
				// Plugin URL
				define( 'RBM_Services_CPT_URL', plugin_dir_url( __FILE__ ) );
			}
			
			if ( ! defined( 'RBM_Services_CPT_FILE' ) ) {
				// Plugin File
				define( 'RBM_Services_CPT_FILE', __FILE__ );
			}

		}

		/**
		 * Internationalization
		 *
		 * @access	  private 
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function load_textdomain() {

			// Set filter for language directory
			$lang_dir = RBM_Services_CPT_DIR . '/languages/';
			$lang_dir = apply_filters( 'rbm_services_cpt_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), RBM_Services_CPT_ID );
			$mofile = sprintf( '%1$s-%2$s.mo', RBM_Services_CPT_ID, $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/' . RBM_Services_CPT_ID . '/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/rbm-services-cpt/ folder
				// This way translations can be overridden via the Theme/Child Theme
				load_textdomain( RBM_Services_CPT_ID, $mofile_global );
			}
			else if ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/rbm-services-cpt/languages/ folder
				load_textdomain( RBM_Services_CPT_ID, $mofile_local );
			}
			else {
				// Load the default language files
				load_plugin_textdomain( RBM_Services_CPT_ID, false, $lang_dir );
			}

		}
		
		/**
		 * Include different aspects of the Plugin
		 * 
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function require_necessities() {
			
			require_once RBM_Services_CPT_DIR . 'core/cpt/class-rbm-cpt-services.php';
			$this->cpt = new RBM_CPT_Services();
			
		}
		
		/**
		 * Show admin errors.
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  HTML
		 */
		public function admin_errors() {
			?>
			<div class="error">
				<?php foreach ( $this->admin_errors as $notice ) : ?>
					<p>
						<?php echo $notice; ?>
					</p>
				<?php endforeach; ?>
			</div>
			<?php
		}
		
		/**
		 * Register our CSS/JS to use later
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  void
		 */
		public function register_scripts() {
			
			wp_register_style(
				RBM_Services_CPT_ID . '-admin',
				RBM_Services_CPT_URL . 'assets/css/admin.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : RBM_Services_CPT_VER
			);
			
			wp_register_script(
				RBM_Services_CPT_ID . '-admin',
				RBM_Services_CPT_URL . 'assets/js/admin.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : RBM_Services_CPT_VER,
				true
			);
			
			wp_localize_script( 
				RBM_Services_CPT_ID . '-admin',
				'rBMServicesCPT',
				apply_filters( 'rbm_services_cpt_localize_admin_script', array() )
			);
			
		}
		
	}
	
} // End Class Exists Check

/**
 * The main function responsible for returning the one true RBM_Services_CPT
 * instance to functions everywhere
 *
 * @since	  1.0.0
 * @return	  \RBM_Services_CPT The one true RBM_Services_CPT
 */
add_action( 'plugins_loaded', 'rbm_services_cpt_load', 999 );
function rbm_services_cpt_load() {

	require_once __DIR__ . '/core/rbm-services-cpt-functions.php';
	RBMSERVICESCPT();

}
