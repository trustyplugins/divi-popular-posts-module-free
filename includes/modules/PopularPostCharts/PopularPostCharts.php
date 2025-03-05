<?php

class TP_Popular_Post_Charts extends ET_Builder_Module
{

	public $slug       = 'tp_popular_post_charts';
	public $vb_support = 'on';
	public $popular_div_order = 0;
	protected $module_credits = array(
		'module_uri' => 'https://trustyplugins.com',
		'author'     => 'Trusty Plugins',
		'author_uri' => 'https://trustyplugins.com',
	);

	public function init()
	{
		$this->name = esc_html__('Popular posts - Charts', 'tp-divi-popular-posts');
		$this->main_css_element = '%%order_class%%';
		$this->settings_modal_toggles = array(
			'general' => array(
				'toggles' => array(
					'post_setting' => array(
						'title'          => esc_html__('Settings', 'tp-divi-popular-posts'),
						'default_active' => true, // Open by default
						'priority'       => 1,    // Highest priority (appears first)
					),
					'logic_setting' => array(
						'title'          => esc_html__('Filter', 'tp-divi-popular-posts'),
						'priority'       => 2,    // Highest priority (appears first)
					),
					'charts_setting' => array(
						'title'          => esc_html__('Charts', 'tp-divi-popular-posts'),
						'priority'       => 3,    // Highest priority (appears first)
					),
				),
			),
			'advanced' => array(  // Correct key is 'advanced' not 'general' again
				'toggles' => array(
					'post_container' => array(
						'title'          => esc_html__('Post Container', 'tp-divi-popular-posts'),
					),
					'post_title' => array(
						'title'          => esc_html__('Post Title', 'tp-divi-popular-posts'),
						'tabbed_subtoggles' => true,
						'sub_toggles' => array(
							'a'     => array(
								'name' => 'A',
								'icon' => 'text-link',
							),
						),
					),
					'post_image' => array(
						'title'          => esc_html__('Post Image', 'tp-divi-popular-posts'),
					),
					'post_body'   => array(
						'title' => esc_html__('Post Body', 'tp-divi-popular-posts'),
						'tabbed_subtoggles' => true,
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
						'title' => esc_html__('Post Meta', 'tp-divi-popular-posts'),
						'tabbed_subtoggles' => true,
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
						'title'          => esc_html__('Post Button', 'tp-divi-popular-posts'),
						'show_if'         => array(
							'show_more' => 'on',
						),
					),
				),
			),
		);
	}

	public function get_fields()
	{
		$saved_post_types = get_option('tp_divi_post_types', []);
		$saved_post_types_with_keys = array_combine($saved_post_types, array_map('ucfirst', $saved_post_types));
		$all_types_tab_slug    = 'demo';
		return array(
			'filter'                  => array(
				'label'            => esc_html__('Filter', 'tp-divi-popular-posts'),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'today' => esc_html__("Today", 'tp-divi-popular-posts'),
					'weekly'  => esc_html__('Weekly', 'tp-divi-popular-posts'),
					'monthly'  => esc_html__('Monthly', 'tp-divi-popular-posts'),
					'yearly'  => esc_html__('Yearly', 'tp-divi-popular-posts'),
				),

				'description'      => esc_html__('Showing the full content will not truncate your posts on the index page. Showing the excerpt will only display your excerpt text.', 'tp-divi-popular-posts'),
				'toggle_slug'      => 'logic_setting',
				'default' => 'yearly',
			),
			'charts_type'                  => array(
				'label'            => esc_html__('Charts', 'tp-divi-popular-posts'),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'layout1' => esc_html__("Layout 1", 'tp-divi-popular-posts'),
					'layout2'  => esc_html__('Layout 2', 'tp-divi-popular-posts'),
				),
				'description'      => esc_html__('Select the style of graphic charts.', 'tp-divi-popular-posts'),
				'toggle_slug'      => 'charts_setting',
				'default' => 'layout1',
				'computed_affects' => array(
					'__posts',
				),
			),
			'charts_column'                  => array(
				'label'            => esc_html__('Charts', 'tp-divi-popular-posts'),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'column1' => esc_html__("Column 1", 'tp-divi-popular-posts'),
					'column2'  => esc_html__('Column 2', 'tp-divi-popular-posts'),
				),
				'description'      => esc_html__('Select the style of graphic charts.', 'tp-divi-popular-posts'),
				'toggle_slug'      => 'charts_setting',
				'default' => 'column1',
				'computed_affects' => array(
					'__posts',
				),
				'show_if'         => array(
					'charts_type' => 'layout1',
				),
			),
			'chart_width' => array(
				'label'           => esc_html__('Chart Width', 'tp-divi-popular-posts'),
				'type'            => 'range',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'charts_setting',
				'default' => '300px',
				'default_unit'   => 'px',
				'allowed_units'  => array('px', 'em', 'rem', '%'),
				'range_settings' => array(
					'min'  => '0',
					'max'  => '2000',
					'step' => '50',
				),
				'mobile_options'   => true,
				'computed_affects' => array(
					'__posts',
				),
			),

			'type_settings' => array(
				'label'           => esc_html__('Post Types', 'tp-divi-popular-posts'),
				'type'            => 'dbc_multiple_checkboxes_with_ids_tp',
				'option_category' => 'configuration',
				'options'         => $saved_post_types_with_keys,
				'description'     => esc_html__('Select the post types to include.', 'tp-divi-popular-posts'),
				'toggle_slug'     => 'post_setting',
				'tab_slug'        => 'general',
				'computed_affects' => array(
					'__posts',
				),
				//'default'         => array( 'post' ), // Default checked values
			),
			'chart_posts'                  => array(
				'label'            => esc_html__('Post Count for chart', 'tp-divi-popular-posts'),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__('Choose how much posts you would like to display per page.', 'tp-divi-popular-posts'),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'post_setting',
				'default'          => 10,
			),
			'show_posts'                => array(
				'label'            => esc_html__('Display Posts with Chart', 'tp-divi-popular-posts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'description'      => esc_html__('This will display posts with the chart too.', 'tp-divi-popular-posts'),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'on',
				// 'mobile_options'   => true,
				// 'hover'            => 'tabs',
			),
			'posts_number'                  => array(
				'label'            => esc_html__('Post Count', 'tp-divi-popular-posts'),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__('Choose how much posts you would like to display per page.', 'tp-divi-popular-posts'),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'post_setting',
				'default'          => 10,
				'show_if'         => array(
					'show_posts' => 'on',
				),
			),
			'show_thumbnail'                => array(
				'label'            => esc_html__('Show Featured Image', 'tp-divi-popular-posts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'description'      => esc_html__('This will turn thumbnails on and off.', 'tp-divi-popular-posts'),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'off',
				// 'mobile_options'   => true,
				// 'hover'            => 'tabs',
				'show_if'         => array(
					'show_posts' => 'on',
				),
			),
			'show_excerpt'                  => array(
				'label'            => esc_html__('Show Content', 'tp-divi-popular-posts'),
				'description'      => esc_html__('Turn post content on and off.', 'tp-divi-popular-posts'),
				'type'             => 'yes_no_button',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'post_setting',
				'option_category'  => 'configuration',
				'show_if'         => array(
					'show_posts' => 'on',
				),
			),
			'excerpt_length'                => array(
				'label'            => esc_html__('Excerpt Length', 'tp-divi-popular-posts'),
				'description'      => esc_html__('Define the length of automatically generated excerpts. Leave blank for default ( 50 ) words. ', 'tp-divi-popular-posts'),
				'type'             => 'text',
				'default'          => '50',
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'post_setting',
				'option_category'  => 'configuration',
				'show_if'         => array(
					'show_excerpt' => 'on',
					'show_posts' => 'on',
				),
			),
			'show_more'                     => array(
				'label'            => esc_html__('Show Read More Button', 'tp-divi-popular-posts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => et_builder_i18n('No'),
					'on'  => et_builder_i18n('Yes'),
				),
				'depends_show_if'  => 'off',
				'description'      => esc_html__('Here you can define whether to show "read more" link after the excerpts or not.', 'tp-divi-popular-posts'),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'off',
				'show_if'         => array(
					'show_posts' => 'on',
				),
			),
			'show_author'                   => array(
				'label'            => esc_html__('Show Author', 'tp-divi-popular-posts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'description'      => esc_html__('Turn on or off the author link.', 'tp-divi-popular-posts'),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'on',
				'show_if'         => array(
					'show_posts' => 'on',
				),
			),
			'show_date'                     => array(
				'label'            => esc_html__('Show Date', 'tp-divi-popular-posts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'description'      => esc_html__('Turn the date on or off.', 'tp-divi-popular-posts'),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'on',
				'show_if'         => array(
					'show_posts' => 'on',
				),
			),
			'meta_date'                     => array(
				'label'            => esc_html__('Date Format', 'tp-divi-popular-posts'),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__('If you would like to adjust the date format, input the appropriate PHP date format here.', 'tp-divi-popular-posts'),
				'toggle_slug'      => 'post_setting',
				'default'          => 'M j, Y',
				'show_if'         => array(
					'show_date' => 'on',
					'show_posts' => 'on'
				),
			),
			'show_comments'                 => array(
				'label'            => esc_html__('Show Comment Count', 'tp-divi-popular-posts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'description'      => esc_html__('Turn comment count on and off.', 'tp-divi-popular-posts'),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'on',
				'show_if'         => array(
					'show_posts' => 'on',
				),
			),
			'show_views'                 => array(
				'label'            => esc_html__('Show Views Count', 'tp-divi-popular-posts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'description'      => esc_html__('Turn comment count on and off.', 'tp-divi-popular-posts'),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'on',
				'show_if'         => array(
					'show_posts' => 'on',
				),
			),
			'show_categories'               => array(
				'label'            => esc_html__('Show Categories', 'tp-divi-popular-posts'),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n('Yes'),
					'off' => et_builder_i18n('No'),
				),
				'description'      => esc_html__('Turn the category links on or off.', 'tp-divi-popular-posts'),
				'toggle_slug'      => 'post_setting',
				'default_on_front' => 'off',
				'show_if'         => array(
					'show_posts' => 'on',
				),
			),
			'post_padding' => array(
				'label' => esc_html__('Post Padding', 'tp-divi-popular-posts'),
				'type' => 'custom_padding',
				'description' => esc_html__('Post Padding.', 'tp-divi-popular-posts'),
				'toggle_slug' => 'post_container',
				'tab_slug' => 'advanced',
				'show_if'         => array(
					'show_posts' => 'on',
				),
			),
			'post_margin' => array(
				'label' => esc_html__('Post Margin', 'tp-divi-popular-posts'),
				'type' => 'custom_margin',
				'description' => esc_html__('Post Margin.', 'tp-divi-popular-posts'),
				'toggle_slug' => 'post_container',
				'tab_slug' => 'advanced',
				'show_if'         => array(
					'show_posts' => 'on',
				),
			),
			'post_inner_padding' => array(
				'label' => esc_html__('Post inner Padding', 'tp-divi-popular-posts'),
				'type' => 'custom_padding',
				'description' => esc_html__('Post inner Padding.', 'tp-divi-popular-posts'),
				'toggle_slug' => 'post_container',
				'tab_slug' => 'advanced',
				'show_if'         => array(
					'post_layout' => 'layout2',
					'show_posts' => 'on',
				),
				
			),
			'post_background_color' => array(
				'label'           => esc_html__('Background Color', 'tp-divi-popular-posts'),
				'type'            => 'color-alpha',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'post_container',
				'show_if'         => array(
					'show_posts' => 'on',
				),
			),
			'image_width' => array(
				'label'           => esc_html__('Width', 'tp-divi-popular-posts'),
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
					'show_posts' => 'on',
				),
				
			),
			'image_min_height' => array(
				'label'           => esc_html__('Min Height', 'tp-divi-popular-posts'),
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
					'show_posts' => 'on',
				),
				
			),
			'image_height' => array(
				'label'           => esc_html__('Height', 'tp-divi-popular-posts'),
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
					'show_posts' => 'on',
				),
			),
			'image_max_height' => array(
				'label'           => esc_html__('Max Height', 'tp-divi-popular-posts'),
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
				'show_if'         => array(
					'show_posts' => 'on',
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
					'filter',
					'charts_type',
					'chart_width',
					'chart_posts'
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
		$advanced_fields['transform'] = false;
		$advanced_fields['filters'] = false;
		$advanced_fields['box_shadow'] = false;
		$advanced_fields['border'] = false;
		$advanced_fields['borders']['post_container'] = array(
			'label_prefix' => esc_html__('Post Container', 'tp-divi-popular-posts'),
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
		$advanced_fields['fonts']['post_title_link'] = array(
			'label'    => esc_html__('Post Title Link', 'tp-divi-popular-posts'),
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
			'label'    => esc_html__('Post Meta', 'tp-divi-popular-posts'),
			'css'      => array(
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
			'label'    => esc_html__('Post Meta links', 'tp-divi-popular-posts'),
			'css'      => array(
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
			'label'    => esc_html__('Post Body', 'tp-divi-popular-posts'),
			'css'      => array(
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
			'label'    => esc_html__('Post Body links', 'tp-divi-popular-posts'),
			'css'      => array(
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
			'label'    => esc_html__('Post Body quote', 'tp-divi-popular-posts'),
			'css'      => array(
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
				'label' => esc_html__('Button', 'tp-divi-popular-posts'),
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
		global $wpdb;
		if (get_option('tp_divi_post_types', [])) {
			$selected_post_types = get_option('tp_divi_post_types', []);
		} else {
			echo "Please Select Post Types first from admin settings.";
		}
		$post_types = $this->props['type_settings'];
		if (isset($attrs['type_settings'])) {
			$post_types = $attrs['type_settings'];
		}
		if ($post_types) {
			$selected_post_types = explode(',', $post_types);
		}
		if (isset($selected_post_types)) {

			$limit1 = $this->props['posts_number'];
			if (isset($attrs['posts_number'])) {
				$limit1 = $attrs['posts_number'];
			}
			$chart_posts=$this->props['chart_posts'];
			$limit=max($limit1,$chart_posts);
			$thumbnail = $this->props['show_thumbnail'];
			if (isset($attrs['show_thumbnail'])) {
				$thumbnail = $attrs['show_thumbnail'];
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
			
			$chart_width = $this->props['chart_width'];
			$chart_width_tablet = $this->props['chart_width_tablet'];
			$chart_width_phone = $this->props['chart_width_phone'];
			
			if (isset($attrs['meta_date'])) {
				$meta_date = $attrs['meta_date'];
			}
			
			$placeholders = implode(',', array_fill(0, count($selected_post_types), '%s'));
			$selected_filter = $this->props['filter'];
			//phpcs:ignore
			$today = date('Y-m-d');
			//phpcs:ignore
			$week_start = date('Y-m-d', strtotime('-7 days'));
			//phpcs:ignore
			$month_start = date('Y-m-d', strtotime('-30 days'));
			//phpcs:ignore
			$year_start = date('Y-m-d', strtotime('-1 year'));
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
			$args = array_merge($selected_post_types, $date_args, [$limit]);
			// phpcs:ignore
			$popular_posts = $wpdb->get_results($wpdb->prepare("SELECT p.ID AS post_id,p.post_title,p.post_type,SUM(pv.view_count) AS total_views FROM {$wpdb->prefix}post_views_tp pv INNER JOIN {$wpdb->prefix}posts p ON pv.post_id = p.ID WHERE pv.post_type IN ($placeholders) AND p.post_status = 'publish' $date_filter GROUP BY p.ID ORDER BY total_views DESC LIMIT %d",...$args));
			// Display the results
			$total_views_sum = array_sum(array_column($popular_posts, 'total_views'));
			// echo "<pre>";
            // print_r($this->props);
			// echo "</pre>";
			if (!empty($popular_posts)) {
				$chart_data = [
					'data' => [
						'chart_width' => $chart_width,
						'chart_width_tablet' => $chart_width_tablet,
						'chart_width_phone' => $chart_width_phone,
						'chart_layout'=>$this->props['charts_type']
					],
					'loop' => [],
				];
				
				if (!empty($popular_posts) && $total_views_sum > 0) {
					foreach ($popular_posts as $key => $postloop) {
						if ($key < $chart_posts) {
							$perc = round(($postloop->total_views / $total_views_sum) * 100, 2);
							$chart_data['loop'][] = [
								'id'=>$postloop->post_id,
								'title' => esc_html($postloop->post_title),
								'percentage' => $perc,
								'meta_views'=>$postloop->total_views
							];
						}
					}
				}
				
				// Encode the data as JSON for safe output in HTML
				$chart_json = esc_attr(json_encode($chart_data));
				echo "<div class='tp-chart-container-front " . esc_attr($this->props['charts_type']) . " " . (esc_attr($this->props['charts_column'] ?? '')) . "' data-chart='" . esc_attr($chart_json) . "'>";

					echo "<div class='chart-container-layout1'></div>";
					echo "<div class='chart-container-layout2'></div>";
					echo "<div class='chart-container-posts'>";
					if($this->props['show_posts']==='on') {
				foreach ($popular_posts as $key=>$postloop) {
					if($key<=$limit1-1) {
						include POPULAR_DIVI_PATH . 'includes/tp-layouts/layout-chart.php';
					}
				}
			}
				echo "</div>";
				echo "</div>";
			} else {
				echo '<p>No view count for any post.</p>';
			}
		}
		$output = ob_get_contents();
		ob_end_clean();
		$b++;
		return $output;
	}






	public function apply_css_styles($attrs, $render_slug, $b, $unique_class)
	{

		$post_padding = $this->props['post_padding'];
		$post_margin = $this->props['post_margin'];
		$post_bg = $this->props['post_background_color'];
		$image_width = $this->props['image_width'];
		$image_min_height = $this->props['image_min_height'];
		$image_height = $this->props['image_height'];
		$image_max_height = $this->props['image_max_height'];
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
					'declaration' => sprintf(
						'padding:%1$s %2$s %3$s %4$s;',
						esc_attr($padding_top),
						esc_attr($padding_right),
						esc_attr($padding_bottom),
						esc_attr($padding_left)
					),
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
						'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post .tp-post-inner-container",
						'declaration' => sprintf(
							'padding:%1$s %2$s %3$s %4$s;',
							esc_attr($padding_top),
							esc_attr($padding_right),
							esc_attr($padding_bottom),
							esc_attr($padding_left)
						),
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
					'declaration' => sprintf(
						'margin:%1$s %2$s %3$s %4$s;',
						esc_attr($margin_top),
						esc_attr($margin_right),
						esc_attr($margin_bottom),
						esc_attr($margin_left)
					),
				]
			);
		}
		if ($post_bg != '') {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post",
					'declaration' => sprintf('background-color:%1$s;', esc_attr($post_bg)),
				]
			);
		}
		if ($image_width != '') {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post .tp-left-wrapper",
					'declaration' => sprintf('width:%1$s;', esc_attr($image_width)),
				]
			);
		}
		if ($image_min_height != '') {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post .tp-left-wrapper",
					'declaration' => sprintf('min-height:%1$s;', esc_attr($image_min_height)),
				]
			);
		}
		if ($image_height != '') {
			
				ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post .tp-left-wrapper",
						'declaration' => sprintf('height:%1$s;', esc_attr($image_height)),
					]
				);
			
			
		}
		if ($image_max_height != '') {
			
				ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector' =>  ".tp_popular_posts_$b article.tp-divi-popular-post .tp-left-wrapper",
						'declaration' => sprintf('max-height:%1$s;', esc_attr($image_max_height)),
					]
				);
		}
	}
}

new TP_Popular_Post_Charts;
