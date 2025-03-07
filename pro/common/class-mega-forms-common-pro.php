<?php

/**
 * @link       https://wpmegaforms.com
 * @since      1.0.8
 *
 */

/**
 * Common functionality of the pro plugin.
 *
 * @author     Ali Khallad <ali@wpali.com>
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Mega_Forms_Common_Pro
{

	private $plugin_name;
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.6
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
	 * @since    1.0.6
	 */
	private function load_dependencies()
	{
		// require a loader file to load all existing form add-ons
		require_once MEGAFORMS_DIR_PATH . 'pro/common/partials/loader.php';
	}
	/**
	 * Modify a from data as soon as it's pulled from the database ( eg;  fields, field_types, containers, actions, settings ).
	 *
	 * @since    1.4.8
	 */
	public function modify_get_form_data($form_data)
	{

		// Check if reCaptcha is enabled globally and for the current form
		if (mfget_option('recaptcha_status', false) && mfget('recaptcha_enabled', $form_data->settings, true)) {

			// Do not modify the form data in the admin area, unless it's an AJAX request
			if (is_admin() && !wp_doing_ajax()) {
				return $form_data;
			}

			$form_id = $form_data->ID;
			/**
			 * If recaptcha is enabled, add `recaptcha` type to field types and append it to the form.
			 * 1. Adding to `field_types` will ensure that the reCaptcha JS dependecies will be loaded dynamically from `MF_Shortcodes:the_form`.
			 * 2. Adding to `fields` will ensure the reCaptcha field will be rendered in the frontend
			 *
			 * We are loading the field here manually because `$this->isUnlistedField` is set to `true` for the field.
			 */

			$recaptcha_field    = array(
				'type'   => 'recaptcha',
				'id'     => 'recaptcha',
				'formId' => $form_id,
			);
			$mf_recaptcha_field = MF_Fields::get($recaptcha_field['type'], array('field' => $recaptcha_field));
			// Add to `field_types`
			$form_data->field_types[] = $mf_recaptcha_field->type;
			// Add to `fields`
			$form_data->fields[$recaptcha_field['id']] = $mf_recaptcha_field->sanitize_settings();
			// Add to `containers`
			if (!empty($form_data->containers['data'])) {
				$last_row_index = count($form_data->containers['data']) - 1;
				while ($last_row_index >= 0 && $form_data->containers['data'][$last_row_index]['type'] !== 'row') {
					--$last_row_index;
				}

				if ($last_row_index >= 0) {
					$last_col_index = count($form_data->containers['data'][$last_row_index]['columns']) - 1;
					$form_data->containers['data'][$last_row_index]['columns'][$last_col_index]['fields'][] = $recaptcha_field['id'];
				}
			}
		}

		return $form_data;
	}
	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.8
	 */
	public function form_tag_attributes($attrs, $form)
	{

		// Enable AJAX conditionally
		if (mfget_option('enable_ajax', true)) {
			$attrs['class'] .= ' ajax-mega-form';
		}

		return $attrs;
	}

	/**
	 * Add more hidden inputs for the pro version.
	 *
	 * @since    1.0.8
	 */
	public function after_hidden_inputs($form)
	{

		// Add hidden inputs related to "paged" forms
		if (mfget('page', $form->containers['settings'])) {

			// Add current page input if this is a paged form
			$current_page = 1;
			if (!mf_submission()->is_empty()) {
				$number       = mfget('_mf_current_page', mf_submission()->posted, mfget('page', mf_submission()->args, 1));
				$current_page = mf_submission()->success && mf_submission()->context == 'page' ? $number + 1 : $number;
			}

			echo '<input type="hidden" name="_mf_current_page" value="' . esc_attr($current_page) . '">';

			// If the form is set to "Auto Advance", we'll add another hidden field to indicate this.
			if (mfget('enable_auto_advance', $form->containers['settings']['page']) === 'yes') {
				$mode = mfget('auto_advance_mode', $form->containers['settings']['page']);
				if ($mode == 'all_pages') {
					echo '<input type="hidden" name="_mf_auto_advance" value="all">';
				} else {
					$selected_pages = mfget('auto_advance_pages', $form->containers['settings']['page']);
					if (!empty($selected_pages)) {
						$selected_pages = str_replace(' ', '', trim($selected_pages, ','));
						echo '<input type="hidden" name="_mf_auto_advance" value="' . esc_attr($selected_pages) . '">';
					}
				}
			}
		}

		// Add `save and continue` token input
		if (isset($_GET['mf_token']) && hash_equals(wp_hash($form->ID), mfget('mf_hash'))) {
			echo '<input type="hidden" name="_mf_resume_token" value="' . esc_attr(mfget('mf_token')) . '">';
		}
	}
	/**
	 * Display the markup for save and continue button if enabled.
	 *
	 * @since    1.0.8
	 */
	public function save_and_continue_button($form)
	{
		if (mfget('enable_save_and_continue', $form->settings, false)) {
			$button_text = mfget('save_and_continue_text', $form->settings, __('Save and Continue Later', 'megaforms'));

			echo get_mf_button(
				'submit',
				$button_text,
				array(
					'name'           => 'mform_save',
					'class'          => 'button mf-save-btn',
					'formnovalidate' => 'formnovalidate',
				)
			);
		}
	}
	/**
	 * Register the tasks that will run on daily basis using wp cron.
	 *
	 * @see     MF_Crons
	 * @since   1.0.7
	 */
	public function daily_cron_tasks()
	{
		// Remove tmp files
		mf_files()->clean_temp_files();
	}

	/**
	 * Validate form submission.
	 *
	 * @since    1.0.7
	 */
	// public function validate_submission($object)
	// {
	// If something is wrong, throw an exception
	// $error = false;
	// if ($error) {
	// throw new Exception(__('Submission failed, please refresh and try again.', 'megaforms'));
	// }
	// }
	/**
	 * Validate custom form submission ( validate page/save ).
	 *
	 * @since    1.0.7
	 */
	public function validate_custom_submission($object)
	{

		// Only validate 'page', `save`, or `continue` submissions
		if ($object->context !== 'page' && $object->context !== 'save' && $object->context !== 'continue') {
			return;
		}

		// Check if form exists
		if (!$object->form || empty($object->posted)) {
			throw new Exception($object->get_validation_text('form_validation_invalid_submission'));
		}

		/**
		 * Validate page submission
		 *
		 */
		if ($object->context == 'page') {
			// Check if current page number is set
			$page = mfget('page', $object->args, false);
			if (!$page) {
				throw new Exception(__('There was an error, please refresh the page and try again.', 'megaforms'));
			}

			// Extract page field from the existing form fields based on form containers
			$page_fields    = array();
			$submitted_page = absint($page);
			$loop_page      = 1;
			foreach ($object->form->containers['data'] as $data) {
				// set `loop_page` on each page_break ( where container type is 'page' )
				if ('page' == $data['type']) {
					++$loop_page;
					continue;
				}

				// Move to next container if this is not a row ( rows are the only container type that holds the field ids )
				if ('row' !== $data['type']) {
					continue;
				}

				// Only store page fields when the `submitted_page` is equal to the `loop_page`
				if ($submitted_page !== $loop_page) {
					if ($loop_page > $submitted_page) {
						break;
					} else {
						continue;
					}
				}

				// Loop through row columns and search for field ID
				$columns = !empty($data['columns']) ? $data['columns'] : array();
				if (!empty($columns)) {
					foreach ($columns as $column) {
						$col_fields = !empty($column['fields']) ? $column['fields'] : array();
						foreach ($col_fields as $field_id) {
							// Extract page fields data from `$this->form->fields` by id and save it to `page_fields`
							if (isset($object->form->fields[$field_id])) {
								$page_fields[$field_id] = $object->form->fields[$field_id];
							}
						}
					}
				}
			}

			// Filter whether to save entry after each page submission
			$save_page = apply_filters('mf_save_paginated_form_pages', false);
			if ($save_page) {
				// An exception will be thrown if the form is not valid.
				$object->validate_submitted_form();
			}

			// Validate page fields
			$result = $object->validate_fields($page_fields);
			if ($result['valid'] === false) {
				throw new Exception($object->get_validation_text('form_validation_errors'));
			} else {
				$object->custom_submission_valid = true;  // Make sure to set the custom validation property to true.
				$object->submission_values       = $result['values']; // Make sure page submission values are stored in the object.
				// Check whether we need to save this page as an entry or not
				if ($save_page) {
					// Check if there is an existing entry for this submission
					// We'll use safety token `_mf_s_token` to ensure non-duplicate token.
					// The value of nonces can repeat for the same user,
					// while safety token is different on each page load.
					$nonce        = mfpost('_mf_nonce', $object->posted);
					$wp_nonce     = mfpost('_mf_extra_nonce', $object->posted);
					$safety_token = mfpost('_mf_s_token', $object->posted);
					$token_id     = 'entry_' . $object->form->ID . '_' . wp_hash($nonce . '-' . $wp_nonce . '-' . $safety_token);
					$entry_id     = mf_session()->get($token_id);
					// If there is an entry already, update it.
					// Otherwise, create a new one.
					if ($entry_id) {
						// Update the existing entry
						$object->save_entry_changes($entry_id);
					} else {
						// Create a new entry and save the ID
						$entry_id = $object->create_entry();
						if ($entry_id) {
							mf_session()->set($token_id, $entry_id);
						}
					}
				}
			}
		}
		/**
		 * Validate save and continue submission
		 *
		 */
		elseif ($object->context == 'save') {

			if (mfget('enable_save_and_continue', $object->form->settings, false)) {
				// Validate the form ( avoid spam submissions + manipulated forms ) + fields
				$fields = mfget_form_fields($object->form);
				$object->validate_submitted_form();
				$object->validate_fields($fields);

				// Generate a valid token and set a transiet with that token
				$form_id      = $object->form->ID;
				$resume_token = mfpost('_mf_resume_token', $object->posted);

				if (!empty($resume_token) && false !== get_transient('wp_megaforms_' . $resume_token)) {
					$token = $resume_token;
				} else {
					$nonce    = mfpost('_mf_nonce', $object->posted);
					$wp_nonce = mfpost('_mf_extra_nonce', $object->posted);
					$token    = wp_hash($wp_nonce . '-' . $nonce);
				}

				$referrer = htmlspecialchars_decode(urldecode(mfpost('_mf_referrer', $object->posted)));
				$link     = add_query_arg(
					array(
						'mf_token' => $token,
						'mf_hash'  => wp_hash($form_id),
					),
					home_url($referrer)
				);

				set_transient(
					'wp_megaforms_' . $token,
					array(
						'page'   => mfpost('_mf_current_page', $object->posted, false),
						'fields' => $object->posted['fields'],
					),
					MONTH_IN_SECONDS
				);

				// Set success message and the custom validation property to true.
				$success_message  = '';
				$success_message .= '<p style="text-align: center;">';
				$success_message .= __('Please use the following link to return and complete this form from any computer.', 'megaforms');
				$success_message .= '<br></br><a class="mf-continue-link" href="' . $link . '">' . $link . '</a><br></br>';
				$success_message .= __('Note: This link will expire after 30 days.', 'megaforms');
				$success_message .= '</p>';

				$object->message                 = $success_message;
				$object->custom_submission_valid = true;
			}
		} elseif ($object->context == 'continue') {
			$token = mfget('mf_token');
			$saved = get_transient('wp_megaforms_' . $token);
			if ($saved !== false) {
				if ($saved['page']) {
					$object->args['page'] = absint($saved['page']);
				}
				$object->posted['fields'] = $saved['fields'];
			}
			// Set the custom validation property to true.
			$object->custom_submission_valid = true;
		}
	}

	/**
	 * Customize the success response when a paged form is submitted.
	 *
	 * @since    1.0.7
	 */
	public function customize_ajax_success_response($success_args)
	{
		if (mfget('page', mf_submission()->form->containers['settings']) && in_array(mf_submission()->context, array('form', 'save'))) {
			// Make sure to hide progress indicator when the last page of a paged form is submitted
			$success_args['hideProgressIndicator'] = true;
		}

		return $success_args;
	}
	/**
	 * Handle conditional logic on form actions
	 *
	 * @since    1.3.1
	 */
	public function handle_action_conditional_logic($exec, $action, $posted_values)
	{
		// Pull all the options assigned to the current action
		$action_options = $action->get_action_options();
		// Check if conditional logic is available for this action
		if (in_array('conditional_logic', $action_options)) {
			// Now check whether conditional logic is enabled, if yes, evaluate rules
			// and continue with the execution if all rules are passed.
			$cl_value   = $action->get_setting_value('conditional_logic');
			$is_enabled = mfget_bool_value(mfget('enable', $cl_value));
			if ($is_enabled && isset($cl_value['rules'])) {
				$conditional_logic_option = MF_Extender::get_single_action_option('conditional_logic');
				return $conditional_logic_option->evaluate_rules(mfget('rules', $cl_value), $posted_values);
			}
		}
		return $exec;
	}
	/**
	 * Handle any processes that should run after entry creation
	 *
	 * @since    1.3.2
	 */
	public function handle_entry_processes($entry_id, $entry_meta, $submission_object)
	{

		// Handle processes that should run after the creation of a form related entry
		// Note: entries can be also created as part of pages by returning true for
		// the filter `mf_save_paginated_form_pages`.
		if ($submission_object->context == 'form') {
			// If there is a previosuly created entry as part of paged form, delete it.
			// We do this because page associated entries are incomplete and doesn't have
			// all the required fields.
			$nonce          = mfpost('_mf_nonce', $submission_object->posted);
			$wp_nonce       = mfpost('_mf_extra_nonce', $submission_object->posted);
			$safety_token   = mfpost('_mf_s_token', $submission_object->posted);
			$token_id       = 'entry_' . $submission_object->form->ID . '_' . wp_hash($nonce . '-' . $wp_nonce . '-' . $safety_token);
			$older_entry_id = mf_session()->get($token_id);
			// If there is an entry already, update it.
			// Otherwise, create a new one.
			if ($older_entry_id) {
				mf_api()->delete_entry($older_entry_id);
				mf_api()->set_form_entry_count($submission_object->form->ID);
			}
		}
	}
}
