<?php
/**
 * Quote widget
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */


$options = [

	'title' => [
		'type' => 'text',
		'label' => __( 'Title', 'blc' ),
		'field_attr' => [ 'id' => 'widget-title' ],
		'design' => 'inline',
		'value' => __( 'Quote', 'blc' ),
		'disableRevertButton' => true,
	],

	// 'quote_text' => [
	// 	'label' => __( 'Quote text', 'blc' ),
	// 	'type' => 'textarea',
	// 	'value' => '',
	// 	'design' => 'inline',
	// 	'disableRevertButton' => true,
	// ],

	'quote_text' => [
		'label' => __( 'Text', 'blc' ),
		'type' => 'wp-editor',
		'design' => 'inline',
		'desc' => __( 'You can add here some arbitrary HTML code.', 'blc' ),
		'disableRevertButton' => true,
		'setting' => [ 'transport' => 'postMessage' ],

		'mediaButtons' => false,
		'tinymce' => [
			'toolbar1' => 'bold,italic,link,undo,redo',
		],
	],

	'quote_author' => [
		'type' => 'text',
		'label' => __( 'Author Name', 'blc' ),
		'field_attr' => [ 'id' => 'widget-title' ],
		'design' => 'inline',
		'value' => __( 'John Doe', 'blc' ),
		'disableRevertButton' => true,
	],

	'quote_has_by_label' => [
		'type'  => 'ct-switch',
		'label' => __( 'Author Label', 'blc' ),
		'value' => 'yes',
		'design' => 'inline-full',
		'disableRevertButton' => true,
	],

	'quote_avatar' => [
		'label' => __('Author Avatar', 'blc'),
		'type' => 'ct-image-uploader',
		'design' => 'inline',
		'value' => [ 'attachment_id' => null ],
		'emptyLabel' => __('Select Image', 'blc'),
		'filledLabel' => __('Change Image', 'blc'),
	],

];
