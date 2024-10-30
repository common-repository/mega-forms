<?php

/**
 * @link       https://wpmegaforms.com
 * @since      1.0.0
 *
 * @package    Mega_Forms
 * @subpackage Mega_Forms/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Mega_Forms
 * @subpackage Mega_Forms/public
 * @author     Ali Khallad <ali@wpali.com>
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Mega_Forms_Public
{

	private $plugin_name;
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->load_dependencies();
	}

	/**
	 * Load the required dependencies for this class.
	 *
	 * @since    1.0.0
	 */
	private function load_dependencies()
	{
		// Load form view class which is used for the shortcode
		require_once MEGAFORMS_PUBLIC_PATH . 'partials/class-mega-forms-public-form-view.php';
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * Register the main CSS file for the frontend area.
		 * This file is automatically generated. It is a minified version of the following file:
		 *
		 * - common/assets/css/*.css
		 * - public/assets/css/*.css
		 *
		 * If `MEGAFORMS_LOAD_COMBINED_CSS` is set to true, we will load a combined JS file, which also
		 * include the JS code for the PRO version.
		 *
		 * @see Gruntfile.js for more details
		 */

		if (!MEGAFORMS_LOAD_COMBINED_CSS) {
			wp_register_style('mf-public', MEGAFORMS_DIR_URL . 'assets/frontend/css/styles.min.css', array(), $this->version, 'all');
		} else {
			wp_register_style('mf-public', MEGAFORMS_DIR_URL . 'assets/frontend/css/combined/styles.min.css', array(), $this->version, 'all');
		}

		if (mfget_option('load_form_styling', true)) {
			wp_enqueue_style('mf-public');
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * Note: Any field specific JavaScript files is loaded from the shortcode callback only when needed.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		$deps   = array('jquery');
		$mfVars = array(
			'labels' => array(
				'x' => __('Placeholder', 'megaforms'),
			),
			'cookie' => mf_session()->get_cookie_key(),
			'sid'    => mf_session()->get_session_id(),
		);
		/**
		 * Register the main JS file for the frontend area.
		 * This file is automatically generated. It is a minified version of the following file:
		 *
		 * - common/assets/js/*.js
		 * - public/assets/js/*.js
		 *
		 * If `MEGAFORMS_LOAD_COMBINED_JS` is set to true, we will load a combined JS file, which also
		 * include the JS code for the PRO version.
		 *
		 * @see Gruntfile.js for more details
		 */
		if (!MEGAFORMS_LOAD_COMBINED_JS) {
			wp_register_script('mf-public', MEGAFORMS_DIR_URL . 'assets/frontend/js/scripts.min.js', $deps, $this->version, false);
		} else {
			wp_register_script('mf-public', MEGAFORMS_DIR_URL . 'assets/frontend/js/combined/scripts.min.js', $deps, $this->version, false);
		}
		wp_localize_script('mf-public', 'mfCommonVars', get_mf_common_js_vars());
		wp_localize_script('mf-public', 'mfVars', $mfVars);
	}

	/**
	 * Listen to form submissions and process them if there are any.
	 *
	 * @since    1.0.8
	 */
	public function listen()
	{
		if (isset($_POST['mform_submit']) && isset($_POST['_mf_form_id'])) {

			mf_submission()->exec($_POST['_mf_form_id'], $_POST);

			// Make any necessary redirections
			if (mf_submission()->is_empty() !== true && mf_submission()->success) {
				if (mf_submission()->redirect !== false) {
					wp_redirect(mf_submission()->redirect);
					exit;
				}
			}
		}
	}
}
