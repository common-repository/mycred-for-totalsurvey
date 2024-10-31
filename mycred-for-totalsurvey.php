<?php
/**
 * Plugin Name: myCred for TotalSurvey
 * Requires Plugins: mycred, totalsurvey
 * Plugin URI: https://www.mycred.me/store/
 * Description: Allows you to reward users points for complete Survey.
 * Version: 1.0.1
 * Tags: mycred, points, cred, survey
 * Author: myCred
 * Author URI: https://www.mycred.me
 * Author Email: support@mycred.me
 * Requires at least: WP 6.2.1
 * Tested up to: WP 6.6.1
 * Text Domain: mycred-for-totalsurvey
 * Domain Path: /lang
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MyCred_TotalSurvey' ) ) :
	final class MyCred_TotalSurvey {

		// Plugin Version
		public $version                 = '1.0.1';

		// Instnace
		protected static $_instance     = null;

		public $slug                    = '';
		public $plugin                  = null;
		public $plugin_name             = '';
		private $register_hook_check    = false;

		/**
		 * Setup Instance
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Not allowed
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function __clone() {

			_doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.0.1' ); }

		/**
		 * Not allowed
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function __wakeup() {

			 _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.0.1' ); }

		/**
		 * Define
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function define( $name, $value, $definable = true ) {

			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Require File
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function file( $required_file ) {

			if ( file_exists( $required_file ) ) {
				require_once $required_file;
			}
		}

		/**
		 * Construct
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function __construct() {

			$this->slug        = 'mycred-totalsurvey';
			$this->plugin      = plugin_basename( __FILE__ );
			$this->plugin_name = 'myCRED for TotalSurvey';

			$this->define_constants();
			add_filter('mycred_setup_hooks', array( $this, 'register_hook' ) );
			add_action('mycred_init', array( $this, 'load_textdomain' ) );
			add_action('admin_init', array( $this, 'check_required_plugin' ) );
			add_action('mycred_all_references', array( $this, 'add_badge_support' ) );
			add_action('mycred_load_hooks', array( $this, 'mycred_total_survey_load_hook' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_script' ) );
		}

		/**
		 * Define Constants
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function define_constants() {

			$this->define( 'MYCRED_TOTAL_SURVEY_VER', $this->version );
			$this->define( 'MYCRED_TOTAL_SURVEY_SLUG', $this->slug );
			$this->define( 'MYCRED_DEFAULT_TYPE_KEY', 'mycred_default' );
			$this->define( 'MYCRED_TOTAL_SURVEY', __FILE__ );
			$this->define( 'MYCRED_TOTAL_SURVEY_URL', plugin_dir_url( MYCRED_TOTAL_SURVEY ) );
			$this->define( 'MYCRED_TOTAL_SURVEY_PATH', plugin_dir_path( MYCRED_TOTAL_SURVEY ) );
		}

		/**
		 * Includes
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function includes() { }

		/**
		 * Load Textdomain
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function load_textdomain() {

			/**
			* Filter plugin_locale
			* 
			* @since 1.0.0
			**/
			$locale = apply_filters( 'plugin_locale', get_locale(), 'mycred-for-totalsurvey' );

			load_textdomain( 'mycred-for-totalsurvey', WP_LANG_DIR . '/' . $this->slug . '/mycred-totalsurvey-' . $locale . '.mo' );
			load_plugin_textdomain( 'mycred-for-totalsurvey', false, dirname( $this->plugin ) . '/lang/' );
		}
		/**
		 * Deactive plugin if myCred and TotalSurvey are not active
		 */
		public function check_required_plugin() {

			
			if ( ! is_plugin_active( 'mycred/mycred.php' ) && ! is_plugin_active( 'totalsurvey/plugin.php' ) ) {   // Check if TotalSurvey plugin and myCred plugin is active
				
				// Deactivate "myCred for TotalSurvey" add-on
				deactivate_plugins( 'mycred-for-totalsurvey/mycred-for-totalsurvey.php' );

				// Display an error message
				wp_die( 'Please activate myCred plugin and TotalSurvey plugin before activating myCred for TotalSurvey.' );

			} elseif ( ! is_plugin_active( 'mycred/mycred.php' ) ) {   // Check if myCred plugin is active
				
				// Deactivate "myCred for TotalSurvey" add-on
				deactivate_plugins( 'mycred-for-totalsurvey/mycred-for-totalsurvey.php' );

				// Display an error message
				wp_die( 'Please activate myCred plugin before activating myCred for TotalSurvey.' );

			} elseif ( ! is_plugin_active( 'totalsurvey/plugin.php' ) ) {   // Check if TotalSurvey plugin is active
				
				// Deactivate "myCred for TotalSurvey" add-on
				deactivate_plugins( 'mycred-for-totalsurvey/mycred-for-totalsurvey.php' );

				// Display an error message
				wp_die( 'Please activate TotalSurvey plugin before activating myCred for TotalSurvey.' );

			}
		}

		public function enqueue_styles_script() {

			wp_enqueue_style( 'mycredtotalsurvey_hook_style', plugins_url( 'assets/css/admin.css', MYCRED_TOTAL_SURVEY ), '1.0.0', true );
			wp_enqueue_script( 'mycredtotalsurvey_hook_script', plugins_url( 'assets/js/admin.js', MYCRED_TOTAL_SURVEY ), array( 'jquery' ), '1.0.0', true );
		}
		/**
		 * Display admin notice when myCred plugin is not activated
		 */
		public function myCred_admin_notice() {

			$mycred_check_notice = '<div class="error"><p>' . __('To Activate myCred TotalSurvey add-on plugin You Need To Activate myCred Plugin.', 'mycred-for-totalsurvey') . '</p></div>';
			echo wp_kses_post( $mycred_check_notice ); 
		}
		public function TotalSurvey_admin_notice() {

			$totalsurvey_check_notice = '<div class="error"><p>' . __('To Activate myCred TotalSurvey add-on plugin You Need To Activate TotalSurvey Plugin.', 'mycred-for-totalsurvey') . '</p></div>';
			echo wp_kses_post( $totalsurvey_check_notice ); 
		}

		/**
		 * Register Hook
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function register_hook( $installed ) {

			if ( $this->register_hook_check ) {
				return $installed;
			}

			$installed['totalsurvey'] = array(
				'title'       => __( 'TotalSurvey', 'mycred-for-totalsurvey' ),
				'description' => __( 'Awards %_plural% for complete survey.', 'mycred-for-totalsurvey' ),
				'callback'    => array( 'MyCred_Hook_Total_Survey' )
			);

			return $installed;
		}

		/**
		 * Add Badge Support
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function add_badge_support( $references ) {

			$references['totalsurvey'] = __( 'Complete Survey (TotalSurvey)', 'mycred-for-totalsurvey' );

			return $references;
		}

		/**
		 * Require Hook Settings
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function mycred_total_survey_load_hook() {

			$this->file( MYCRED_TOTAL_SURVEY_PATH . 'includes/mycred-totalsurvey-hook.php' );
		}

		public static function mycred_totalsurvey_plugin() {
			return self::instance();
		}
	}
endif;

MyCred_TotalSurvey::mycred_totalsurvey_plugin();
