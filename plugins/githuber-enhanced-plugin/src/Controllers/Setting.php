<?php
/**
 * Class Setting
 *
 * @author Terry Lin
 * @link https://terryl.in/githuber
 *
 * @package Githuber
 * @since 1.1.0
 * @version 1.1.0
 */

namespace Githuber\Controller;

class Setting extends ControllerAbstract {

	public static $settings = [];
	public static $setting_api;

	/**
	 * Constructer.
	 */
	public function __construct() {
		parent::__construct();

		if ( ! self::$setting_api ) {
			self::$setting_api = new \WeDevs_Settings_API();
		}
	}
	
	/**
	 * Initialize.
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'setting_admin_init' ) );
		add_action( 'admin_menu', array( $this, 'setting_admin_menu' ) );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
	}

    /**
     * Load specfic CSS file for the Githuber setting page.
     */
	public function admin_enqueue_styles( $hook_suffix ) {

		if ( false === strpos( $hook_suffix, 'githuber-plugin' ) ) {
			return;
		}
		wp_enqueue_style( 'custom_wp_admin_css', $this->githuber_plugin_url . 'assets/css/admin-style.css' );
	}

    /**
     * Register JS files.
     */
	public function admin_enqueue_scripts( $hook_suffix ) {

	}

	/**
	 * The Githuber setting page, sections and fields.
	 */
	public function setting_admin_init() {

		$sections = array(
			array(
				'id'    => 'githuber_markdown',
				'title' => __( 'Markdown', $this->text_domain )
			),
			
			array(
				'id'    => 'githuber_github',
				'title' => __( 'GitHub', $this->text_domain )
			),

			/*
			array(
				'id'    => 'githuber_others',
				'title' => __( 'Other Settings', $this->text_domain )
			)
			*/
		);

		$fields = array(
			'githuber_markdown' => array(

				array(
					'name'    => 'enable_markdown_for',
					'label'   => __( 'Enable', $this->text_domain ),
					'desc'    => __( 'Enable Markdown for post, pages or comments.', $this->text_domain ),
					'type'    => 'multicheck',
					'default' => array(
						'post' => 'post', 
						'page' => 'page'
					),
					'options' => array(
						'post'    => 'Posts',
						'page'    => 'Pages',
						'comment' => 'Comments'
					)
				),

				array(
					'name'  => '',
					'label' => __( 'Editor Settings', $this->text_domain ),
				),

				array(
					'name'    => 'editor_live_preview',
					'label'   => __( 'Live Preview', $this->text_domain ),
					'desc'    => __( 'Split editor into two panes to display a live preview when editing post.', $this->text_domain ),
					'type'    => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => __( 'Yes', $this->text_domain ),
						'no'  => __( 'No', $this->text_domain )
					)
				),

				array(
					'name'    => 'editor_sync_scrolling',
					'label'   => __( 'Sync Scrolling', $this->text_domain ),
					'desc'    => __( 'Synchronize scrolling of two editor panes by content.', $this->text_domain ),
					'type'    => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => __( 'Yes', $this->text_domain ),
						'no'  => __( 'No', $this->text_domain )
					)
				),

				array(
					'name'    => 'editor_image_paste',
					'label'   => __( 'Image Paste', $this->text_domain ),
					'desc'    => __( 'Easily paste image from clipboard directly into the post content.', $this->text_domain ),
					'type'    => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => __( 'Yes', $this->text_domain ),
						'no'  => __( 'No', $this->text_domain )
					)
				),

				array(
					'name'    => 'editor_html_decode',
					'label'   => __( 'HTML Decode', $this->text_domain ),
					'desc'    => __( 'Allow all HTML tags and attributes in the Markdown Editor. Default false to increase security.', $this->text_domain ),
					'type'    => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => __( 'Yes', $this->text_domain ),
						'no'  => __( 'No', $this->text_domain )
					)
				),

				array(
					'name'  => '',
					'label' => __( 'Editor Style', $this->text_domain ),
				),

				array(
					'name'    => 'editor_toolbar_theme',
					'label'   => __( 'Toolbar', $this->text_domain ),
					'desc'    => __( 'Choose a perferred style for the Editor\'s toolbar.', $this->text_domain ),
					'type'    => 'select',
					'default' => 'default',
					'options' => array(
						'default' => 'default',
						'dark'    => 'dark'
					),
				),

				array(
					'name'    => 'editor_preview_theme',
					'label'   => __( 'Preview Area', $this->text_domain ),
					'desc'    => __( 'Choose a perferred style for the Editor\'s preview area.', $this->text_domain ),
					'type'    => 'select',
					'default' => 'default',
					'options' => array(
						'default' => 'default',
						'dark'    => 'dark'
					),
				),
			
				array(
					'name'    => 'editor_editor_theme',
					'label'   => __( 'Editing Area', $this->text_domain ),
					'desc'    => __( 'Choose a perferred style for the Editor\'s editing area.', $this->text_domain ),
					'type'    => 'select',
					'default' => 'default',
					'options' => array(
						'default'                 => 'default',
						'3024-day'                => '3024-day',
						'3024-night'              => '3024-night',
						'abcdef'                  => 'abcdef',
						'ambiance'                => 'ambiance',
						'ambiance-mobile'         => 'ambiance-mobile',
						'base16-dark'             => 'base16-dark',
						'base16-light'            => 'base16-light',
						'bespin'                  => 'bespin',
						'blackboard'              => 'blackboard',
						'cobalt'                  => 'cobalt',
						'colorforth'              => 'colorforth',
						'dracula'                 => 'dracula',
						'duotone-dark'            => 'duotone-dark',
						'duotone-light'           => 'duotone-light',
						'eclipse'                 => 'eclipse',
						'elegant'                 => 'elegant',
						'erlang-dark'             => 'erlang-dark',
						'gruvbox-dark'            => 'gruvbox-dark',
						'hopscotch'               => 'hopscotch',
						'icecoder'                => 'icecoder',
						'idea'                    => 'idea',
						'isotope'                 => 'isotope',
						'lesser-dark'             => 'lesser-dark',
						'liquibyte'               => 'liquibyte',
						'lucario'                 => 'lucario',
						'material'                => 'material',
						'mbo'                     => 'mbo',
						'mdn-like'                => 'mdn-like',
						'midnight'                => 'midnight',
						'monokai'                 => 'monokai',
						'neat'                    => 'neat',
						'neo'                     => 'neo',
						'night'                   => 'night',
						'oceanic-next'            => 'oceanic-next',
						'panda-syntax'            => 'panda-syntax',
						'paraiso-dark'            => 'paraiso-dark',
						'paraiso-light'           => 'paraiso-light',
						'pastel-on-dark'          => 'pastel-on-dark',
						'railscasts'              => 'railscasts',
						'rubyblue'                => 'rubyblue',
						'seti'                    => 'seti',
						'shadowfox'               => 'shadowfox',
						'solarized'               => 'solarized',
						'ssms'                    => 'ssms',
						'the-matrix'              => 'the-matrix',
						'tomorrow-night-bright'   => 'tomorrow-night-bright',
						'tomorrow-night-eighties' => 'tomorrow-night-eighties',
						'ttcn'                    => 'ttcn',
						'twilight'                => 'twilight',
						'vibrant-ink'             => 'vibrant-ink',
						'xq-dark'                 => 'xq-dark',
						'xq-light'                => 'xq-light',
						'yeti'                    => 'yeti',
						'zenburn'                 => 'zenburn'
					),
				),

				array(
					'name'  => '',
					'label' => __( 'Markdown Extra', $this->text_domain ),
				),

				array(
					'name'    => 'support_toc',
					'label'   => __( 'Table of Content', $this->text_domain ),
					'desc'    => __( 'Display a TOC in the every first section.', $this->text_domain ),
					'type'    => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => __( 'Yes', $this->text_domain ),
						'no'  => __( 'No', $this->text_domain )
					)
				),

				array(
					'name'    => 'support_emoji',
					'label'   => __( 'Emoji', $this->text_domain ),
					'desc'    => __( 'Support Emoji in posts.', $this->text_domain ),
					'type'    => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => __( 'Yes', $this->text_domain ),
						'no'  => __( 'No', $this->text_domain )
					)
				),

				array(
					'name'    => 'support_katex',
					'label'   => __( 'KaTeX', $this->text_domain ),
					'desc'    => __( 'Support <a href="https://katex.org/" target="_blank">KaTeX</a> math typesetting.', $this->text_domain ),
					'type'    => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => __( 'Yes', $this->text_domain ),
						'no'  => __( 'No', $this->text_domain )
					)
				),

				array(
					'name'    => 'support_flow_chart',
					'label'   => __( 'Flow Chart', $this->text_domain ),
					'desc'    => __( 'Support <a href="http://flowchart.js.org/" target="_blank">flowchart.js</a> to draws simple SVG flow chart diagrams.', $this->text_domain ),
					'type'    => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => __( 'Yes', $this->text_domain ),
						'no'  => __( 'No', $this->text_domain )
					)
				),

				array(
					'name'    => 'support_sequence_diagram',
					'label'   => __( 'Sequence Diagrams', $this->text_domain ),
					'desc'    => __( 'Support <a href="https://bramp.github.io/js-sequence-diagrams/" target="_blank">js-sequence-diagrams</a> to turn text into vector UML sequence diagrams.', $this->text_domain ),
					'type'    => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => __( 'Yes', $this->text_domain ),
						'no'  => __( 'No', $this->text_domain )
					)
				),

				array(
					'name'    => 'support_task_list',
					'label'   => __( 'Task List', $this->text_domain ),
					'desc'    => __( 'Support Github Flavored Markdown task lists.', $this->text_domain ),
					'type'    => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => __( 'Yes', $this->text_domain ),
						'no'  => __( 'No', $this->text_domain )
					)
				),

			),
			/*
			'githuber_advanced' => array(
				array(
					'name' => 'text',
					'label' => __('Text Input', $this->text_domain),
					'desc' => __('Text input description', $this->text_domain),
					'type' => 'text',
					'default' => 'Title'
				),
				array(
					'name' => 'textarea',
					'label' => __('Textarea Input', $this->text_domain),
					'desc' => __('Textarea description', $this->text_domain),
					'type' => 'textarea'
				),
				array(
					'name' => 'checkbox',
					'label' => __('Checkbox', $this->text_domain),
					'desc' => __('Checkbox Label', $this->text_domain),
					'type' => 'checkbox'
				),
				array(
					'name' => 'radio',
					'label' => __('Radio Button', $this->text_domain),
					'desc' => __('A radio button', $this->text_domain),
					'type' => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No'
					)
				),
				array(
					'name' => 'multicheck',
					'label' => __('Multile checkbox', $this->text_domain),
					'desc' => __('Multi checkbox description', $this->text_domain),
					'type' => 'multicheck',
					'default' => array('one' => 'one', 'four' => 'four'),
					'options' => array(
						'one' => 'One',
						'two' => 'Two',
						'three' => 'Three',
						'four' => 'Four'
					)
				),
				array(
					'name' => 'selectbox',
					'label' => __('A Dropdown', $this->text_domain),
					'desc' => __('Dropdown description', $this->text_domain),
					'type' => 'select',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No'
					)
				)
			),
			'githuber_others' => array(
				array(
					'name' => 'text',
					'label' => __('Text Input', $this->text_domain),
					'desc' => __('Text input description', $this->text_domain),
					'type' => 'text',
					'default' => 'Title'
				),
				array(
					'name' => 'textarea',
					'label' => __('Textarea Input', $this->text_domain),
					'desc' => __('Textarea description', $this->text_domain),
					'type' => 'textarea'
				),
				array(
					'name' => 'checkbox',
					'label' => __('Checkbox', $this->text_domain),
					'desc' => __('Checkbox Label', $this->text_domain),
					'type' => 'checkbox'
				),
				array(
					'name' => 'radio',
					'label' => __('Radio Button', $this->text_domain),
					'desc' => __('A radio button', $this->text_domain),
					'type' => 'radio',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No'
					)
				),
				array(
					'name' => 'multicheck',
					'label' => __('Multile checkbox', $this->text_domain),
					'desc' => __('Multi checkbox description', $this->text_domain),
					'type' => 'multicheck',
					'options' => array(
						'one' => 'One',
						'two' => 'Two',
						'three' => 'Three',
						'four' => 'Four'
					)
				),
				array(
					'name' => 'selectbox',
					'label' => __('A Dropdown', $this->text_domain),
					'desc' => __('Dropdown description', $this->text_domain),
					'type' => 'select',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No'
					)
				)
				
			)*/
		);

		// set sections and fields.
		self::$setting_api->set_sections( $sections );
		self::$setting_api->set_fields( $fields );
	 
		// initialize them.
		self::$setting_api->admin_init();
		
		self::$settings = $fields;
	}

	/**
	 * Register the plugin page.
	 */
	public function setting_admin_menu() {

		add_options_page(
			__( 'Githuber ', $this->text_domain ),
			__( 'Githuber', $this->text_domain ),
			'manage_options',
			'githuber-plugin',
			array( $this, 'setting_plugin_page' )
		);
	}

	/**
	* Display the plugin settings options page.
	*/
	public function setting_plugin_page() {

		echo '<div class="wrap">';
		settings_errors();
	
		self::$setting_api->show_navigation();
		self::$setting_api->show_forms();
	
		echo '</div>';
	}

	/**
	* Get the value of a settings field.
	*
	* @param string $option  settings field name.
	* @param string $section the section name this field belongs to.
	* @param string $default default text if it's not found.
	* @return mixed
	*/
	public static function get_option( $option, $section, $default = '' ) {

		$options = get_option( $section );

		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return $default;
	}
}