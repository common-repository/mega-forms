<?php

/**
 * Mega Forms Ajax Class
 *
 * @link       https://wpmegaforms.com
 * @since      1.0.0
 *
 * @package    Mega_Forms
 * @subpackage Mega_Forms/public/partials
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class MF_Public_Ajax extends MF_Ajax
{

	public function submit_form()
	{
		// Get data from the request
		$has_json         = $this->maybe_get_value('has_json');
		$maybe_decode     = $has_json !== null ? $has_json : false;
		$form_id          = $this->get_value('form_id');
		$context          = $this->maybe_get_value('context');
		$posted_data      = $this->get_value('posted_data', false, $maybe_decode);
		$args             = $this->maybe_get_value('args', false, $maybe_decode);
		$refreshed_fields = array();

		// Process the submission
		mf_submission()->exec($form_id, $posted_data, $context, $args);

		// Prepare the new HTML for the fields that needs refreshing
		if (isset($args['refresh_fields'])) {
			// This is mainly used for file upload field to replace initial view with "uploaded file" view on error or page submit then return..etc
			// However, this can expanded to be used with other fields.
			foreach ($args['refresh_fields'] as $field_key) {
				if (($pos = strpos($field_key, '_mfield_')) !== false) {
					$field_id         = substr($field_key, $pos + 8);
					$field_wrapper_id = sprintf('mf_%d_field_%d', $form_id, $field_id);
					$field            = mf_submission()->form->fields[$field_id] ?? false;
					if ($field) {
						$fieldObj                                  = MF_Fields::get($field['type'], array('field' => $field));
						$refreshed_fields['#' . $field_wrapper_id] = $fieldObj->get_the_field(mf_submission()->get_value($field_id));
					}
				}
			}
		}

		// Make any necessary redirections
		if (!mf_submission()->is_empty() && mf_submission()->success) {
			if (mf_submission()->redirect !== false) {
				// If this submission requires a redirect, we don't need to pass any data except from the URL
				$this->success(
					array(
						'redirect' => mf_submission()->redirect,
					)
				);
			} else {
				// prepare response data
				$response = array(
					'keep_form' => mf_submission()->keep_form, // Whether to keep the form in view or hide it
					'scrollTop' => true, // Whether to scroll to the success message or not
				);

				// Pass the new HTML for the fields that needs refreshing after the response is recieved/updated.
				// Initially made for fields, but can be expanded to be used to dynamically change page elements
				// when the form submitted.
				if (!empty($refreshed_fields)) {
					$response['refreshedFieldsContainer'] = '.mform_container';
					$response['refreshedFields']          = $refreshed_fields;
				}

				// Return the response
				$this->success(
					get_mf_submission_msg_html('success', mf_submission()->message),
					apply_filters(
						'mf_ajax_submit_success_response',
						$response,
						$form_id
					)
				);
			}
		} else {
			$response = array();
			// Make sure notices are wrapped in the correct HTML
			if (!empty(mf_submission()->notices)) {
				$response['notices'] = array();
				foreach (mf_submission()->notices as $id => $notice) {
					$field_key                       = mf_api()->get_field_key($form_id, $id);
					$response['notices'][$field_key] = get_mf_notice_html($notice);
				}
			}
			if (!empty(mf_submission()->compound_notices)) {
				$response['compound_notices'] = array();
				foreach (mf_submission()->compound_notices as $cm_id => $cm_notice) {
					foreach ($cm_notice as $cd_key => $cd_val) {
						$cm_notice[$cd_key] = get_mf_notice_html($cd_val, 'compound');
					}
					$subfield_key                                = mf_api()->get_field_key($form_id, $cm_id);
					$response['compound_notices'][$subfield_key] = $cm_notice;
				}
			}

			// Set `scrollTop` to jump to the top after the response is recieved/updated
			$response['scrollTop'] = true;

			// Pass the new HTML for the fields that needs refreshing after the response is recieved/updated.
			// Initially made for fields, but can be expanded to be used to dynamically change page elements
			// when the form submitted.
			if (!empty($refreshed_fields)) {
				$response['refreshedFieldsContainer'] = '.mform_container';
				$response['refreshedFields']          = $refreshed_fields;
			}
			// throw an error with the error message + the prepared notices, if available.
			throw new MF_Ajax_Exception(
				get_mf_submission_msg_html('error', mf_submission()->message),
				apply_filters('mf_ajax_submit_fail_response', $response, $form_id)
			);
		}
	}

	public function get_form_nonce_values()
	{

		$form_id = $this->get_value('form_id');

		// Get referrer
		$referrer = wp_doing_ajax() && wp_get_referer() ? esc_attr(wp_get_referer()) : esc_attr(wp_unslash($_SERVER['REQUEST_URI']));
		// Clean URL, only keep request path
		if (strpos($referrer, 'http') !== false) {
			$referrer_url = parse_url($referrer);
			if (!empty($referrer_url['path'])) {
				$referrer = $referrer_url['path'];
			} else {
				$referrer = '/';
			}
		}

		// Get session tokens, or set them if not available
		$session_token_id    = get_mf_session_token_id($form_id, $referrer);
		$session_referrer_id = get_mf_session_referrer_id($form_id, $referrer);
		$form_token          = mf_session()->get($session_token_id);
		$form_referrer       = mf_session()->get($session_referrer_id);

		if (empty($form_token) || empty($form_referrer)) {
			$form_token    = esc_attr(wp_generate_uuid4());
			$form_referrer = $referrer;
			mf_session()->set($session_token_id, $form_token);
			mf_session()->set($session_referrer_id, $form_referrer);
		}

		// Create nonce
		$wp_nonce = wp_create_nonce('mf_form_' . $form_id);
		// Get cookie data
		$cookie_value      = mf_session()->get_cookie_value();
		$cookie_data       = $cookie_value ? explode('||', $cookie_value) : false;
		$cookie_expiration = $cookie_data ? $cookie_data[1] : false;

		// Check for missing data before sending a success response.
		$error_message = __('There was an issue generating security tokens for this form.', 'megaforms');

		if (empty($form_token)) {
			throw new MF_Ajax_Exception(get_mf_submission_msg_html('error', $error_message . ' (Error Code: MF101)'));
		}

		if (empty($form_referrer)) {
			throw new MF_Ajax_Exception(get_mf_submission_msg_html('error', $error_message . ' (Error Code: MF102)'));
		}

		if (empty($wp_nonce)) {
			throw new MF_Ajax_Exception(get_mf_submission_msg_html('error', $error_message . ' (Error Code: MF103)'));
		}

		if (empty($cookie_value)) {
			throw new MF_Ajax_Exception(get_mf_submission_msg_html('error', $error_message . ' (Error Code: MF104)'));
		}

		if (empty($cookie_expiration)) {
			throw new MF_Ajax_Exception(get_mf_submission_msg_html('error', $error_message . ' (Error Code: MF105)'));
		}

		// If we have all data, return a response
		$this->success(
			'',
			array(
				'form_token'        => esc_attr($form_token),
				'form_referrer'     => $form_referrer,
				'wp_nonce'          => esc_attr($wp_nonce),
				'cookie_value'      => esc_attr($cookie_value),
				'cookie_expiration' => esc_attr($cookie_expiration),
			)
		);
	}
}
