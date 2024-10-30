<?php

/**
 * @link       https://wpmegaforms.com
 * @since      1.0.3
 *
 * @package    Mega_Forms
 * @subpackage Mega_Forms/common/partials/fields
 */

/**
 * Text field type class
 *
 * @package    Mega_Forms
 * @subpackage Mega_Forms/common/partials/fields
 * @author     Ali Khallad <ali@wpali.com>
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class MegaForms_Hidden extends MF_Field
{

	public $type             = 'hidden';
	public $editorSettings   = array(
		'general'  => array(
			'field_default',
			'field_visibility',
		),
		'advanced' => array(
			'field_non_submitting',
		),
	);
	public $editorExceptions = array(
		'field_required',
		'field_label_visibility',
		'field_description',
		'field_description_position',
		'field_css_class',
		'field_placeholder',
		'field_default',
		'field_visibility',
	);
	public $isHiddenField    = true;

	public function get_field_title()
	{
		return esc_attr__('Hidden', 'megaforms');
	}

	public function get_field_icon()
	{
		return 'mega-icons-eye-slash';
	}

	public function get_field_display($value = null)
	{

		// Define arguements array and pass required arguements
		$args          = $this->build_field_display_args();
		$args['value'] = $value;

		// retrieve and return the input markup
		if ($this->is_editor) {
			$input = mfinput('text', $args, $this->is_editor);
		} else {
			$input = mfinput('hidden', $args, $this->is_editor);
		}

		return $input;
	}
	/**********************************************************************
	 ********************** Fields Options Markup *************************
	 **********************************************************************/
	/**
	 * Returns the display for the field's "Non-Submitting" options.
	 * This option allows user to set this field and skip it on submission,
	 * so it's not saved to the entry.
	 *
	 * @return string
	 */
	protected function field_non_submitting()
	{

		$label     = __('Non-Submitting Field', 'megaforms');
		$desc      = __('Enable this for fields used only in processing. They won\'t be saved or included in the form submission, ideal for internal PHP operations.', 'megaforms');
		$field_key = 'field_non_submitting';

		$args['id']          = $this->get_field_key('options', $field_key);
		$args['label']       = $label;
		$args['after_label'] = $this->get_description_tip_markup($label, $desc);
		$args['value']       = $this->get_setting_value($field_key);

		$input = mfinput('switch', $args, true);
		return $input;
	}

	/**********************************************************************
	 ********************* Validation && Sanitazation *********************
	 **********************************************************************/
	public function sanitize_settings()
	{

		$sanitized                         = parent::sanitize_settings();
		$sanitized['field_non_submitting'] = sanitize_text_field($this->get_setting_value('field_non_submitting'));

		return $sanitized;
	}
}

MF_Fields::register(new MegaForms_Hidden());
