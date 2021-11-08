<?php
class APPMAKER_WC_Category_Filter {

	public function __construct() {
		add_action( 'product_cat_add_form_fields', array( $this, 'category_filter_attributes_fields' ), 10, 1 );
		add_action( 'product_cat_edit_form_fields', array( $this, 'category_filter_attributes_fields' ), 10, 1 );

		add_action( 'edited_product_cat', array( $this, 'save_category_filter_attributes_fields' ), 10, 1 );
		add_action( 'create_product_cat', array( $this, 'save_category_filter_attributes_fields' ), 10, 1 );

		add_action( 'admin_enqueue_scripts', array( $this, 'load_custom_script' ) );
	}

	public function category_filter_attributes_fields( $term = false ) {
		if ( false !== $term && ! is_string( $term ) ) {
			$term_id           = $term->term_id;
			$filter_attributes = get_term_meta( $term_id, 'appmaker-wc-category-filter-attributes', true );
			if ( empty( $filter_attributes ) ) {
				$filter_attributes = array();
			}
		} else {
			$filter_attributes = array();
		}
		?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="wh_meta_title"><?php _e( 'Category filter attributes (App)', 'appmaker-woocommerce-mobile-app-manager' ); ?></label></th>
		<td>
		<select data-sorted-values name="appmaker-wc-category-filter-attributes[]" id="category-filter-attributes" multiple style="width:100%">
		<?php
		global $wc_product_attributes;
		// Array of defined attribute taxonomies.
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $tax ) {
				$attribute_taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );
				$label                   = $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name;
				$selected                = in_array( esc_attr( $attribute_taxonomy_name ), $filter_attributes ) ? 'selected' : '';
				echo '<option value="' . esc_attr( $attribute_taxonomy_name ) . '" ' . $selected . ' >' . esc_html( $label ) . '</option>';
			}
		}
		?>
		</select>
		<p class="description"><?php _e( 'Select filter attributes to display in this category on mobile app', 'appmaker-woocommerce-mobile-app-manager' ); ?></p>
		</td>
	</tr>
		<?php
	}

	// Save extra taxonomy fields callback function.
	public function save_category_filter_attributes_fields( $term_id ) {
		$filter_attributes = filter_input( INPUT_POST, 'appmaker-wc-category-filter-attributes', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		update_term_meta( $term_id, 'appmaker-wc-category-filter-attributes', $filter_attributes );
	}

	public function load_custom_script() {
		$screen = get_current_screen();

        if ( (strpos( $screen->base, 'edit-tags' ) !== false || strpos( $screen->base, 'term' ) !== false ) && !empty($_GET['taxonomy']) && 'product_cat' == $_GET['taxonomy']) {
		if ( ! wp_script_is( 'jquery', 'done' ) ) {
			wp_enqueue_script( 'jquery' );
		}
		wp_add_inline_script(
			'jquery-migrate',
			'jQuery(document).ready(function(){ 
                jQuery("#category-filter-attributes").select2() 
                jQuery("#category-filter-attributes").on("select2:select", function(e){
                var id = e.params.data.id;
                var option = $(e.target).children("[value="+id+"]");
                option.detach();
                $(e.target).append(option).change();
              });
            });'
		);
	}
}
}

new APPMAKER_WC_Category_Filter();
