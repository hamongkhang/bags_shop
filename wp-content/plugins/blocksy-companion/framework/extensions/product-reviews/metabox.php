<?php

$options = [

	blocksy_rand_md5() => [
		'title' => __( 'General', 'blc' ),
		'type' => 'tab',
		'options' => [
			'product_review_entity' => [
				'label' => __( 'Review Entity', 'blc' ),
				'type' => 'ct-select',
				'value' => 'Thing',
				'view' => 'text',
				'design' => 'inline',
				'choices' => blocksy_ordered_keys(
					[
						'Thing' => __( 'Default', 'blc' ),
						'Product' => __( 'Product', 'blc' ),
						'Book' => __( 'Book', 'blc' ),
						// 'Course' => __( 'Course', 'blc' ),
						'CreativeWorkSeason' => __( 'Creative Work Season', 'blc' ),
						'CreativeWorkSeries' => __( 'Creative Work Series', 'blc' ),
						'Episode' => __( 'Episode', 'blc' ),
						// 'Event' => __( 'Event', 'blc' ),
						'Game' => __( 'Game', 'blc' ),
						// 'HowTo' => __( 'How To', 'blc' ),
						'LocalBusiness' => __( 'Local Business', 'blc' ),
						'MediaObject' => __( 'Media Object', 'blc' ),
						'Movie' => __( 'Movie', 'blc' ),
						'MusicPlaylist' => __( 'Music Playlist', 'blc' ),
						'MusicRecording' => __( 'Music Recording', 'blc' ),
						'Organization' => __( 'Organization', 'blc' ),
						// 'Recipe' => __( 'Recipe', 'blc' ),
						// 'SoftwareApplication' => __( 'Software Application', 'blc' ),
					]
				),
				'desc' => sprintf(
					__(
						'More info about review entity and how to choose the right one can be found %shere%s.',
						'blc'
					),
					'<a href="https://developers.google.com/search/blog/2019/09/making-review-rich-results-more-helpful" target="_blank">',
					'</a>'
				),
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => ['product_review_entity' => 'Product'],
				'options' => [
					'product_entity_price' => [
						'type' => 'text',
						'label' => __('Product Price', 'blc'),
						'design' => 'inline',
						'value' => '',
					],

					'product_entity_sku' => [
						'type' => 'text',
						'label' => __('Product SKU', 'blc'),
						'design' => 'inline',
						'value' => ''
					],

					'product_entity_brand' => [
						'type' => 'text',
						'label' => __('Product Brand', 'blc'),
						'design' => 'inline',
						'value' => ''
					],
				]
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'gallery' => [
				'type' => 'ct-multi-image-uploader',
				'label' => __('Gallery', 'blc'),
				'design' => 'inline',
				'value' => []
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'product_button_label' => [
				'type' => 'text',
				'label' => __('Affiliate Button Label', 'blc'),
				'design' => 'inline',
				'value' => __('Buy Now', 'blc')
			],

			'product_link' => [
				'type' => 'text',
				'label' => __('Affiliate Link', 'blc'),
				'design' => 'inline',
				'value' => '#'
			],

			'product_link_target' => [
				'label' => __( 'Open Link In New Tab', 'blc' ),
				'type'  => 'ct-switch',
				'value' => 'no',
			],

			/*
			'product_button_icon' => [
				'type' => 'icon-picker',
				'label' => __('Button Icon', 'blc'),
				'design' => 'inline',
				'value' => [
					'icon' => 'fas fa-shopping-cart'
				]
			],
			 */

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'product_read_content_button_label' => [
				'type' => 'text',
				'label' => __('Read More Button Label', 'blc'),
				'design' => 'inline',
				'value' => __('Read More', 'blc')
			],

			/*
			'product_read_content_button_icon' => [
				'type' => 'icon-picker',
				'label' => __('Button Icon', 'blc'),
				'design' => 'inline',
				'value' => [
					'icon' => 'fas fa-arrow-down'
				]
			],
			 */

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'product_description' => [
				'type' => 'wp-editor',
				'label' => __('Short Description', 'blc'),
				'value' => '',
				'design' => 'inline',
			],

		],
	],

	blocksy_rand_md5() => [
		'title' => __( 'Rating', 'blc' ),
		'type' => 'tab',
		'options' => [

			'scores' => [
				'type' => 'ct-addable-box',
				'label' => __('Scores', 'blc'),
				'design' => 'inline',
				'preview-template' => '<%= label %> (<%= score === 1 ? "1 star" : score + " stars" %>)',

				'inner-options' => [
					'label' => [
						'type' => 'text',
						'value' => 'Default'
					],

					'score' => [
						'type' => 'ct-number',
						'value' => 5,
						'step' => 0.1,
						'min' => 1,
						'max' => 5
					]
				],

				'value' => [
					/*
					[
						'label' => 'Features',
						'score' => 5
					],

					[
						'label' => 'Quality',
						'score' => 5
					]
					 */
				]
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'product_specs' => [
				'type' => 'ct-addable-box',
				'label' => __('Product specs', 'blc'),
				'design' => 'inline',
				'preview-template' => '<%= label %>',

				'inner-options' => [
					'label' => [
						'type' => 'text',
						'value' => 'Default'
					],

					'value' => [
						'type' => 'text',
						'value' => ''
					]
				],

				'value' => []
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'product_pros' => [
				'type' => 'ct-addable-box',
				'label' => __('Pros', 'blc'),
				'design' => 'inline',
				'preview-template' => '<%= label %>',

				'inner-options' => [
					'label' => [
						'type' => 'text',
						'value' => 'Default'
					],
				],

				'value' => []
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'product_cons' => [
				'type' => 'ct-addable-box',
				'label' => __('Cons', 'blc'),
				'design' => 'inline',
				'preview-template' => '<%= label %>',

				'inner-options' => [
					'label' => [
						'type' => 'text',
						'value' => 'Default'
					],
				],

				'value' => []
			],

		],
	],

	// blocksy_rand_md5() => [
	// 	'title' => __( 'Design', 'blc' ),
	// 	'type' => 'tab',
	// 	'options' => [

	// 	],
	// ],
];

