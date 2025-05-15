<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class TPDIVI_Popular_Posts extends ET_Builder_Module
{

	public $slug       = 'tp_popular_posts';
	public $vb_support = 'on';
	public $popular_div_order = 0;
	protected $module_credits = array(
		'module_uri' => 'https://trustyplugins.com',
		'author'     => 'Trusty Plugins',
		'author_uri' => 'https://trustyplugins.com',
	);

	public function init()
	{
		$this->name = esc_html__('Popular posts', 'popular-posts-for-divi-with-charts');
		$this->settings_modal_toggles = array(
			'general' => array(
				'toggles' => array(
					'post_setting' => array(
						'title'          => esc_html__('Settings', 'popular-posts-for-divi-with-charts'),
						'default_active' => true, // Open by default
						'priority'       => 1,    // Highest priority (appears first)
					),
					'logic_setting' => array(
						'title'          => esc_html__('Filter', 'popular-posts-for-divi-with-charts'),
						'priority'       => 2,    // Highest priority (appears first)
					),
				),
			),
			'advanced' => array(  // Correct key is 'advanced' not 'general' again
				'toggles' => array(
					'post_setting_layout' => array(
						'title'          => esc_html__('Layouts', 'popular-posts-for-divi-with-charts'),
						'default_active' => true, // Set to false if you want it closed by default
						'priority'       => 2,     // Lower priority than 'post_setting'
					),
					'post_container' => array(
						'title'          => esc_html__('Post Container', 'popular-posts-for-divi-with-charts'),
					),
					'post_title' => array(
						'title'          => esc_html__('Post Title', 'popular-posts-for-divi-with-charts'),
						'tabbed_subtoggles' => true,
						// Subtoggle tab configuration. Add `sub_toggle` attribute on field to put them here
						'sub_toggles' => array(
							// 'p'     => array(
							// 	'name' => 'P',
							// 	'icon' => 'text-left',
							// ),
							'a'     => array(
								'name' => 'A',
								'icon' => 'text-link',
							),
						),
					),
					'post_image' => array(
						'title'          => esc_html__('Post Image', 'popular-posts-for-divi-with-charts'),
					),
					'post_body'   => array(
						'title' => esc_html__('Post Body', 'popular-posts-for-divi-with-charts'),
						// Groups can be organized into tab
						'tabbed_subtoggles' => true,
						// Subtoggle tab configuration. Add `sub_toggle` attribute on field to put them here
						'sub_toggles' => array(
							'p'     => array(
								'name' => 'P',
								'icon' => 'text-left',
							),
							'a'     => array(
								'name' => 'A',
								'icon' => 'text-link',
							),
							'quote' => array(
								'name' => 'QUOTE',
								'icon' => 'text-quote',
							),
						),
					),
					'post_meta'   => array(
						'title' => esc_html__('Post Meta', 'popular-posts-for-divi-with-charts'),
						// Groups can be organized into tab
						'tabbed_subtoggles' => true,
						// Subtoggle tab configuration. Add `sub_toggle` attribute on field to put them here
						'sub_toggles' => array(
							'p'     => array(
								'name' => 'P',
								'icon' => 'text-left',
							),
							'a'     => array(
								'name' => 'A',
								'icon' => 'text-link',
							),
						),
					),
					'post_button' => array(
						'title'          => esc_html__('Post Button', 'popular-posts-for-divi-with-charts'),
						'show_if'         => array(
							'show_more' => 'on',
						),
					),
				),
			),
		);

		$this->main_css_element = '%%order_class%%';
	}

	public function get_fields()
	{
		$saved_post_types = get_option('tpdivi_post_types', []);
		$saved_post_types_with_keys = array_combine($saved_post_types, array_map('ucfirst', $saved_post_types));
		//var_dump($saved_post_types_with_keys);
		$saved_post_types_with_keys=array('post'=>'post');
		return array(

			'post_layout'                  => array(
				'label'            => esc_html__('Post Layout', 'popular-posts-for-divi-with-charts'),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'layout1' => esc_html__('Layout 1', 'popular-posts-for-divi-with-charts'),
					'layout2'  => esc_html__('Layout 2', 'popular-posts-for-divi-with-charts'),
				),
				'description'      => esc_html__('Showing the full content will not truncate your posts on the index page. Showing the excerpt will only display your excerpt text.', 'popular-posts-for-divi-with-charts'),
				'toggle_slug'      => 'post_setting_layout',
				'computed_affects' => array(
					'__posts',
				),
				'default'         => 'layout1',
				//'mobile_options'   => true,
				//'hover'            => 'tabs',
				'tab_slug' => 'advanced'
			),
			'column_layout'                  => array(
				'label'            => esc_html__('Column Layout', 'popular-posts-for-divi-with-charts'),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'column1' => esc_html__('Column 1', 'popular-posts-for-divi-with-charts'),
					'column2'  => esc_html__('Column 2', 'popular-posts-for-divi-with-charts'),
					'column3'  => esc_html__('Column 3', 'popular-posts-for-divi-with-charts'),
					'column4'  => esc_html__('Column 4', 'popular-posts-for-divi-with-charts'),
				),
				'description'      => esc_html__('Showing the full content will not truncate your posts on the index page. Showing the excerpt will only display your excerpt text.', 'popular-posts-for-divi-with-charts'),
				'toggle_slug'      => 'post_setting_layout',
				'default'         => 'column3',
				'mobile_options'   => true,
				'hover'            => 'tabs',
				'tab_slug' => 'advanced',
				'show_if'         => array(
					'post_layout' => 'layout2',
				),
			),
			'filter'                  => array(
				'label'            => esc_html__('Filter', 'popular-posts-for-divi-with-charts'),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'yearly'  => esc_html__('Yearly', 'popular-posts-for-divi-with-charts'),
				),

				'description'      => esc_html__('Showing the full content will not truncate your posts on the index page. Showing the excerpt will only display your excerpt text.', 'popular-posts-for-divi-with-charts'),
				'toggle_slug'      => 'logic_setting',
				'default' => 'yearly',
				//'default_on_front' => 'yearly',
			),
			'type_settings' => array(
				'label'           => esc_html__('Post Types', 'popular-posts-for-divi-with-charts'),
				'type'            => 'dbc_multiple_checkboxes_with_ids_tp',
				'option_category' => 'configuration',
				'options'         => $saved_post_types_with_keys,
				'description'     => esc_html__('Select the post types to include.', 'popular-posts-for-divi-with-charts'),
				'toggle_slug'     => 'post_setting',
				'tab_slug'        => 'general',
				'computed_affects' => array(
					'__posts',
				),
				//'default'         => array( 'post' ), // Default checked values
			),

			'posts_number'                  => array(
				'label'            => esc_html__('Post Count', 'popular-posts-for-divi-with-charts'),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__('Choose how much posts you would like to display per page.', 'popular-posts-for-divi-with-charts'),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'post_setting',
				'default'          => 10,
			),
			'show_thumbnail'                => array(
				'label'            => esc_html__('Show Featured Image', 'popular-posts-for-divi-with-charts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'description'      => esc_html__('This will turn thumbnails on and off.', 'popular-posts-for-divi-with-charts'),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'off',
				// 'mobile_options'   => true,
				// 'hover'            => 'tabs',
			),
			'show_excerpt'                  => array(
				'label'            => esc_html__('Show Content', 'popular-posts-for-divi-with-charts'),
				'description'      => esc_html__('Turn post content on and off.', 'popular-posts-for-divi-with-charts'),
				'type'             => 'yes_no_button',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'default_on_front' => 'on',
				// 'computed_affects' => array(
				// 	'__posts',
				// ),
				//'depends_show_if'  => 'off',
				'toggle_slug'      => 'post_setting',
				'option_category'  => 'configuration',
				// 'mobile_options'   => true,
				// 'hover'            => 'tabs',
			),

			'show_content'                  => array(
				'label'            => esc_html__('Content Length', 'popular-posts-for-divi-with-charts'),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__('Show Excerpt', 'popular-posts-for-divi-with-charts'),
					'on'  => esc_html__('Show Content', 'popular-posts-for-divi-with-charts'),
				),

				'description'      => esc_html__('Showing the full content will not truncate your posts on the index page. Showing the excerpt will only display your excerpt text.', 'popular-posts-for-divi-with-charts'),
				'toggle_slug'      => 'post_setting',
				// 'computed_affects' => array(
				// 	'__posts',
				// ),
				'default_on_front' => 'off',
				// 'mobile_options'   => true,
				// 'hover'            => 'tabs',
				'show_if'         => array(
					'show_excerpt' => 'on',
				),
			),
			'excerpt_length'                => array(
				'label'            => esc_html__('Excerpt Length', 'popular-posts-for-divi-with-charts'),
				'description'      => esc_html__('Define the length of automatically generated excerpts. Leave blank for default ( 50 ) words. ', 'popular-posts-for-divi-with-charts'),
				'type'             => 'text',
				'default'          => '50',
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'post_setting',
				'option_category'  => 'configuration',
				'show_if'         => array(
					'show_excerpt' => 'on',
					'show_content' => 'off'
				),
			),
			'show_more'                     => array(
				'label'            => esc_html__('Show Read More Button', 'popular-posts-for-divi-with-charts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => et_builder_i18n('No'),
					'on'  => et_builder_i18n('Yes'),
				),
				'depends_show_if'  => 'off',
				'description'      => esc_html__('Here you can define whether to show "read more" link after the excerpts or not.', 'popular-posts-for-divi-with-charts'),
				// 'computed_affects' => array(
				// 	'__posts',
				// ),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'off',
				// 'mobile_options'   => true,
				// 'hover'            => 'tabs',
			),
			'show_author'                   => array(
				'label'            => esc_html__('Show Author', 'popular-posts-for-divi-with-charts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'description'      => esc_html__('Turn on or off the author link.', 'popular-posts-for-divi-with-charts'),
				// 'computed_affects' => array(
				// 	'__posts',
				// ),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'on',
				// 'mobile_options'   => true,
				// 'hover'            => 'tabs',
			),
			'show_date'                     => array(
				'label'            => esc_html__('Show Date', 'popular-posts-for-divi-with-charts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'description'      => esc_html__('Turn the date on or off.', 'popular-posts-for-divi-with-charts'),
				// 'computed_affects' => array(
				// 	'__posts',
				// ),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'on',
				// 'mobile_options'   => true,
				// 'hover'            => 'tabs',
			),
			'meta_date'                     => array(
				'label'            => esc_html__('Date Format', 'popular-posts-for-divi-with-charts'),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__('If you would like to adjust the date format, input the appropriate PHP date format here.', 'popular-posts-for-divi-with-charts'),
				'toggle_slug'      => 'post_setting',
				// 'computed_affects' => array(
				// 	'__posts',
				// ),
				'default'          => 'M j, Y',
				'show_if'         => array(
					'show_date' => 'on',
				),
			),
			'show_comments'                 => array(
				'label'            => esc_html__('Show Comment Count', 'popular-posts-for-divi-with-charts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'description'      => esc_html__('Turn comment count on and off.', 'popular-posts-for-divi-with-charts'),
				// 'computed_affects' => array(
				// 	'__posts',
				// ),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'on',
				// 'mobile_options'   => true,
				// 'hover'            => 'tabs',
			),
			'show_views'                 => array(
				'label'            => esc_html__('Show Views Count', 'popular-posts-for-divi-with-charts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'description'      => esc_html__('Turn comment count on and off.', 'popular-posts-for-divi-with-charts'),
				// 'computed_affects' => array(
				// 	'__posts',
				// ),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'on',
				// 'mobile_options'   => true,
				// 'hover'            => 'tabs',
			),
			'show_categories'               => array(
				'label'            => esc_html__('Show Categories', 'popular-posts-for-divi-with-charts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'description'      => esc_html__('Turn the category links on or off.', 'popular-posts-for-divi-with-charts'),
				// 'computed_affects' => array(
				// 	'__posts',
				// ),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'off',
				// 'mobile_options'   => true,
				// 'hover'            => 'tabs',
			),
			'post_padding' => array(
				'label' => esc_html__('Post Padding', 'popular-posts-for-divi-with-charts'),
				'type' => 'custom_padding',
				'description' => esc_html__('Post Padding.', 'popular-posts-for-divi-with-charts'),
				'toggle_slug' => 'post_container',
				'tab_slug' => 'advanced',
			),
			'post_margin' => array(
				'label' => esc_html__('Post Margin', 'popular-posts-for-divi-with-charts'),
				'type' => 'custom_margin',
				'description' => esc_html__('Post Margin.', 'popular-posts-for-divi-with-charts'),
				'toggle_slug' => 'post_container',
				'tab_slug' => 'advanced',
			),
			'post_inner_padding' => array(
				'label' => esc_html__('Post inner Padding', 'popular-posts-for-divi-with-charts'),
				'type' => 'custom_padding',
				'description' => esc_html__('Post inner Padding.', 'popular-posts-for-divi-with-charts'),
				'toggle_slug' => 'post_container',
				'tab_slug' => 'advanced',
				'show_if'         => array(
					'post_layout' => 'layout2',
				),
			),
			'post_background_color' => array(
				'label'           => esc_html__('Background Color', 'popular-posts-for-divi-with-charts'),
				'type'            => 'color-alpha',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'post_container',
			),
			'image_width' => array(
				'label'           => esc_html__('Width', 'popular-posts-for-divi-with-charts'),
				'type'            => 'range',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'post_image',
				'default' => '100%',
				'default_unit'   => '%',
				'allowed_units'  => array('px', 'em', 'rem', '%'),
				'range_settings' => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'show_if'         => array(
					'post_layout' => 'layout1',
				),
			),
			'image_min_height' => array(
				'label'           => esc_html__('Min Height', 'popular-posts-for-divi-with-charts'),
				'type'            => 'range',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'post_image',
				'default' => '100%',
				'default_unit'   => '%',
				'allowed_units'  => array('px', 'em', 'rem', '%'),
				'range_settings' => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'show_if'         => array(
					'post_layout' => 'layout1',
				),

			),
			'image_height' => array(
				'label'           => esc_html__('Height', 'popular-posts-for-divi-with-charts'),
				'type'            => 'range',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'post_image',
				'default' => '100%',
				'default_unit'   => '%',
				'allowed_units'  => array('px', 'em', 'rem', '%'),
				'range_settings' => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
			),
			'image_max_height' => array(
				'label'           => esc_html__('Max Height', 'popular-posts-for-divi-with-charts'),
				'type'            => 'range',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'post_image',
				'default' => '300px',
				'default_unit'   => 'px',
				'allowed_units'  => array('px', 'em', 'rem', '%'),
				'range_settings' => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
			),
			'__posts'                       => array(
				'type'                => 'computed',
				'computed_callback'   => array('TP_Popular_Posts', 'render'),
				'computed_depends_on' => array(
					'posts_number',
					'excerpt_length',
					'meta_date',
					'type_settings',
					'filter'
				),
			),
		);
	}
	public function get_advanced_fields_config()
	{
		$advanced_fields = array();
		$advanced_fields['link'] = false;
		$advanced_fields['animation'] = false;
		$advanced_fields['text'] = false;
		//$advanced_fields['margin_padding'] = false;
		$advanced_fields['transform'] = false;
		$advanced_fields['filters'] = false;
		$advanced_fields['box_shadow'] = false;
		$advanced_fields['border'] = false;
		//$advanced_fields['fonts']['post_title']=false;
		$advanced_fields['borders']['post_container'] = array(
			'label_prefix' => esc_html__('Post Container', 'popular-posts-for-divi-with-charts'),
			'css'          => array(
				'main'      => array(
					'border_radii'  => "{$this->main_css_element} .tp-divi-popular-post",
					'border_styles' => "{$this->main_css_element} .tp-divi-popular-post",
				),
			),
			'defaults'     => array(
				'border_width' => '1px', // Default border width
				'border_color' => '#cccccc', // Optional: Default border color
				'border_style' => 'solid', // Optional: Default border style
			),
			'tab_slug'     => 'advanced',
			'toggle_slug' => 'post_container',
		);
		// $advanced_fields['fonts']['post_title'] = array(
		// 	'label'    => esc_html__('Post Title', 'popular-posts-for-divi-with-charts'),
		// 	'css'      => array(
		// 		// Ensure the styles apply to the specific element
		// 		'main' => "{$this->main_css_element} .tp-post-title a",
		// 	),
		// 	'line_height' => array(
		// 		'default' => '1em',
		// 	),
		// 	'font_size' => array(
		// 		'default' => '22px',
		// 	),
		// 	'toggle_slug' => 'post_title',
		// 	'sub_toggle'  => 'p',

		// );
		$advanced_fields['fonts']['post_title_link'] = array(
			'label'    => esc_html__('Post Title Link', 'popular-posts-for-divi-with-charts'),
			'css'      => array(
				// Ensure the styles apply to the specific element
				'main' => "{$this->main_css_element} .tp-post-title a",
			),
			'line_height' => array(
				'default' => '1em',
			),
			'font_size' => array(
				'default' => '22px',
			),
			'toggle_slug' => 'post_title',
			'sub_toggle'  => 'a',

		);
		$advanced_fields['fonts']['post_meta'] = array(
			'label'    => esc_html__('Post Meta', 'popular-posts-for-divi-with-charts'),
			'css'      => array(
				// Ensure the styles apply to the specific element
				'main' => "{$this->main_css_element} .tp-meta-data",
			),
			'line_height' => array(
				'default' => '1em',
			),
			'font_size' => array(
				'default' => '14px',
			),
			'toggle_slug' => 'post_meta',
			'sub_toggle'  => 'p',
		);
		$advanced_fields['fonts']['post_meta_link'] = array(
			'label'    => esc_html__('Post Meta links', 'popular-posts-for-divi-with-charts'),
			'css'      => array(
				// Ensure the styles apply to the specific element
				'main' => "{$this->main_css_element} .tp-meta-data a,{$this->main_css_element} .tp-post-cats a",
			),
			'line_height' => array(
				'default' => '1em',
			),
			'font_size' => array(
				'default' => '14px',
			),
			'toggle_slug' => 'post_meta',
			'sub_toggle'  => 'a',
		);

		$advanced_fields['fonts']['post_body'] = array(
			'label'    => esc_html__('Post Body', 'popular-posts-for-divi-with-charts'),
			'css'      => array(
				// Ensure the styles apply to the specific element
				'main' => "{$this->main_css_element} .tp-post-content",
			),
			'line_height' => array(
				'default' => '1em',
			),
			'font_size' => array(
				'default' => '14px',
			),
			'toggle_slug' => 'post_body',
			'sub_toggle'  => 'p',
		);
		$advanced_fields['fonts']['post_body_link'] = array(
			'label'    => esc_html__('Post Body links', 'popular-posts-for-divi-with-charts'),
			'css'      => array(
				// Ensure the styles apply to the specific element
				'main' => "{$this->main_css_element} .tp-post-content a",
			),
			'line_height' => array(
				'default' => '1em',
			),
			'font_size' => array(
				'default' => '14px',
			),
			'toggle_slug' => 'post_body',
			'sub_toggle'  => 'a',
		);
		$advanced_fields['fonts']['post_body_quote'] = array(
			'label'    => esc_html__('Post Body quote', 'popular-posts-for-divi-with-charts'),
			'css'      => array(
				// Ensure the styles apply to the specific element
				'main' => "{$this->main_css_element} .tp-post-content blockquote",
			),
			'line_height' => array(
				'default' => '1em',
			),
			'font_size' => array(
				'default' => '14px',
			),
			'toggle_slug' => 'post_body',
			'sub_toggle'  => 'quote',
		);
		$advanced_fields['button'] = array(
			'button' => array(
				'label' => esc_html__('Button', 'popular-posts-for-divi-with-charts'),
				'css'   => array(
					'alignment'   => "%%order_class%% .et_pb_button_wrapper",
					'main' => "{$this->main_css_element} .tp-read-more a",
				),
				'toggle_slug' => 'post_button',
			),

		);
		return $advanced_fields;
	}
	public function render($attrs, $render_slug, $content = null)
	{
		ob_start();
		// echo "<pre>";
		// print_r($this->props);
		// echo "</pre>";
		$unique_class = '';
		if (isset($attrs['moduleInfo']['orderClassName'])) {
			$unique_class = $attrs['moduleInfo']['orderClassName'];
		}
		static $b = 0;
		$this->apply_css_styles($attrs, $render_slug, $b, $unique_class);
		//echo $b. $this->main_css_element;
		global $wpdb;
		// The array of selected post types
		if (get_option('tpdivi_post_types', [])) {
			$selected_post_types = get_option('tpdivi_post_types', []);
		} else {
			echo "Please Select Post Types first from admin settings.";
		}
		$selected_post_types=array('post');
		$post_types = $this->props['type_settings'];
		if (isset($attrs['type_settings'])) {
			$post_types = $attrs['type_settings'];
		}
		if ($post_types) {
			$selected_post_types = explode(',', $post_types);
		}
		if (isset($selected_post_types)) {

			$limit = $this->props['posts_number'];
			if (isset($attrs['posts_number'])) {
				$limit = $attrs['posts_number'];
			}
			$thumbnail = $this->props['show_thumbnail'];
			if (isset($attrs['show_thumbnail'])) {
				$thumbnail = $attrs['show_thumbnail'];
			}
			$show_content = $this->props['show_content'];
			if (isset($attrs['show_content'])) {
				$show_content = $attrs['show_content'];
			}
			$excerpt_length = $this->props['excerpt_length'];
			if (isset($attrs['excerpt_length'])) {
				$excerpt_length = $attrs['excerpt_length'];
			}
			$show_excerpt = $this->props['show_excerpt'];
			if (isset($attrs['show_excerpt'])) {
				$show_excerpt = $attrs['show_excerpt'];
			}
			$show_more = $this->props['show_more'];
			if (isset($attrs['show_more'])) {
				$show_more = $attrs['show_more'];
			}
			$show_author = $this->props['show_author'];
			if (isset($attrs['show_author'])) {
				$show_author = $attrs['show_author'];
			}
			$show_date = $this->props['show_date'];
			if (isset($attrs['show_date'])) {
				$show_date = $attrs['show_date'];
			}
			$show_categories = $this->props['show_categories'];
			if (isset($attrs['show_categories'])) {
				$show_categories = $attrs['show_categories'];
			}
			$show_comments = $this->props['show_comments'];
			if (isset($attrs['show_comments'])) {
				$show_comments = $attrs['show_comments'];
			}
			$show_views = $this->props['show_views'];
			if (isset($attrs['show_views'])) {
				$show_views = $attrs['show_views'];
			}
			$meta_date = $this->props['meta_date'];
			if (isset($attrs['meta_date'])) {
				$meta_date = $attrs['meta_date'];
			}
			$post_layout = $this->props['post_layout'];
			if (isset($attrs['post_layout'])) {
				$post_layout = $attrs['post_layout'];
			}
			$column_layout = 'column3';
			$column_layout = $this->props['column_layout'];
			$column_layout_tablet = 'column2';
			$column_layout_tablet = $this->props['column_layout_tablet'];
			$column_layout_phone = 'column1';
			$column_layout_phone = $this->props['column_layout_phone'];

			if (isset($attrs['column_layout'])) {
				$column_layout = $attrs['column_layout'];
			}
			// Prepare placeholders for the post types
			$placeholders = implode(',', array_fill(0, count($selected_post_types), '%s'));
			$selected_filter = $this->props['filter'];
			// SQL query to get post titles and view counts
			//phpcs:ignore
			$today = date('Y-m-d');
			//phpcs:ignore
			$week_start = date('Y-m-d', strtotime('-7 days'));
			//phpcs:ignore
			$month_start = date('Y-m-d', strtotime('-30 days'));
			//phpcs:ignore
			$year_start = date('Y-m-d', strtotime('-1 year'));

			// Build the WHERE clause dynamically based on the selected filter
			$date_filter = ''; // Default: no filter
			$date_args = []; // To store arguments for the date filter
			switch ($selected_filter) {
				case 'today':
					$date_filter = 'AND pv.view_date >= %s';
					$date_args[] = $today;
					break;
				case 'weekly':
					$date_filter = 'AND pv.view_date >= %s';
					$date_args[] = $week_start;
					break;
				case 'monthly':
					$date_filter = 'AND pv.view_date >= %s';
					$date_args[] = $month_start;
					break;
				case 'yearly':
					$date_filter = 'AND pv.view_date >= %s';
					$date_args[] = $year_start;
					break;
			}
			// Combine all arguments into a single array
			$args = array_merge($selected_post_types, $date_args, [$limit]);
			// Execute the query
			// phpcs:ignore
			$popular_posts = $wpdb->get_results($wpdb->prepare("SELECT p.ID AS post_id,p.post_title,p.post_type,SUM(pv.view_count) AS total_views FROM {$wpdb->prefix}post_views_tp pv INNER JOIN {$wpdb->prefix}posts p ON pv.post_id = p.ID WHERE p.post_type='post' AND p.post_status = 'publish' $date_filter GROUP BY p.ID ORDER BY total_views DESC LIMIT %d",...$args));
			//var_dump($args);
			// Display the results
			if (!empty($popular_posts)) {
				if ($post_layout == 'layout2') {
					echo "<div class='tp-layout-container " . esc_attr($column_layout) . " tablet-" . esc_attr($column_layout_tablet) . " phone-" . esc_attr($column_layout_phone) . "'>";
				}

				
				foreach ($popular_posts as $postloop) {
					if ($post_layout === 'layout1') {
						include POPULAR_DIVI_PATH . 'includes/tp-layouts/layout1.php';
					} else {
						include POPULAR_DIVI_PATH . 'includes/tp-layouts/layout2.php';
					}
				}
				if ($post_layout == 'layout2') {
					echo "</div>";
				}
			} else {
				echo '<p>No view count for any post.</p>';
			}
		}
		// echo "<pre>";
		// var_dump($this->props);
		// echo "</pre>";
		$output = ob_get_contents();
		ob_end_clean();
		$b++;
		return $output;
		//return sprintf('<h1>%1$s</h1>', $this->props['type_settings']);

	}
	public function apply_css_styles($attrs, $render_slug, $b, $unique_class)
	{

		$post_padding = $this->props['post_padding'];
		$post_margin = $this->props['post_margin'];
		$post_bg = $this->props['post_background_color'];
		$image_width = $this->props['image_width'];
		//$image_max_width = $this->props['image_max_width'];
		$image_min_height = $this->props['image_min_height'];
		$image_height = $this->props['image_height'];
		$image_max_height = $this->props['image_max_height'];
		//var_dump($post_bg);
		if ($post_padding != '') {
			$padding_part = explode('|', $post_padding);
			$padding_top = ($padding_part[0] !== "") ? $padding_part[0] : "0px";
			$padding_right = ($padding_part[1] !== "") ? $padding_part[1] : "0px";
			$padding_bottom = ($padding_part[2] !== "") ? $padding_part[2] : "0px";
			$padding_left = ($padding_part[3] !== "") ? $padding_part[3] : "0px";
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post",
					'declaration' => sprintf('padding:%1$s %2$s %3$s %4$s;', $padding_top, $padding_right, $padding_bottom, $padding_left),
				]
			);
		}
		if (isset($this->props['post_inner_padding'])) {
			$post_inner_padding = $this->props['post_inner_padding'];
			if ($post_inner_padding != '') {
				$padding_part = explode('|', $post_inner_padding);
				$padding_top = ($padding_part[0] !== "") ? $padding_part[0] : "0px";
				$padding_right = ($padding_part[1] !== "") ? $padding_part[1] : "0px";
				$padding_bottom = ($padding_part[2] !== "") ? $padding_part[2] : "0px";
				$padding_left = ($padding_part[3] !== "") ? $padding_part[3] : "0px";
				ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post.layout2 .tp-post-inner-container",
						'declaration' => sprintf('padding:%1$s %2$s %3$s %4$s;', $padding_top, $padding_right, $padding_bottom, $padding_left),
					]
				);
			}
		}


		if ($post_margin != '') {
			$margin_part = explode('|', $post_margin);
			$margin_top = ($margin_part[0] !== "") ? $margin_part[0] : "0px";
			$margin_right = ($margin_part[1] !== "") ? $margin_part[1] : "0px";
			$margin_bottom = ($margin_part[2] !== "") ? $margin_part[2] : "0px";
			$margin_left = ($margin_part[3] !== "") ? $margin_part[3] : "0px";
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post",
					'declaration' => sprintf('margin:%1$s %2$s %3$s %4$s;', $margin_top, $margin_right, $margin_bottom, $margin_left),
				]
			);
		}
		if ($post_bg != '') {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post",
					'declaration' => sprintf('background-color:%1$s;', $post_bg),
				]
			);
		}
		if ($image_width != '') {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post .tp-left-wrapper",
					'declaration' => sprintf('width:%1$s;', $image_width),
				]
			);
		}

		// if ($image_max_width != '') {
		// 	ET_Builder_Element::set_style(
		// 		$render_slug,
		// 		[
		// 			'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post .tp-left-wrapper",
		// 			'declaration' => sprintf('max-width:%1$s;', $image_max_width),
		// 		],
		// 	);
		// }
		if ($image_min_height != '') {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post .tp-left-wrapper",
					'declaration' => sprintf('min-height:%1$s;', $image_min_height),
				]
			);
		}
		if ($image_height != '') {
			if ($this->props['post_layout'] == 'layout1') {
				ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post .tp-left-wrapper",
						'declaration' => sprintf('height:%1$s;', $image_height),
					]
				);
			}
			if ($this->props['post_layout'] == 'layout2') {
				ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post.layout2 .tp-post-thumb img",
						'declaration' => sprintf('height:%1$s;', $image_height),
					]
				);
			}
		}
		if ($image_max_height != '') {
			if ($this->props['post_layout'] == 'layout1') {
				ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post .tp-left-wrapper",
						'declaration' => sprintf('max-height:%1$s;', $image_max_height),
					]
				);
			}
			if ($this->props['post_layout'] == 'layout2') {
				ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post.layout2 .tp-post-thumb img",
						'declaration' => sprintf('max-height:%1$s;', $image_max_height),
					]
				);
			}
		}
	}
}

new TPDIVI_Popular_Posts;
