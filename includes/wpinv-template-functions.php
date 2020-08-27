<?php
/**
 * Template functions.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Displays an invoice.
 * 
 * @param WPInv_Invoice $invoice.
 */
function getpaid_invoice( $invoice ) {
    if ( ! empty( $invoice ) ) {
        wpinv_get_template( 'invoice/invoice.php', compact( 'invoice' ) );
    }
}
add_action( 'getpaid_invoice', 'getpaid_invoice', 10 );

/**
 * Displays the invoice footer.
 */
function getpaid_invoice_footer( $invoice ) {
    if ( ! empty( $invoice ) ) {
        wpinv_get_template( 'invoice/footer.php', compact( 'invoice' ) );
    }
}
add_action( 'getpaid_invoice_footer', 'getpaid_invoice_footer', 10 );

/**
 * Displays the invoice top bar.
 */
function getpaid_invoice_header( $invoice ) {
    if ( ! empty( $invoice ) ) {
        wpinv_get_template( 'invoice/header.php', compact( 'invoice' ) );
    }
}
add_action( 'getpaid_invoice_header', 'getpaid_invoice_header', 10 );

/**
 * Displays actions on the left side of the header.
 */
function getpaid_invoice_header_left_actions( $invoice ) {
    if ( ! empty( $invoice ) ) {
        wpinv_get_template( 'invoice/header-left-actions.php', compact( 'invoice' ) );
    }
}
add_action( 'getpaid_invoice_header_left', 'getpaid_invoice_header_left_actions', 10 );

/**
 * Displays actions on the right side of the invoice top bar.
 */
function getpaid_invoice_header_right_actions( $invoice ) {
    if ( ! empty( $invoice ) ) {
        wpinv_get_template( 'invoice/header-right-actions.php', compact( 'invoice' ) );
    }
}
add_action( 'getpaid_invoice_header_right', 'getpaid_invoice_header_right_actions', 10 );

/**
 * Displays the invoice title, watermark, logo etc.
 */
function getpaid_invoice_details_top( $invoice ) {
    if ( ! empty( $invoice ) ) {
        wpinv_get_template( 'invoice/details-top.php', compact( 'invoice' ) );
    }
}
add_action( 'getpaid_invoice_details', 'getpaid_invoice_details_top', 10 );

/**
 * Displays the company logo.
 */
function getpaid_invoice_logo( $invoice ) {
    if ( ! empty( $invoice ) ) {
        wpinv_get_template( 'invoice/invoice-logo.php', compact( 'invoice' ) );
    }
}
add_action( 'getpaid_invoice_details_top_left', 'getpaid_invoice_logo' );

/**
 * Displays the type of invoice.
 */
function getpaid_invoice_type( $invoice ) {
    if ( ! empty( $invoice ) ) {
        wpinv_get_template( 'invoice/invoice-type.php', compact( 'invoice' ) );
    }
}
add_action( 'getpaid_invoice_details_top_right', 'getpaid_invoice_type' );

/**
 * Displays the invoice details.
 */
function getpaid_invoice_details_main( $invoice ) {
    if ( ! empty( $invoice ) ) {
        wpinv_get_template( 'invoice/details.php', compact( 'invoice' ) );
    }
}
add_action( 'getpaid_invoice_details', 'getpaid_invoice_details_main', 50 );

/**
 * Returns a path to the templates directory.
 * 
 * @return string
 */
function wpinv_get_templates_dir() {
    return WPINV_PLUGIN_DIR . 'templates';
}

/**
 * Returns a url to the templates directory.
 * 
 * @return string
 */
function wpinv_get_templates_url() {
    return WPINV_PLUGIN_URL . 'templates';
}

/**
 * Displays a template.
 * 
 * First checks if there is a template overide, if not it loads the default template.
 * 
 * @param string $template_name e.g payment-forms/cart.php The template to locate.
 * @param string $template_path The templates directory relative to the theme's root dir. Defaults to 'invoicing'.
 * @param string $default_path The root path to the default template. Defaults to invoicing/templates
 */
function wpinv_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

    // Make variables available to the template.
    if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

    // Locate the template.
	$located = wpinv_locate_template( $template_name, $template_path, $default_path );

    // Abort if the file does not exist.
	if ( ! file_exists( $located ) ) {
        _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
		return;
	}

    // Fires before loading a template.
	do_action( 'wpinv_before_template_part', $template_name, $template_path, $located, $args );

    // Load the template.
	include( $located );

    // Fires after loading a template.
	do_action( 'wpinv_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Retrieves a given template's html code.
 * 
 * First checks if there is a template overide, if not it loads the default template.
 * 
 * @param string $template_name e.g payment-forms/cart.php The template to locate.
 * @param string $template_path The templates directory relative to the theme's root dir. Defaults to 'invoicing'.
 * @param string $default_path The root path to the default template. Defaults to invoicing/templates
 */
function wpinv_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	wpinv_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Returns the default path from where to look for template overides.
 * 
 * @return string
 */
function wpinv_template_path() {
    return apply_filters( 'wpinv_template_path', wpinv_get_theme_template_dir_name() );
}

/**
 * Returns the directory containing the template overides.
 * 
 * @return string
 */
function wpinv_get_theme_template_dir_name() {
	return trailingslashit( apply_filters( 'wpinv_templates_dir', 'invoicing' ) );
}

/**
 * Locates a template path.
 * 
 * First checks if there is a template overide, if not it loads the default template.
 * 
 * @param string $template_name e.g payment-forms/cart.php The template to locate.
 * @param string $template_path The template path relative to the theme's root dir. Defaults to 'invoicing'.
 * @param string $default_path The root path to the default template. Defaults to invoicing/templates
 */
function wpinv_locate_template( $template_name, $template_path = '', $default_path = '' ) {

    // Load the defaults for the template path and default path.
    $template_path = empty( $template_path ) ? wpinv_template_path() : $template_path;
    $default_path  = empty( $default_path ) ? WPINV_PLUGIN_DIR . 'templates/' : $default_path;

    // Check if the template was overidden.
    $template = locate_template(
        array( trailingslashit( $template_path ) . $template_name )
    );

    // Maybe replace it with a default path.
    if ( empty( $template ) && ! empty( $default_path ) ) {
        $template = trailingslashit( $default_path ) . $template_name;
    }

    // Return what we found.
    return apply_filters( 'wpinv_locate_template', $template, $template_name, $template_path, $default_path );
}

function wpinv_get_template_part( $slug, $name = null, $load = true ) {
	do_action( 'get_template_part_' . $slug, $slug, $name );

	// Setup possible parts
	$templates = array();
	if ( isset( $name ) )
		$templates[] = $slug . '-' . $name . '.php';
	$templates[] = $slug . '.php';

	// Allow template parts to be filtered
	$templates = apply_filters( 'wpinv_get_template_part', $templates, $slug, $name );

	// Return the part that is found
	return wpinv_locate_tmpl( $templates, $load, false );
}

function wpinv_locate_tmpl( $template_names, $load = false, $require_once = true ) {
	// No file found yet
	$located = false;

	// Try to find a template file
	foreach ( (array)$template_names as $template_name ) {

		// Continue if template is empty
		if ( empty( $template_name ) )
			continue;

		// Trim off any slashes from the template name
		$template_name = ltrim( $template_name, '/' );

		// try locating this template file by looping through the template paths
		foreach( wpinv_get_theme_template_paths() as $template_path ) {

			if( file_exists( $template_path . $template_name ) ) {
				$located = $template_path . $template_name;
				break;
			}
		}

		if( !empty( $located ) ) {
			break;
		}
	}

	if ( ( true == $load ) && ! empty( $located ) )
		load_template( $located, $require_once );

	return $located;
}

function wpinv_get_theme_template_paths() {
	$template_dir = wpinv_get_theme_template_dir_name();

	$file_paths = array(
		1 => trailingslashit( get_stylesheet_directory() ) . $template_dir,
		10 => trailingslashit( get_template_directory() ) . $template_dir,
		100 => wpinv_get_templates_dir()
	);

	$file_paths = apply_filters( 'wpinv_template_paths', $file_paths );

	// sort the file paths based on priority
	ksort( $file_paths, SORT_NUMERIC );

	return array_map( 'trailingslashit', $file_paths );
}

function wpinv_checkout_meta_tags() {

	$pages   = array();
	$pages[] = wpinv_get_option( 'success_page' );
	$pages[] = wpinv_get_option( 'failure_page' );
	$pages[] = wpinv_get_option( 'invoice_history_page' );
	$pages[] = wpinv_get_option( 'invoice_subscription_page' );

	if( !wpinv_is_checkout() && !is_page( $pages ) ) {
		return;
	}

	echo '<meta name="robots" content="noindex,nofollow" />' . "\n";
}
add_action( 'wp_head', 'wpinv_checkout_meta_tags' );

function wpinv_add_body_classes( $class ) {
	$classes = (array)$class;

	if( wpinv_is_checkout() ) {
		$classes[] = 'wpinv-checkout';
		$classes[] = 'wpinv-page';
	}

	if( wpinv_is_success_page() ) {
		$classes[] = 'wpinv-success';
		$classes[] = 'wpinv-page';
	}

	if( wpinv_is_failed_transaction_page() ) {
		$classes[] = 'wpinv-failed-transaction';
		$classes[] = 'wpinv-page';
	}

	if( wpinv_is_invoice_history_page() ) {
		$classes[] = 'wpinv-history';
		$classes[] = 'wpinv-page';
	}

	if( wpinv_is_subscriptions_history_page() ) {
		$classes[] = 'wpinv-subscription';
		$classes[] = 'wpinv-page';
	}

	if( wpinv_is_test_mode() ) {
		$classes[] = 'wpinv-test-mode';
		$classes[] = 'wpinv-page';
	}

	return array_unique( $classes );
}
add_filter( 'body_class', 'wpinv_add_body_classes' );

function wpinv_html_dropdown( $name = 'wpinv_discounts', $selected = 0, $status = '' ) {
    $args = array( 'nopaging' => true );

    if ( ! empty( $status ) )
        $args['post_status'] = $status;

    $discounts = wpinv_get_discounts( $args );
    $options   = array();

    if ( $discounts ) {
        foreach ( $discounts as $discount ) {
            $options[ absint( $discount->ID ) ] = esc_html( get_the_title( $discount->ID ) );
        }
    } else {
        $options[0] = __( 'No discounts found', 'invoicing' );
    }

    $output = wpinv_html_select( array(
        'name'             => $name,
        'selected'         => $selected,
        'options'          => $options,
        'show_option_all'  => false,
        'show_option_none' => false,
    ) );

    return $output;
}

function wpinv_html_year_dropdown( $name = 'year', $selected = 0, $years_before = 5, $years_after = 0 ) {
    $current     = date( 'Y' );
    $start_year  = $current - absint( $years_before );
    $end_year    = $current + absint( $years_after );
    $selected    = empty( $selected ) ? date( 'Y' ) : $selected;
    $options     = array();

    while ( $start_year <= $end_year ) {
        $options[ absint( $start_year ) ] = $start_year;
        $start_year++;
    }

    $output = wpinv_html_select( array(
        'name'             => $name,
        'selected'         => $selected,
        'options'          => $options,
        'show_option_all'  => false,
        'show_option_none' => false
    ) );

    return $output;
}

function wpinv_html_month_dropdown( $name = 'month', $selected = 0 ) {

    $options = array(
        '1'  => __( 'January', 'invoicing' ),
        '2'  => __( 'February', 'invoicing' ),
        '3'  => __( 'March', 'invoicing' ),
        '4'  => __( 'April', 'invoicing' ),
        '5'  => __( 'May', 'invoicing' ),
        '6'  => __( 'June', 'invoicing' ),
        '7'  => __( 'July', 'invoicing' ),
        '8'  => __( 'August', 'invoicing' ),
        '9'  => __( 'September', 'invoicing' ),
        '10' => __( 'October', 'invoicing' ),
        '11' => __( 'November', 'invoicing' ),
        '12' => __( 'December', 'invoicing' ),
    );

    // If no month is selected, default to the current month
    $selected = empty( $selected ) ? date( 'n' ) : $selected;

    $output = wpinv_html_select( array(
        'name'             => $name,
        'selected'         => $selected,
        'options'          => $options,
        'show_option_all'  => false,
        'show_option_none' => false
    ) );

    return $output;
}

function wpinv_html_select( $args = array() ) {
    $defaults = array(
        'options'          => array(),
        'name'             => null,
        'class'            => '',
        'id'               => '',
        'selected'         => 0,
        'placeholder'      => null,
        'multiple'         => false,
        'show_option_all'  => _x( 'All', 'all dropdown items', 'invoicing' ),
        'show_option_none' => _x( 'None', 'no dropdown items', 'invoicing' ),
        'data'             => array(),
        'onchange'         => null,
        'required'         => false,
        'disabled'         => false,
        'readonly'         => false,
    );

    $args = wp_parse_args( $args, $defaults );

    $data_elements = '';
    foreach ( $args['data'] as $key => $value ) {
        $data_elements .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
    }

    if( $args['multiple'] ) {
        $multiple = ' MULTIPLE';
    } else {
        $multiple = '';
    }

    if( $args['placeholder'] ) {
        $placeholder = $args['placeholder'];
    } else {
        $placeholder = '';
    }
    
    $options = '';
    if( !empty( $args['onchange'] ) ) {
        $options .= ' onchange="' . esc_attr( $args['onchange'] ) . '"';
    }
    
    if( !empty( $args['required'] ) ) {
        $options .= ' required="required"';
    }
    
    if( !empty( $args['disabled'] ) ) {
        $options .= ' disabled';
    }
    
    if( !empty( $args['readonly'] ) ) {
        $options .= ' readonly';
    }

    $class  = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
    $output = '<select name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" class="wpinv-select ' . $class . '"' . $multiple . ' data-placeholder="' . $placeholder . '" ' . trim( $options ) . $data_elements . '>';

    if ( $args['show_option_all'] ) {
        if( $args['multiple'] ) {
            $selected = selected( true, in_array( 0, $args['selected'] ), false );
        } else {
            $selected = selected( $args['selected'], 0, false );
        }
        $output .= '<option value="all"' . $selected . '>' . esc_html( $args['show_option_all'] ) . '</option>';
    }

    if ( !empty( $args['options'] ) ) {

        if ( $args['show_option_none'] ) {
            if( $args['multiple'] ) {
                $selected = selected( true, in_array( "", $args['selected'] ), false );
            } else {
                $selected = selected( $args['selected'] === "", true, false );
            }
            $output .= '<option value=""' . $selected . '>' . esc_html( $args['show_option_none'] ) . '</option>';
        }

        foreach( $args['options'] as $key => $option ) {

            if( $args['multiple'] && is_array( $args['selected'] ) ) {
                $selected = selected( true, (bool)in_array( $key, $args['selected'] ), false );
            } else {
                $selected = selected( $args['selected'], $key, false );
            }

            $output .= '<option value="' . esc_attr( $key ) . '"' . $selected . '>' . esc_html( $option ) . '</option>';
        }
    }

    $output .= '</select>';

    return $output;
}

function wpinv_item_dropdown( $args = array() ) {
    $defaults = array(
        'name'              => 'wpi_item',
        'id'                => 'wpi_item',
        'class'             => '',
        'multiple'          => false,
        'selected'          => 0,
        'number'            => 100,
        'placeholder'       => __( 'Choose a item', 'invoicing' ),
        'data'              => array( 'search-type' => 'item' ),
        'show_option_all'   => false,
        'show_option_none'  => false,
        'show_recurring'    => false,
    );

    $args = wp_parse_args( $args, $defaults );

    $item_args = array(
        'post_type'      => 'wpi_item',
        'orderby'        => 'title',
        'order'          => 'ASC',
        'posts_per_page' => $args['number']
    );

    $item_args  = apply_filters( 'wpinv_item_dropdown_query_args', $item_args, $args, $defaults );

    $items      = get_posts( $item_args );
    $options    = array();
    if ( $items ) {
        foreach ( $items as $item ) {
            $title = esc_html( $item->post_title );
            
            if ( !empty( $args['show_recurring'] ) ) {
                $title .= wpinv_get_item_suffix( $item->ID, false );
            }
            
            $options[ absint( $item->ID ) ] = $title;
        }
    }

    // This ensures that any selected items are included in the drop down
    if( is_array( $args['selected'] ) ) {
        foreach( $args['selected'] as $item ) {
            if( ! in_array( $item, $options ) ) {
                $title = get_the_title( $item );
                if ( !empty( $args['show_recurring'] ) ) {
                    $title .= wpinv_get_item_suffix( $item, false );
                }
                $options[$item] = $title;
            }
        }
    } elseif ( is_numeric( $args['selected'] ) && $args['selected'] !== 0 ) {
        if ( ! in_array( $args['selected'], $options ) ) {
            $title = get_the_title( $args['selected'] );
            if ( !empty( $args['show_recurring'] ) ) {
                $title .= wpinv_get_item_suffix( $args['selected'], false );
            }
            $options[$args['selected']] = get_the_title( $args['selected'] );
        }
    }

    $output = wpinv_html_select( array(
        'name'             => $args['name'],
        'selected'         => $args['selected'],
        'id'               => $args['id'],
        'class'            => $args['class'],
        'options'          => $options,
        'multiple'         => $args['multiple'],
        'placeholder'      => $args['placeholder'],
        'show_option_all'  => $args['show_option_all'],
        'show_option_none' => $args['show_option_none'],
        'data'             => $args['data'],
    ) );

    return $output;
}

/**
 * Returns an array of published items.
 */
function wpinv_get_published_items_for_dropdown() {

    $items = get_posts(
        array(
            'post_type'      => 'wpi_item',
            'orderby'        => 'title',
            'order'          => 'ASC',
            'posts_per_page' => '-1'
        )
    );

    $options = array();
    if ( $items ) {
        foreach ( $items as $item ) {
            $options[ $item->ID ] = esc_html( $item->post_title ) . wpinv_get_item_suffix( $item->ID, false );
        }
    }

    return $options;
}

function wpinv_html_checkbox( $args = array() ) {
    $defaults = array(
        'name'     => null,
        'current'  => null,
        'class'    => 'wpinv-checkbox',
        'options'  => array(
            'disabled' => false,
            'readonly' => false
        )
    );

    $args = wp_parse_args( $args, $defaults );

    $class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
    $options = '';
    if ( ! empty( $args['options']['disabled'] ) ) {
        $options .= ' disabled="disabled"';
    } elseif ( ! empty( $args['options']['readonly'] ) ) {
        $options .= ' readonly';
    }

    $output = '<input type="checkbox"' . $options . ' name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['name'] ) . '" class="' . $class . ' ' . esc_attr( $args['name'] ) . '" ' . checked( 1, $args['current'], false ) . ' />';

    return $output;
}

/**
 * Displays a hidden field.
 */
function getpaid_hidden_field( $name, $value ) {
    $name  = sanitize_text_field( $name );
    $value = esc_attr( $value );

    echo "<input type='hidden' name='$name' value='$value' />";
}

function wpinv_html_text( $args = array() ) {
    // Backwards compatibility
    if ( func_num_args() > 1 ) {
        $args = func_get_args();

        $name  = $args[0];
        $value = isset( $args[1] ) ? $args[1] : '';
        $label = isset( $args[2] ) ? $args[2] : '';
        $desc  = isset( $args[3] ) ? $args[3] : '';
    }

    $defaults = array(
        'id'           => '',
        'name'         => isset( $name )  ? $name  : 'text',
        'value'        => isset( $value ) ? $value : null,
        'label'        => isset( $label ) ? $label : null,
        'desc'         => isset( $desc )  ? $desc  : null,
        'placeholder'  => '',
        'class'        => 'regular-text',
        'disabled'     => false,
        'readonly'     => false,
        'required'     => false,
        'autocomplete' => '',
        'data'         => false
    );

    $args = wp_parse_args( $args, $defaults );

    $class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
    $options = '';
    if( $args['required'] ) {
        $options .= ' required="required"';
    }
    if( $args['readonly'] ) {
        $options .= ' readonly';
    }
    if( $args['readonly'] ) {
        $options .= ' readonly';
    }

    $data = '';
    if ( !empty( $args['data'] ) ) {
        foreach ( $args['data'] as $key => $value ) {
            $data .= 'data-' . wpinv_sanitize_key( $key ) . '="' . esc_attr( $value ) . '" ';
        }
    }

    $output = '<span id="wpinv-' . wpinv_sanitize_key( $args['name'] ) . '-wrap">';
    $output .= '<label class="wpinv-label" for="' . wpinv_sanitize_key( $args['id'] ) . '">' . esc_html( $args['label'] ) . '</label>';
    if ( ! empty( $args['desc'] ) ) {
        $output .= '<span class="wpinv-description">' . esc_html( $args['desc'] ) . '</span>';
    }

    $output .= '<input type="text" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] )  . '" autocomplete="' . esc_attr( $args['autocomplete'] )  . '" value="' . esc_attr( $args['value'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" class="' . $class . '" ' . $data . ' ' . trim( $options ) . '/>';

    $output .= '</span>';

    return $output;
}

function wpinv_html_date_field( $args = array() ) {
    if( empty( $args['class'] ) ) {
        $args['class'] = 'wpiDatepicker';
    } elseif( ! strpos( $args['class'], 'wpiDatepicker' ) ) {
        $args['class'] .= ' wpiDatepicker';
    }

    return wpinv_html_text( $args );
}

function wpinv_html_textarea( $args = array() ) {
    $defaults = array(
        'name'        => 'textarea',
        'value'       => null,
        'label'       => null,
        'desc'        => null,
        'class'       => 'large-text',
        'disabled'    => false,
        'placeholder' => '',
    );

    $args = wp_parse_args( $args, $defaults );

    $class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
    $disabled = '';
    if( $args['disabled'] ) {
        $disabled = ' disabled="disabled"';
    }

    $output = '<span id="wpinv-' . wpinv_sanitize_key( $args['name'] ) . '-wrap">';
    $output .= '<label class="wpinv-label" for="' . wpinv_sanitize_key( $args['name'] ) . '">' . esc_html( $args['label'] ) . '</label>';
    $output .= '<textarea name="' . esc_attr( $args['name'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" id="' . wpinv_sanitize_key( $args['name'] ) . '" class="' . $class . '"' . $disabled . '>' . esc_attr( $args['value'] ) . '</textarea>';

    if ( ! empty( $args['desc'] ) ) {
        $output .= '<span class="wpinv-description">' . esc_html( $args['desc'] ) . '</span>';
    }
    $output .= '</span>';

    return $output;
}

function wpinv_html_ajax_user_search( $args = array() ) {
    $defaults = array(
        'name'        => 'user_id',
        'value'       => null,
        'placeholder' => __( 'Enter username', 'invoicing' ),
        'label'       => null,
        'desc'        => null,
        'class'       => '',
        'disabled'    => false,
        'autocomplete'=> 'off',
        'data'        => false
    );

    $args = wp_parse_args( $args, $defaults );

    $args['class'] = 'wpinv-ajax-user-search ' . $args['class'];

    $output  = '<span class="wpinv_user_search_wrap">';
        $output .= wpinv_html_text( $args );
        $output .= '<span class="wpinv_user_search_results hidden"><a class="wpinv-ajax-user-cancel" title="' . __( 'Cancel', 'invoicing' ) . '" aria-label="' . __( 'Cancel', 'invoicing' ) . '" href="#">x</a><span></span></span>';
    $output .= '</span>';

    return $output;
}

function wpinv_ip_geolocation() {
    global $wpinv_euvat;
    
    $ip         = !empty( $_GET['ip'] ) ? sanitize_text_field( $_GET['ip'] ) : '';    
    $content    = '';
    $iso        = '';
    $country    = '';
    $region     = '';
    $city       = '';
    $longitude  = '';
    $latitude   = '';
    $credit     = '';
    $address    = '';
    
    if ( wpinv_get_option( 'vat_ip_lookup' ) == 'geoip2' && $geoip2_city = $wpinv_euvat->geoip2_city_record( $ip ) ) {
        try {
            $iso        = $geoip2_city->country->isoCode;
            $country    = $geoip2_city->country->name;
            $region     = !empty( $geoip2_city->subdivisions ) && !empty( $geoip2_city->subdivisions[0]->name ) ? $geoip2_city->subdivisions[0]->name : '';
            $city       = $geoip2_city->city->name;
            $longitude  = $geoip2_city->location->longitude;
            $latitude   = $geoip2_city->location->latitude;
            $credit     = __( 'Geolocated using the information by MaxMind, available from <a href="http://www.maxmind.com" target="_blank">www.maxmind.com</a>', 'invoicing' );
        } catch( Exception $e ) { }
    }
    
    if ( !( $iso && $longitude && $latitude ) && function_exists( 'simplexml_load_file' ) ) {
        try {
            $load_xml = simplexml_load_file( 'http://www.geoplugin.net/xml.gp?ip=' . $ip );
            
            if ( !empty( $load_xml ) && isset( $load_xml->geoplugin_countryCode ) && !empty( $load_xml->geoplugin_latitude ) && !empty( $load_xml->geoplugin_longitude ) ) {
                $iso        = $load_xml->geoplugin_countryCode;
                $country    = $load_xml->geoplugin_countryName;
                $region     = !empty( $load_xml->geoplugin_regionName ) ? $load_xml->geoplugin_regionName : '';
                $city       = !empty( $load_xml->geoplugin_city ) ? $load_xml->geoplugin_city : '';
                $longitude  = $load_xml->geoplugin_longitude;
                $latitude   = $load_xml->geoplugin_latitude;
                $credit     = $load_xml->geoplugin_credit;
                $credit     = __( 'Geolocated using the information by geoPlugin, available from <a href="http://www.geoplugin.com" target="_blank">www.geoplugin.com</a>', 'invoicing' ) . '<br>' . $load_xml->geoplugin_credit;
            }
        } catch( Exception $e ) { }
    }
    
    if ( $iso && $longitude && $latitude ) {
        if ( $city ) {
            $address .= $city . ', ';
        }
        
        if ( $region ) {
            $address .= $region . ', ';
        }
        
        $address .= $country . ' (' . $iso . ')';
        $content = '<p>'. sprintf( __( '<b>Address:</b> %s', 'invoicing' ), $address ) . '</p>';
        $content .= '<p>'. $credit . '</p>';
    } else {
        $content = '<p>'. sprintf( __( 'Unable to find geolocation for the IP address: %s', 'invoicing' ), $ip ) . '</p>';
    }
    ?>
<!DOCTYPE html>
<html><head><title><?php echo sprintf( __( 'IP: %s', 'invoicing' ), $ip );?></title><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"><link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.0-rc.1/leaflet.css" /><style>html,body{height:100%;margin:0;padding:0;width:100%}body{text-align:center;background:#fff;color:#222;font-size:small;}body,p{font-family: arial,sans-serif}#map{margin:auto;width:100%;height:calc(100% - 120px);min-height:240px}</style></head>
<body>
    <?php if ( $latitude && $latitude ) { ?>
    <div id="map"></div>
        <script src="//cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.0-rc.1/leaflet.js"></script>
        <script type="text/javascript">
        var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            osm = L.tileLayer(osmUrl, {maxZoom: 18, attribution: osmAttrib}),
            latlng = new L.LatLng(<?php echo $latitude;?>, <?php echo $longitude;?>);

        var map = new L.Map('map', {center: latlng, zoom: 12, layers: [osm]});

        var marker = new L.Marker(latlng);
        map.addLayer(marker);

        marker.bindPopup("<p><?php esc_attr_e( $address );?></p>");
    </script>
    <?php } ?>
    <div style="height:100px"><?php echo $content; ?></div>
</body></html>
<?php
    exit;
}
add_action( 'wp_ajax_wpinv_ip_geolocation', 'wpinv_ip_geolocation' );
add_action( 'wp_ajax_nopriv_wpinv_ip_geolocation', 'wpinv_ip_geolocation' );

/**
 * Use our template to display invoices.
 * 
 * @param string $template the template that is currently being used.
 */
function wpinv_template( $template ) {
    global $post;

    if ( ! is_admin() && ( is_single() || is_404() ) && ! empty( $post->ID ) && getpaid_is_invoice_post_type( get_post_type( $post->ID ) ) ) {

        // If the user can view this invoice, display it.
        if ( wpinv_user_can_view_invoice( $post->ID ) ) {

            return wpinv_get_template_part( 'wpinv-invoice-print', false, false );

        // Else display an error message.
        } else {

            return wpinv_get_template_part( 'wpinv-invalid-access', false, false );

        }

    }

    return $template;
}
add_filter( 'template_include', 'wpinv_template', 10, 1 );

function wpinv_get_business_address() {
    $business_address   = wpinv_store_address();
    $business_address   = !empty( $business_address ) ? wpautop( wp_kses_post( $business_address ) ) : '';
    
    $business_address = $business_address ? '<div class="address">' . $business_address . '</div>' : '';
    
    return apply_filters( 'wpinv_get_business_address', $business_address );
}

/**
 * Displays the company address.
 */
function wpinv_display_from_address() {
    wpinv_get_template( 'invoice/company-address.php' );
}
add_action( 'getpaid_invoice_details_left', 'wpinv_display_from_address', 10 );

function wpinv_watermark( $id = 0 ) {
    $output = wpinv_get_watermark( $id );
    return apply_filters( 'wpinv_get_watermark', $output, $id );
}

function wpinv_get_watermark( $id ) {
    if ( !$id > 0 ) {
        return NULL;
    }

    $invoice = wpinv_get_invoice( $id );
    
    if ( !empty( $invoice ) && "wpi_invoice" === $invoice->post_type ) {
        if ( $invoice->is_paid() ) {
            return __( 'Paid', 'invoicing' );
        }
        if ( $invoice->is_refunded() ) {
            return __( 'Refunded', 'invoicing' );
        }
        if ( $invoice->has_status( array( 'wpi-cancelled' ) ) ) {
            return __( 'Cancelled', 'invoicing' );
        }
    }
    
    return NULL;
}

/**
 * @deprecated
 */
function wpinv_display_invoice_details( $invoice ) {
    return getpaid_invoice_meta( $invoice );
}

/**
 * Displays invoice meta.
 */
function getpaid_invoice_meta( $invoice ) {

    $invoice = new WPInv_Invoice( $invoice );

    // Ensure that we have an invoice.
    if ( 0 == $invoice->get_id() ) {
        return;
    }

    // Load the invoice meta.
    $meta    = array(

        'number' => array(
            'label' => sprintf(
                __( '%s Number', 'invoicing' ),
                ucfirst( $invoice->get_type() )
            ),
            'value' => sanitize_text_field( $invoice->get_number() ),
        ),

        'status' => array(
            'label' => sprintf(
                __( '%s Status', 'invoicing' ),
                ucfirst( $invoice->get_type() )
            ),
            'value' => sanitize_text_field( $invoice->get_status_nicename() ),
        ),

        'date' => array(
            'label' => sprintf(
                __( '%s Date', 'invoicing' ),
                ucfirst( $invoice->get_type() )
            ),
            'value' => getpaid_format_date( $invoice->get_created_date() ),
        ),

        'date_paid' => array(
            'label' => __( 'Paid On', 'invoicing' ),
            'value' => getpaid_format_date( $invoice->get_completed_date() ),
        ),

        'transaction_id' => array(
            'label' => __( 'Transaction ID', 'invoicing' ),
            'value' => sanitize_text_field( $invoice->get_transaction_id() ),
        ),

        'due_date'  => array(
            'label' => __( 'Due Date', 'invoicing' ),
            'value' => getpaid_format_date( $invoice->get_due_date() ),
        ),

        'vat_number' => array(
            'label' => sprintf(
                __( '%s Number', 'invoicing' ),
                $GLOBALS['wpinv_euvat']->get_vat_name()
            ),
            'value' => sanitize_text_field( $invoice->get_vat_number() ),
        ),

    );

    // If it is not paid, remove the date of payment.
    if ( ! $invoice->is_paid() ) {
        unset( $meta[ 'date_paid' ] );
        unset( $meta[ 'transaction_id' ] );
    }

    // Only display the due date if due dates are enabled.
    if ( ! $invoice->needs_payment() || ! wpinv_get_option( 'overdue_active' ) ) {
        unset( $meta[ 'due_date' ] );
    }

    // Only display the vat number if taxes are enabled.
    if ( ! wpinv_use_taxes() ) {
        unset( $meta[ 'vat_number' ] );
    }

    if ( $invoice->is_recurring() ) {

        // Link to the parent invoice.
        if ( $invoice->is_renewal() ) {

            $meta[ 'parent' ] = array(

                'label' => sprintf(
                    __( 'Parent %s', 'invoicing' ),
                    ucfirst( $invoice->get_type() )
                ),

                'value' => wpinv_invoice_link( $invoice->get_parent_id() ),

            );

        }

        $subscription = wpinv_get_subscription( $invoice );

        if ( ! empty ( $subscription ) ) {

            // Display the renewal date.
            if ( $subscription->is_active() && 'cancelled' != $subscription->status ) {

                $meta[ 'renewal_date' ] = array(

                    'label' => __( 'Renews On', 'invoicing' ),
                    'value' => getpaid_format_date( $subscription->expiration ),
        
                );

            }

            if ( $invoice->is_parent() ) {

                // Display the recurring amount.
                $meta[ 'recurring_total' ] = array(

                    'label' => __( 'Recurring Amount', 'invoicing' ),
                    'value' => wpinv_price( wpinv_format_amount( $subscription->recurring_amount ), $invoice->get_currency() ),
        
                );

            }
            
        }
    }

    // Add the invoice total to the meta.
    $meta[ 'invoice_total' ] = array(

        'label' => __( 'Total Amount', 'invoicing' ),
        'value' => wpinv_price( wpinv_format_amount( $invoice->get_total() ), $invoice->get_currency() ),

    );

    // Provide a way for third party plugins to filter the meta.
    $meta = apply_filters( 'getpaid_invoice_meta_data', $meta, $invoice );

    wpinv_get_template( 'invoice/invoice-meta.php', compact( 'invoice', 'meta' ) );

}
add_action( 'getpaid_invoice_details_right', 'getpaid_invoice_meta', 10 );

/**
 * Retrieves the address markup to use on Invoices.
 * 
 * @since 1.0.13
 * @see `wpinv_get_full_address_format`
 * @see `wpinv_get_invoice_address_replacements`
 * @param array $billing_details customer's billing details
 * @param  string $separator How to separate address lines.
 * @return string
 */
function wpinv_get_invoice_address_markup( $billing_details, $separator = '<br/>' ) {

    // Retrieve the address markup...
    $country= empty( $billing_details['country'] ) ? '' : $billing_details['country'];
    $format = wpinv_get_full_address_format( $country );

    // ... and the replacements.
    $replacements = wpinv_get_invoice_address_replacements( $billing_details );

    $formatted_address = str_ireplace( array_keys( $replacements ), $replacements, $format );
    
	// Remove unavailable tags.
    $formatted_address = preg_replace( "/\{\{\w+\}\}/", '', $formatted_address );

    // Clean up white space.
	$formatted_address = preg_replace( '/  +/', ' ', trim( $formatted_address ) );
    $formatted_address = preg_replace( '/\n\n+/', "\n", $formatted_address );
    
    // Break newlines apart and remove empty lines/trim commas and white space.
	$formatted_address = array_filter( array_map( 'wpinv_trim_formatted_address_line', explode( "\n", $formatted_address ) ) );

    // Add html breaks.
	$formatted_address = implode( $separator, $formatted_address );

	// We're done!
	return $formatted_address;
    
}

/**
 * Displays the billing address.
 * 
 * @param WPInv_Invoice $invoice
 */
function wpinv_display_to_address( $invoice = 0 ) {
    if ( ! empty( $invoice ) ) {
        wpinv_get_template( 'invoice/billing-address.php', compact( 'invoice' ) );
    }
}
add_action( 'getpaid_invoice_details_left', 'wpinv_display_to_address', 40 );


/**
 * Displays invoice line items.
 */
function wpinv_display_line_items( $invoice_id = 0 ) {

    // Prepare the invoice.
    $invoice = new WPInv_Invoice( $invoice_id );

    // Abort if there is no invoice.
    if ( 0 == $invoice->get_id() ) {
        return;
    }

    // Line item columns.
    $columns = getpaid_invoice_item_columns( $invoice );
    $columns = apply_filters( 'getpaid_invoice_line_items_table_columns', $columns, $invoice );

    wpinv_get_template( 'invoice/line-items.php', compact( 'invoice', 'columns' ) );
}
add_action( 'getpaid_invoice_line_items', 'wpinv_display_line_items', 10 );

/**
 * @param WPInv_Invoice $invoice
 */
function wpinv_display_invoice_notes( $invoice ) {

    // Retrieve the notes.
    $notes = wpinv_get_invoice_notes( $invoice->get_id(), 'customer' );

    // Abort if we have non.
    if ( empty( $notes ) ) {
        return;
    }

    // Echo the note.
    echo '<div class="getpaid-invoice-notes-wrapper border position-relative w-100 mb-4 p-0">';
    echo '<h3 class="getpaid-invoice-notes-title text-dark bg-light border-bottom m-0 d-block">' . __( 'Notes', 'invoicing' ) .'</h3>';
    echo '<ul class="getpaid-invoice-notes mt-4 p-0">';

    foreach( $notes as $note ) {
        wpinv_get_invoice_note_line_item( $note );
    }

    echo '</ul>';
    echo '</div>';
}
add_action( 'getpaid_invoice_line_items', 'wpinv_display_invoice_notes', 60 );

/**
 * Loads scripts on our invoice templates.
 */
function wpinv_display_style() {

    // Make sure that all scripts have been loaded.
    if ( ! did_action( 'wp_enqueue_scripts' ) ) {
        do_action( 'wp_enqueue_scripts' );
    }

    // Register the invoices style.
    wp_register_style( 'wpinv-single-style', WPINV_PLUGIN_URL . 'assets/css/invoice.css', array(), filemtime( WPINV_PLUGIN_DIR . 'assets/css/invoice.css' ) );

    // Load required styles
    wp_print_styles( 'open-sans' );
    wp_print_styles( 'wpinv-single-style' );
    wp_print_styles( 'ayecode-ui' );

    // Maybe load custom css.
    $custom_css = wpinv_get_option( 'template_custom_css' );

    if ( isset( $custom_css ) && ! empty( $custom_css ) ) {
        $custom_css     = wp_kses( $custom_css, array( '\'', '\"' ) );
        $custom_css     = str_replace( '&gt;', '>', $custom_css );
        echo '<style type="text/css">';
        echo $custom_css;
        echo '</style>';
    }

}
add_action( 'wpinv_invoice_print_head', 'wpinv_display_style' );
add_action( 'wpinv_invalid_invoice_head', 'wpinv_display_style' );


/**
 * Displays the checkout page.
 */
function wpinv_checkout_form() {
    global $wpi_checkout_id;

    // Retrieve the current invoice.
    $invoice_id = getpaid_get_current_invoice_id();

    if ( empty( $invoice_id ) ) {

        return aui()->alert(
            array(
                'type'    => 'warning',
                'content' => __( 'Invalid invoice', 'invoicing' ),
            )
        );

    }

    // Can the user view this invoice?
    if ( ! wpinv_user_can_view_invoice( $invoice_id ) ) {

        return aui()->alert(
            array(
                'type'    => 'warning',
                'content' => __( 'You are not allowed to view this invoice', 'invoicing' ),
            )
        );

    }

    // Ensure that it is not yet paid for.
    $invoice = new WPInv_Invoice( $invoice_id );

    // Maybe mark it as viewed.
    getpaid_maybe_mark_invoice_as_viewed( $invoice );

    if ( $invoice->is_paid() ) {

        return aui()->alert(
            array(
                'type'    => 'success',
                'content' => __( 'This invoice has already been paid.', 'invoicing' ),
            )
        );

    }

    // Set the global invoice id.
    $wpi_checkout_id = $invoice_id;

    // We'll display this invoice via the default form.
    $form = new GetPaid_Payment_Form( wpinv_get_default_payment_form() );

    if ( 0 == $form->get_id() ) {

        return aui()->alert(
            array(
                'type'    => 'warning',
                'content' => __( 'Error loading the payment form', 'invoicing' ),
            )
        );

    }

    // Set the invoice.
    $form->invoice = $invoice;
    $form->set_items( $invoice->get_items() );

    // Generate the html.
    return $form->get_html();

}

function wpinv_checkout_cart( $cart_details = array(), $echo = true ) {
    global $ajax_cart_details;
    $ajax_cart_details = $cart_details;

    ob_start();
    do_action( 'wpinv_before_checkout_cart' );
    echo '<div id="wpinv_checkout_cart_form" method="post">';
        echo '<div id="wpinv_checkout_cart_wrap">';
            wpinv_get_template_part( 'wpinv-checkout-cart' );
        echo '</div>';
    echo '</div>';
    do_action( 'wpinv_after_checkout_cart' );
    $content = ob_get_clean();

    if ( $echo ) {
        echo $content;
    } else {
        return $content;
    }
}
add_action( 'wpinv_checkout_cart', 'wpinv_checkout_cart', 10 );

function wpinv_empty_cart_message() {
	return apply_filters( 'wpinv_empty_cart_message', '<span class="wpinv_empty_cart">' . __( 'Your cart is empty.', 'invoicing' ) . '</span>' );
}

/**
 * Echoes the Empty Cart Message
 *
 * @since 1.0
 * @return void
 */
function wpinv_empty_checkout_cart() {
    echo aui()->alert(
        array(
            'type'    => 'warning',
            'content' => wpinv_empty_cart_message(),
        )
    );
}
add_action( 'wpinv_cart_empty', 'wpinv_empty_checkout_cart' );

function wpinv_checkout_cart_columns() {
    $default = 3;
    if ( wpinv_item_quantities_enabled() ) {
        $default++;
    }

    if ( wpinv_use_taxes() ) {
        $default++;
    }

    return apply_filters( 'wpinv_checkout_cart_columns', $default );
}

function wpinv_receipt_billing_address( $invoice_id = 0 ) {
    $invoice = wpinv_get_invoice( $invoice_id );

    if ( empty( $invoice ) ) {
        return NULL;
    }

    $billing_details = $invoice->get_user_info();
    $address_row = wpinv_get_invoice_address_markup( $billing_details );

    ob_start();
    ?>
    <table class="table table-bordered table-sm wpi-billing-details">
        <tbody>
            <tr class="wpi-receipt-name">
                <th class="text-left"><?php _e( 'Name', 'invoicing' ); ?></th>
                <td><?php echo esc_html( trim( $billing_details['first_name'] . ' ' . $billing_details['last_name'] ) ) ;?></td>
            </tr>
            <tr class="wpi-receipt-email">
                <th class="text-left"><?php _e( 'Email', 'invoicing' ); ?></th>
                <td><?php echo $billing_details['email'] ;?></td>
            </tr>
            <tr class="wpi-receipt-address">
                <th class="text-left"><?php _e( 'Address', 'invoicing' ); ?></th>
                <td><?php echo $address_row ;?></td>
            </tr>
            <?php if ( $billing_details['phone'] ) { ?>
            <tr class="wpi-receipt-phone">
                <th class="text-left"><?php _e( 'Phone', 'invoicing' ); ?></th>
                <td><?php echo esc_html( $billing_details['phone'] ) ;?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php
    $output = ob_get_clean();
    
    $output = apply_filters( 'wpinv_receipt_billing_address', $output, $invoice_id );

    echo $output;
}

/**
 * Filters the receipt page.
 */
function wpinv_filter_success_page_content( $content ) {

    // Ensure this is our page.
    if ( isset( $_GET['payment-confirm'] ) && wpinv_is_success_page() ) {

        $gateway = sanitize_text_field( $_GET['payment-confirm'] );
        return apply_filters( "wpinv_payment_confirm_$gateway", $content );

    }

    return $content;
}
add_filter( 'the_content', 'wpinv_filter_success_page_content', 99999 );

function wpinv_invoice_link( $invoice_id ) {
    $invoice = wpinv_get_invoice( $invoice_id );

    if ( empty( $invoice ) ) {
        return NULL;
    }

    $invoice_link = '<a href="' . esc_url( $invoice->get_view_url() ) . '">' . $invoice->get_number() . '</a>';

    return apply_filters( 'wpinv_get_invoice_link', $invoice_link, $invoice );
}

function wpinv_invoice_subscription_details( $invoice ) {
    if ( !empty( $invoice ) && $invoice->is_recurring() && ! wpinv_is_subscription_payment( $invoice ) ) {
        $subscription = wpinv_get_subscription( $invoice, true );

        if ( empty( $subscription ) ) {
            return;
        }

        $frequency = WPInv_Subscriptions::wpinv_get_pretty_subscription_frequency($subscription->period, $subscription->frequency);
        $billing = wpinv_price(wpinv_format_amount($subscription->recurring_amount), $invoice->get_currency() ) . ' / ' . $frequency;
        $initial = wpinv_price(wpinv_format_amount($subscription->initial_amount), $invoice->get_currency() );

        $payments = $subscription->get_child_payments();
        ?>
        <div class="wpinv-subscriptions-details">
            <h3 class="wpinv-subscriptions-t"><?php echo apply_filters( 'wpinv_subscription_details_title', __( 'Subscription Details', 'invoicing' ) ); ?></h3>
            <table class="table">
                <thead>
                    <tr>
                        <th><?php _e( 'Billing Cycle', 'invoicing' ) ;?></th>
                        <th><?php _e( 'Start Date', 'invoicing' ) ;?></th>
                        <th><?php _e( 'Expiration Date', 'invoicing' ) ;?></th>
                        <th class="text-center"><?php _e( 'Times Billed', 'invoicing' ) ;?></th>
                        <th class="text-center"><?php _e( 'Status', 'invoicing' ) ;?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php printf(_x('%s then %s', 'Initial subscription amount then billing cycle and amount', 'invoicing'), $initial, $billing); ?></td>
                        <td><?php echo date_i18n(get_option('date_format'), strtotime($subscription->created, current_time('timestamp'))); ?></td>
                        <td><?php echo date_i18n(get_option('date_format'), strtotime($subscription->expiration, current_time('timestamp'))); ?></td>
                        <td class="text-center"><?php echo $subscription->get_times_billed() . ' / ' . (($subscription->bill_times == 0) ? 'Until Cancelled' : $subscription->bill_times); ?></td>
                        <td class="text-center wpi-sub-status"><?php echo $subscription->get_status_label(); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php if ( !empty( $payments ) ) { ?>
        <div class="wpinv-renewal-payments">
            <h3 class="wpinv-renewals-t"><?php echo apply_filters( 'wpinv_renewal_payments_title', __( 'Renewal Payments', 'invoicing' ) ); ?></h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php _e( 'Invoice', 'invoicing' ) ;?></th>
                        <th><?php _e( 'Date', 'invoicing' ) ;?></th>
                        <th class="text-right"><?php _e( 'Amount', 'invoicing' ) ;?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $i = 1;
                        foreach ( $payments as $payment ) {
                            $invoice_id = $payment->ID;
                    ?>
                    <tr>
                        <th scope="row"><?php echo $i;?></th>
                        <td><?php echo wpinv_invoice_link( $invoice_id ) ;?></td>
                        <td><?php echo$invoice->get_date_created(); ?></td>
                        <td class="text-right"><?php echo wpinv_price( wpinv_format_amount( $invoice->get_total() ), $invoice->get_currency() ); ?></td>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
        <?php
    }
}
add_action( 'getpaid_invoice_line_items', 'wpinv_invoice_subscription_details', 20 );

function wpinv_cart_total_label( $label, $invoice ) {
    if ( empty( $invoice ) ) {
        return $label;
    }

    $prefix_label = '';
    if ( $invoice->is_parent() && $item_id = $invoice->get_recurring() ) {
        $prefix_label   = '<span class="label label-primary label-recurring">' . __( 'Recurring Payment', 'invoicing' ) . '</span> ' . wpinv_subscription_payment_desc( $invoice );
    } else if ( $invoice->is_renewal() ) {
        $prefix_label   = '<span class="label label-primary label-renewal">' . __( 'Renewal Payment', 'invoicing' ) . '</span> ';        
    }

    if ( $prefix_label != '' ) {
        $label  = '<span class="wpinv-cart-sub-desc">' . $prefix_label . '</span> ' . $label;
    }

    return $label;
}
add_filter( 'wpinv_cart_total_label', 'wpinv_cart_total_label', 10, 2 );
add_filter( 'wpinv_email_cart_total_label', 'wpinv_cart_total_label', 10, 2 );
add_filter( 'wpinv_print_cart_total_label', 'wpinv_cart_total_label', 10, 2 );

function wpinv_get_invoice_note_line_item( $note, $echo = true ) {
    if ( empty( $note ) ) {
        return NULL;
    }

    if ( is_int( $note ) ) {
        $note = get_comment( $note );
    }

    if ( !( is_object( $note ) && is_a( $note, 'WP_Comment' ) ) ) {
        return NULL;
    }

    $note_classes   = array( 'note' );
    $note_classes[] = get_comment_meta( $note->comment_ID, '_wpi_customer_note', true ) ? 'customer-note' : '';
    $note_classes[] = $note->comment_author === 'System' ? 'system-note' : '';
    $note_classes   = apply_filters( 'wpinv_invoice_note_class', array_filter( $note_classes ), $note );
    $note_classes   = !empty( $note_classes ) ? implode( ' ', $note_classes ) : '';

    ob_start();
    ?>
    <li rel="<?php echo absint( $note->comment_ID ) ; ?>" class="<?php echo esc_attr( $note_classes ); ?> mt-4 pl-3 pr-3">
        <div class="note_content bg-light border position-relative p-4">

            <?php echo wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ); ?>

            <?php if ( ! is_admin() ) : ?>
                <em class="meta position-absolute form-text">
                    <?php
                        printf(
                            __( '%1$s - %2$s at %3$s', 'invoicing' ),
                            $note->comment_author,
                            date_i18n( get_option( 'date_format' ), strtotime( $note->comment_date ) ),
                            date_i18n( get_option( 'time_format' ), strtotime( $note->comment_date ) )
                        );
                    ?>
                </em>
            <?php endif; ?>

        </div>

        <?php if ( is_admin() ) : ?>

            <p class="meta px-4 py-2">
                <abbr class="exact-date" title="<?php echo esc_attr( $note->comment_date ); ?>"><?php printf( __( '%1$s - %2$s at %3$s', 'invoicing' ), $note->comment_author, date_i18n( get_option( 'date_format' ), strtotime( $note->comment_date ) ), date_i18n( get_option( 'time_format' ), strtotime( $note->comment_date ) ) ); ?></abbr>&nbsp;&nbsp;
                <?php if ( $note->comment_author !== 'System' && wpinv_current_user_can_manage_invoicing() ) { ?>
                    <a href="#" class="delete_note"><?php _e( 'Delete note', 'invoicing' ); ?></a>
                <?php } ?>
            </p>

        <?php endif; ?>
        
    </li>
    <?php
    $note_content = ob_get_clean();
    $note_content = apply_filters( 'wpinv_get_invoice_note_line_item', $note_content, $note, $echo );

    if ( $echo ) {
        echo $note_content;
    } else {
        return $note_content;
    }
}

function wpinv_invalid_invoice_content() {
    global $post;

    $invoice = wpinv_get_invoice( $post->ID );

    $error = __( 'This invoice is only viewable by clicking on the invoice link that was sent to you via email.', 'invoicing' );
    if ( !empty( $invoice->get_id() ) && $invoice->has_status( array_keys( wpinv_get_invoice_statuses() ) ) ) {
        if ( is_user_logged_in() ) {
            if ( wpinv_require_login_to_checkout() ) {
                if ( isset( $_GET['invoice_key'] ) && $_GET['invoice_key'] === $invoice->get_key() ) {
                    $error = __( 'You are not allowed to view this invoice.', 'invoicing' );
                }
            }
        } else {
            if ( wpinv_require_login_to_checkout() ) {
                if ( isset( $_GET['invoice_key'] ) && $_GET['invoice_key'] === $invoice->get_key() ) {
                    $error = __( 'You must be logged in to view this invoice.', 'invoicing' );
                }
            }
        }
    } else {
        $error = __( 'This invoice is deleted or does not exist.', 'invoicing' );
    }
    ?>
    <div class="row wpinv-row-invalid">
        <div class="col-md-6 col-md-offset-3 wpinv-message error">
            <h3><?php _e( 'Access Denied', 'invoicing' ); ?></h3>
            <p class="wpinv-msg-text"><?php echo $error; ?></p>
        </div>
    </div>
    <?php
}
add_action( 'wpinv_invalid_invoice_content', 'wpinv_invalid_invoice_content' );

/**
 * Function to get privacy policy text.
 *
 * @since 1.0.13
 * @return string
 */
function wpinv_get_policy_text() {
    $privacy_page_id = get_option( 'wp_page_for_privacy_policy', 0 );

    $text = wpinv_get_option('invoicing_privacy_checkout_message', sprintf( __( 'Your personal data will be used to process your invoice, payment and for other purposes described in our %s.', 'invoicing' ), '[wpinv_privacy_policy]' ));

    if(!$privacy_page_id){
        $privacy_page_id = wpinv_get_option( 'privacy_page', 0 );
    }

    $privacy_link    = $privacy_page_id ? '<a href="' . esc_url( get_permalink( $privacy_page_id ) ) . '" class="wpinv-privacy-policy-link" target="_blank">' . __( 'privacy policy', 'invoicing' ) . '</a>' : __( 'privacy policy', 'invoicing' );

    $find_replace = array(
        '[wpinv_privacy_policy]' => $privacy_link,
    );

    $privacy_text = str_replace( array_keys( $find_replace ), array_values( $find_replace ), $text );

    return wp_kses_post(wpautop($privacy_text));
}


/**
 * Allows the user to set their own price for an invoice item
 */
function wpinv_checkout_cart_item_name_your_price( $cart_item, $key ) {
    
    //Ensure we have an item id
    if(! is_array( $cart_item ) || empty( $cart_item['id'] ) ) {
        return;
    }

    //Fetch the item
    $item_id = $cart_item['id'];
    $item    = new WPInv_Item( $item_id );
    
    if(! $item->supports_dynamic_pricing() || !$item->get_is_dynamic_pricing() ) {
        return;
    }

    //Fetch the dynamic pricing "strings"
    $suggested_price_text = esc_html( wpinv_get_option( 'suggested_price_text', __( 'Suggested Price:', 'invoicing' ) ) );
    $minimum_price_text   = esc_html( wpinv_get_option( 'minimum_price_text', __( 'Minimum Price:', 'invoicing' ) ) );
    $name_your_price_text = esc_html( wpinv_get_option( 'name_your_price_text', __( 'Name Your Price', 'invoicing' ) ) );

    //Display a "name_your_price" button
    echo " &mdash; <a href='#' class='wpinv-name-your-price-frontend small'>$name_your_price_text</a></div>";

    //Display a name_your_price form
    echo '<div class="name-your-price-miniform">';
    
    //Maybe display the recommended price
    if( $item->get_price() > 0 && !empty( $suggested_price_text ) ) {
        $suggested_price = $item->get_the_price();
        echo "<div>$suggested_price_text &mdash; $suggested_price</div>";
    }

    //Display the update price form
    $symbol         = wpinv_currency_symbol();
    $position       = wpinv_currency_position();
    $minimum        = esc_attr( $item->get_minimum_price() );
    $price          = esc_attr( $cart_item['item_price'] );
    $update         = esc_attr__( "Update", 'invoicing' );

    //Ensure it supports dynamic prici
    if( $price < $minimum ) {
        $price = $minimum;
    }

    echo '<label>';
    echo $position != 'right' ? $symbol . '&nbsp;' : '';
    echo "<input type='number' min='$minimum' placeholder='$price' value='$price' class='wpi-field-price' />";
    echo $position == 'right' ? '&nbsp;' . $symbol : '' ;
    echo "</label>";
    echo "<input type='hidden' value='$item_id' class='wpi-field-item' />";
    echo "<a class='btn btn-success wpinv-submit wpinv-update-dynamic-price-frontend'>$update</a>";

    //Maybe display the minimum price
    if( $item->get_minimum_price() > 0 && !empty( $minimum_price_text ) ) {
        $minimum_price = wpinv_price( wpinv_format_amount( $item->get_minimum_price() ) );
        echo "<div>$minimum_price_text &mdash; $minimum_price</div>";
    }

    echo "</div>";

}
add_action( 'wpinv_checkout_cart_item_price_after', 'wpinv_checkout_cart_item_name_your_price', 10, 2 );

function wpinv_oxygen_fix_conflict() {
    global $ct_ignore_post_types;

    if ( ! is_array( $ct_ignore_post_types ) ) {
        $ct_ignore_post_types = array();
    }

    $post_types = array( 'wpi_discount', 'wpi_invoice', 'wpi_item' );

    foreach ( $post_types as $post_type ) {
        $ct_ignore_post_types[] = $post_type;

        // Ignore post type
        add_filter( 'pre_option_oxygen_vsb_ignore_post_type_' . $post_type, '__return_true', 999 );
    }

    remove_filter( 'template_include', 'wpinv_template', 10, 1 );
    add_filter( 'template_include', 'wpinv_template', 999, 1 );
}

/**
 * Helper function to display a payment form on the frontend.
 * 
 * @param GetPaid_Payment_Form $form
 */
function getpaid_display_payment_form( $form ) {

    if ( is_numeric( $form ) ) {
        $form = new GetPaid_Payment_Form( $form );
    }

    $form->display();

}

/**
 * Helper function to display a item payment form on the frontend.
 */
function getpaid_display_item_payment_form( $items ) {
    global $invoicing;

    foreach ( array_keys( $items ) as $id ) {
	    if ( 'publish' != get_post_status( $id ) ) {
		    unset( $items[ $id ] );
	    }
    }

    if ( empty( $items ) ) {
		return aui()->alert(
			array(
				'type'    => 'warning',
				'content' => __( 'No published items found', 'invoicing' ),
			)
		);
    }

    $item_key = getpaid_convert_items_to_string( $items );

    // Get the form elements and items.
    $form     = wpinv_get_default_payment_form();
	$elements = $invoicing->form_elements->get_form_elements( $form );
	$items    = $invoicing->form_elements->convert_normal_items( $items );

	ob_start();
	echo "<form class='wpinv_payment_form'>";
	do_action( 'wpinv_payment_form_top' );
    echo "<input type='hidden' name='form_id' value='$form'/>";
    echo "<input type='hidden' name='form_items' value='$item_key'/>";
	wp_nonce_field( 'wpinv_payment_form', 'wpinv_payment_form' );
	wp_nonce_field( 'vat_validation', '_wpi_nonce' );

	foreach ( $elements as $element ) {
		do_action( 'wpinv_frontend_render_payment_form_element', $element, $items, $form );
		do_action( "wpinv_frontend_render_payment_form_{$element['type']}", $element, $items, $form );
	}

	echo "<div class='wpinv_payment_form_errors alert alert-danger d-none'></div>";
	do_action( 'wpinv_payment_form_bottom' );
	echo '</form>';

	$content = ob_get_clean();
	return str_replace( 'sr-only', '', $content );
}

/**
 * Helper function to display an invoice payment form on the frontend.
 */
function getpaid_display_invoice_payment_form( $invoice_id ) {
    global $invoicing;

    $invoice = wpinv_get_invoice( $invoice_id );

    if ( empty( $invoice ) ) {
		return aui()->alert(
			array(
				'type'    => 'warning',
				'content' => __( 'Invoice not found', 'invoicing' ),
			)
		);
    }

    if ( $invoice->is_paid() ) {
		return aui()->alert(
			array(
				'type'    => 'warning',
				'content' => __( 'Invoice has already been paid', 'invoicing' ),
			)
		);
    }

    // Get the form elements and items.
    $form     = wpinv_get_default_payment_form();
	$elements = $invoicing->form_elements->get_form_elements( $form );
	$items    = $invoicing->form_elements->convert_checkout_items( $invoice->cart_details, $invoice );

	ob_start();
	echo "<form class='wpinv_payment_form'>";
	do_action( 'wpinv_payment_form_top' );
    echo "<input type='hidden' name='form_id' value='$form'/>";
    echo "<input type='hidden' name='invoice_id' value='$invoice_id'/>";
	wp_nonce_field( 'wpinv_payment_form', 'wpinv_payment_form' );
	wp_nonce_field( 'vat_validation', '_wpi_nonce' );

	foreach ( $elements as $element ) {
		do_action( 'wpinv_frontend_render_payment_form_element', $element, $items, $form );
		do_action( "wpinv_frontend_render_payment_form_{$element['type']}", $element, $items, $form );
	}

	echo "<div class='wpinv_payment_form_errors alert alert-danger d-none'></div>";
	do_action( 'wpinv_payment_form_bottom' );
	echo '</form>';

	$content = ob_get_clean();
	return str_replace( 'sr-only', '', $content );
}

/**
 * Helper function to convert item string to array.
 */
function getpaid_convert_items_to_array( $items ) {
    $items    = array_filter( array_map( 'trim', explode( ',', $items ) ) );
    $prepared = array();

    foreach ( $items as $item ) {
        $data = array_map( 'trim', explode( '|', $item ) );

        if ( empty( $data[0] ) || ! is_numeric( $data[0] ) ) {
            continue;
        }

        $quantity = 1;
        if ( isset( $data[1] ) && is_numeric( $data[1] ) ) {
            $quantity = $data[1];
        }

        $prepared[ $data[0] ] = $quantity;

    }

    return $prepared;
}

/**
 * Helper function to convert item array to string.
 */
function getpaid_convert_items_to_string( $items ) {
    $prepared = array();

    foreach ( $items as $item => $quantity ) {
        $prepared[] = "$item|$quantity";
    }
    return implode( ',', $prepared );
}

/**
 * Helper function to display a payment item.
 * 
 * Provide a label and one of $form, $items or $invoice.
 */
function getpaid_get_payment_button( $label, $form = null, $items = null, $invoice = null ) {
    $label = sanitize_text_field( $label );
    $nonce = wp_create_nonce('getpaid_ajax_form');

    if ( ! empty( $form ) ) {
        $form  = esc_attr( $form );
        return "<button class='btn btn-primary getpaid-payment-button' type='button' data-nonce='$nonce' data-form='$form'>$label</button>"; 
    }
	
	if ( ! empty( $items ) ) {
        $items  = esc_attr( $items );
        return "<button class='btn btn-primary getpaid-payment-button' type='button' data-nonce='$nonce' data-item='$items'>$label</button>"; 
    }
    
    if ( ! empty( $invoice ) ) {
        $invoice  = esc_attr( $invoice );
        return "<button class='btn btn-primary getpaid-payment-button' type='button' data-nonce='$nonce' data-invoice='$invoice'>$label</button>"; 
    }

}

/**
 * Display invoice description before line items.
 *
 * @param WPInv_Invoice $invoice
 */
function getpaid_the_invoice_description( $invoice ) {
    $description = $invoice->get_description();

    if ( empty( $description ) ) {
        return;
    }

    $description = wp_kses_post( $description );
    echo "<small class='getpaid-invoice-description text-dark p-2 form-text'><em>$description</em></small>";
}
add_action( 'getpaid_invoice_line_items', 'getpaid_the_invoice_description', 100 );

/**
 * Render element on a form.
 *
 * @param array $element
 * @param GetPaid_Payment_Form $form
 */
function getpaid_payment_form_element( $element, $form ) {

    // Set up the args.
    $element_type    = trim( $element['type'] );
    $element['form'] = $form;
    extract( $element );

    // Try to locate the appropriate template.
    $located = wpinv_locate_template( "payment-forms/elements/$element_type.php" );
    
    // Abort if this is not our element.
    if ( empty( $located ) || ! file_exists( $located ) ) {
        return;
    }

    // Generate the class and id of the element.
    $wrapper_class = 'getpaid-payment-form-element-' . trim( esc_attr( $element_type ) );
    $id            = isset( $id ) ? $id : uniqid( 'gp' );

    // Echo the opening wrapper.
    echo "<div class='getpaid-payment-form-element $wrapper_class'>";

    // Fires before displaying a given element type's content.
    do_action( "getpaid_before_payment_form_{$element_type}_element", $element, $form );

    // Include the template for the element.
    include $located;

    // Fires after displaying a given element type's content.
    do_action( "getpaid_payment_form_{$element_type}_element", $element, $form );

    // Echo the closing wrapper.
    echo '</div>';
}
add_action( 'getpaid_payment_form_element', 'getpaid_payment_form_element', 10, 2 );

/**
 * Shows a list of gateways that support recurring payments.
 */
function wpinv_get_recurring_gateways_text() {
    $gateways = array();

    foreach ( wpinv_get_payment_gateways() as $key => $gateway ) {
        if ( wpinv_gateway_support_subscription( $key ) ) {
            $gateways[] = sanitize_text_field( $gateway['admin_label'] );
        }
    }

    if ( empty( $gateways ) ) {
        return "<span class='form-text text-danger'>" . __( 'No active gateways support subscription payments.', 'invoicing' ) ."</span>";
    }

    return "<span class='form-text text-muted'>" . wp_sprintf( __( 'Subscription payments only supported by: %s', 'invoicing' ), implode( ', ', $gateways ) ) ."</span>";

}
