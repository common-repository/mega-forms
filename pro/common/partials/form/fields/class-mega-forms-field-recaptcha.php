<?php

/**
 * @link       https://wpmegaforms.com
 * @since      1.4.7
 *
 */

/**
 * reCaptcha field type class
 * Create the field instance, but don't register it using `MF_Fields::register(new MegaForms_ReCaptcha());`.
 * This field will be used conditionally, similar to honeypot field.
 *
 * @author     Ali Khallad <ali@wpali.com>
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class MegaForms_ReCaptcha extends MF_Field
{
    public $type            = 'recaptcha';
    public $isUnlistedField = true;
    public $hasJSDependency = true;
    public function get_field_js_dependencies()
    {
        $recaptcha_type = mfget_option('recaptcha_type', 'legacy_v2');
        $recaptcha_key  = mfget_option('recaptcha_site_key', '');
        $deps           = array();
        $src            = '';
        if ($recaptcha_type === 'legacy_v2') {
            // use legacy recaptcha
            $src = 'https://www.google.com/recaptcha/api.js';
        }
        if ($recaptcha_type === 'v2') {
            // Use reCaptcha Enterprise
            $src = 'https://www.google.com/recaptcha/enterprise.js';
        } elseif ($recaptcha_type === 'v3') {
            $src = 'https://www.google.com/recaptcha/enterprise.js?render=' . $recaptcha_key;
        }

        if (!empty($src)) {
            $deps['mf-recaptcha'] = array(
                'src'  => $src,
                'deps' => array(),
                'ver'  => null,
            );

            // Add inline JS for reCaptcha v3
            if ($recaptcha_type === 'v3') {
                $inline_script           = $this->get_recaptcha_v3_inline_script();
                $inline_script           = str_replace(array('<script>', '</script>'), '', $inline_script);
                $deps['mf-recaptcha-v3'] = array(
                    'handle'   => 'mf-recaptcha',
                    'inline'   => $inline_script,
                    'position' => 'after',
                );
            }
        }

        return $deps;
    }
    public function get_field_display($value = null)
    {
        // Define arguements array and pass required arguements
        $args = $this->build_field_display_args();
        // Define additional arguements
        $args['id'] = mf_api()->get_field_key($this->form_id, 'recaptcha');
        // Get recaptcha type and keys
        $recaptcha_type = mfget_option('recaptcha_type', 'legacy_v2');
        $site_key       = mfget_option('recaptcha_site_key', '');
        $secret_key     = mfget_option('recaptcha_secret_key', '');
        $project_id     = mfget_option('recaptcha_project_id', '');
        $api_key        = mfget_option('recaptcha_api_key', '');
        // Prepare the markup
        $html = '';

        if ($recaptcha_type === 'v3' && !empty($site_key) && !empty($project_id) && !empty($api_key)) {
            // Use reCaptcha Enterprise v3
            $html .= '<div class="g-recaptcha-v3" data-sitekey="' . esc_attr($site_key) . '" data-action="' . $this->form_id . '_MEGAFORM_SUBMISSION"></div>';
            $html .= '<input type="hidden" id="' . esc_attr($args['id']) . '" class="mf-recaptcha-response" name="g-recaptcha-response" value="">';
        } elseif ($recaptcha_type === 'v2' && !empty($site_key) && !empty($project_id) && !empty($api_key)) {
            // Use reCaptcha Enterprise v2
            $html .= '<div class="g-recaptcha" data-sitekey="' . esc_attr($site_key) . '" data-action="' . $this->form_id . '_MEGAFORM_SUBMISSION"></div>';
        } elseif ($recaptcha_type === 'legacy_v2' && !empty($site_key) && !empty($secret_key)) {
            // Use legacy reCaptcha
            $html .= '<div class="g-recaptcha" data-sitekey="' . esc_attr($site_key) . '"></div>';
        } else {
            $html .= '<span class="mf-notice-holder mf_notice">' . esc_html__('Please configure reCaptcha in the plugin settings.', 'megaforms') . '</span>';
        }

        $args['content'] = $html;

        // retrieve and return the input markup
        $input = mfinput('custom', $args, $this->is_editor);

        return $input;
    }

    /**********************************************************************
     ***************************** Helpers ********************************
     **********************************************************************/
    protected function get_field_container_classes()
    {

        $classes  = parent::get_field_container_classes();
        $classes .= ' mf_recaptcha_' . mfget_option('recaptcha_type', 'legacy_v2');

        return $classes;
    }
    /**********************************************************************
     ********************* Validation && Sanitazation *********************
     **********************************************************************/
    public function sanitize_settings()
    {
        // Prepare field settings
        // Since this is unlisted field, we don't want all the settings loaded by `parent::sanitize_settings()` .
        // Thus, we'll consrutct our own settings and ensure we have the required items ( id, formId, type ).
        $sanitized                   = array();
        $sanitized['id']             = $this->field_id;
        $sanitized['formId']         = absint($this->form_id);
        $sanitized['type']           = wp_strip_all_tags($this->type);
        $sanitized['field_required'] = true;
        return $sanitized;
    }
    public function required_check($value)
    {
        $data           = mfget('g-recaptcha-response', $_POST['posted_data'] ?? array());
        $recaptcha_type = mfget_option('recaptcha_type', 'legacy_v2');
        if (in_array($recaptcha_type, array('v2', 'legacy_v2')) && empty($data)) {
            $error = __('Please verify that you\'re human by checking the reCAPTCHA box.', 'megaforms');
            if (empty(mfget_option('recaptcha_site_key'))) {
                $error = __('Please configure reCaptcha in the plugin settings.', 'megaforms');
            }
            return array(
                'notice' => $error,
            );
        }

        return true;
    }
    public function validate($value, $context = '')
    {
        $recaptcha_response = mfget('g-recaptcha-response', mf_submission()->posted ?? array());
        $recaptcha_type     = mfget_option('recaptcha_type', 'legacy_v2');

        if (empty($recaptcha_response)) {
            return $this->get_error_response('missing-input-response');
        }

        $user_data = $this->get_user_data();
        $response  = $this->send_recaptcha_request($recaptcha_type, $recaptcha_response, $user_data);

        if (is_wp_error($response)) {
            return $this->get_error_response('request-failed', $response->get_error_message());
        }

        $response_body = json_decode(wp_remote_retrieve_body($response), true);

        if ($recaptcha_type === 'legacy_v2') {
            return $this->validate_legacy_response($response_body);
        } else {
            return $this->validate_enterprise_response($response_body);
        }
    }
    public function sanitize($value)
    {
        // Return an empty string to prevent the field from being processed beyond the validation step in the form submission process.
        // unlisted fields, do not get processed after the validation step if their sanitized value is empty.
        return '';
    }
    /**********************************************************************
     ************************** Private Utils *****************************
     **********************************************************************/
    private function get_recaptcha_v3_inline_script()
    {
        ob_start();
		?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Action for non-AJAX form submission to perform actions before the form is submitted
                // This hook allows us to interrupt the submission until we get the reCAPTCHA token and add it to the form.
                megaForms.add_action('beforeFormSubmit', async function(data) {
                    if (!data.isAjax) {
                        if (megaForms.dev_env) {
                            console.log('Starting reCAPTCHA verification...');
                        }
                        await new Promise(async (resolve, reject) => {
                            try {
                                const token = await megaForms.executeReCaptcha(data.form);
                                data.form.querySelector('.mf-recaptcha-response').value = token;

                                if (megaForms.dev_env) {
                                    console.log('ReCAPTCHA token received and added to the form.');
                                }

                                setTimeout(function(){ resolve(); }, 10000);

                            } catch (error) {
                                if (megaForms.dev_env) {
                                    console.log('Token retrieval failed', error);
                                }
                                reject(error);
                            }
                        });
                    }
                });
                // Filter for AJAX form submission to change the posted data before the AJAX request is sent.
                // This hook allows us to add interrupt the submission until we get the reCAPTCHA token and add it to the posted data.
                megaForms.add_filter('ajaxSubmissionPostedData', async function(postedData, extraData) {
                    if (megaForms.dev_env) {
                        console.log('Starting AJAX reCAPTCHA verification...');
                    }

                    return await new Promise(async (resolve, reject) => {
                        try {
                            const token = await megaForms.executeReCaptcha(extraData.form);
                            // Add the reCAPTCHA token to the form
                            extraData.form.querySelector('.mf-recaptcha-response').value = token;
                            // Update the posted_data with the new reCAPTCHA token
                            postedData['g-recaptcha-response'] = token;

                            if (megaForms.dev_env) {
                                console.log('ReCAPTCHA token received and added to the request.');
                            }

                            resolve(postedData);
                        } catch (error) {
                            if (megaForms.dev_env) {
                                console.log('AJAX token retrieval failed');
                            }
                            reject(error);
                        }
                    });
                });
                // A function to execute reCaptcha
                megaForms.executeReCaptcha = async function(form) {
                    const recaptchaElement = form.querySelector('.g-recaptcha-v3');
                    if (recaptchaElement) {
                        const siteKey = recaptchaElement.getAttribute('data-sitekey');
                        const action = recaptchaElement.getAttribute('data-action');
                        return new Promise((resolve) => {
                            grecaptcha.enterprise.ready(async () => {
                                const token = await grecaptcha.enterprise.execute(siteKey, {
                                    action: action
                                });
                                resolve(token);
                            });
                        });
                    }
                };
            });
        </script>
		<?php
        return ob_get_clean();
    }
    private function get_user_data()
    {
        return array(
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'user_ip'    => $this->get_user_ip(),
        );
    }

    private function get_user_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    private function send_recaptcha_request($recaptcha_type, $recaptcha_response, $user_data)
    {
        if ($recaptcha_type === 'legacy_v2') {
            return $this->send_legacy_request($recaptcha_response, $user_data['user_ip']);
        } else {
            return $this->send_enterprise_request($recaptcha_response, $user_data);
        }
    }

    private function send_legacy_request($recaptcha_response, $user_ip)
    {
        $secret_key = mfget_option('recaptcha_secret_key', '');
        return wp_remote_post(
            'https://www.google.com/recaptcha/api/siteverify',
            array(
                'body' => array(
                    'secret'   => $secret_key,
                    'response' => $recaptcha_response,
                    'remoteip' => $user_ip,
                ),
            )
        );
    }

    private function send_enterprise_request($recaptcha_response, $user_data)
    {
        $project_id = mfget_option('recaptcha_project_id', '');
        $site_key   = mfget_option('recaptcha_site_key', '');
        $api_key    = mfget_option('recaptcha_api_key', '');

        return wp_remote_post(
            "https://recaptchaenterprise.googleapis.com/v1/projects/{$project_id}/assessments?key={$api_key}",
            array(
                'body'    => json_encode(
                    array(
                        'event' => array(
                            'token'          => $recaptcha_response,
                            'expectedAction' => $this->form_id . '_MEGAFORM_SUBMISSION',
                            'siteKey'        => $site_key,
                            'userAgent'      => $user_data['user_agent'],
                            'userIpAddress'  => $user_data['user_ip'],
                        ),
                    )
                ),
                'headers' => array('Content-Type' => 'application/json'),
            )
        );
    }


    private function validate_legacy_response($response_body)
    {
        if (!isset($response_body['success']) || $response_body['success'] !== true) {
            $error_code = $response_body['error-codes'][0] ?? 'unknown-error';
            return $this->get_error_response($error_code);
        }

        // If we've made it this far, the validation was successful
        return true;
    }

    private function validate_enterprise_response($response_body)
    {
        // Check for API errors first
        if (isset($response_body['error'])) {
            $error_message = $response_body['error']['message'] ?? 'Unknown API error';
            return $this->get_error_response('api-error', $error_message);
        }

        // Check if the token is valid
        if (!isset($response_body['tokenProperties']['valid']) || $response_body['tokenProperties']['valid'] !== true) {
            $invalid_reason = $response_body['tokenProperties']['invalidReason'] ?? 'Unknown reason';
            return $this->get_error_response('invalid-token', "$invalid_reason");
        }

        // Verify the action
        $expected_action = $this->form_id . '_MEGAFORM_SUBMISSION';
        $actual_action   = $response_body['tokenProperties']['action'] ?? '';
        if ($actual_action !== $expected_action) {
            return $this->get_error_response('action-mismatch', "expected '$expected_action', got '$actual_action'");
        }

        // Evaluate the risk score
        $score     = $response_body['riskAnalysis']['score'] ?? 0;
        $threshold = apply_filters('mf_recaptcha_score_threshold', 0.6); // You can adjust this threshold based on your risk tolerance

        if ($score < $threshold) {
            $reasons     = $response_body['riskAnalysis']['reasons'] ?? array('Unknown');
            $reasons_str = implode(', ', $reasons);
            return $this->get_error_response('high-risk-score', "(score: $score, reasons: $reasons_str)");
        }

        // If we've made it this far, the validation was successful
        return true;
    }

    private function get_error_response($error_code, $custom_message = '', $fallback_message = '')
    {
        $error_messages = array(
            'missing-input-secret'         => __('The reCaptcha secret key is missing.', 'megaforms'),
            'invalid-input-secret'         => __('The provided reCaptcha secret key is invalid or malformed.', 'megaforms'),
            'missing-input-response'       => __('The reCaptcha response is missing.', 'megaforms'),
            'invalid-input-response'       => __('The reCaptcha response is invalid or malformed.', 'megaforms'),
            'bad-request'                  => __('The captcha challenge evaluation failed, please refresh and try again.', 'megaforms'),
            'timeout-or-duplicate'         => __('The captcha challenge timed out, please try again.', 'megaforms'),
            'request-failed'               => __('Could not validate the captcha challenge, please refresh and try again.', 'megaforms'),
            'enterprise-validation-failed' => __('Enterprise reCaptcha validation failed.', 'megaforms'),
            'api-error'                    => __('reCAPTCHA API error: %s', 'megaforms'),
            'invalid-token'                => __('Invalid reCAPTCHA token: %s', 'megaforms'),
            'action-mismatch'              => __('reCAPTCHA action mismatch: %s', 'megaforms'),
            'high-risk-score'              => __('reCAPTCHA detected a high-risk interaction: %s', 'megaforms'),
            'unknown-error'                => __('An unknown error occurred during reCaptcha validation.', 'megaforms'),
        );

        if (isset($error_messages[$error_code])) {
            $message_template = $error_messages[$error_code];
            if (strpos($message_template, '%s') !== false) {
                $error_message = sprintf($message_template, $custom_message);
            } else {
                $error_message = $message_template;
            }
        } elseif (!empty($fallback_message)) {
            $error_message = $fallback_message;
        } else {
            $error_message = $error_messages['unknown-error'];
        }

        return array(
            'notice'      => $error_message,
            'notice_code' => 'recaptcha_invalid',
        );
    }
}

MF_Fields::register(new MegaForms_ReCaptcha());
