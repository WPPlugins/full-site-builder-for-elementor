<?php
/**
 * Metabox showing on all pages and posts.
 *
 * @package dtbaker-elementor
 */

// grab a list of all page templates.
$styles          = $this->get_all_page_styles();
$components = $this->get_all_page_components();
$current_default = $this->get_current_style(true);

$current_style = $this->get_page_template($post->ID);

wp_nonce_field( 'dtbaker_elementor_style_nonce', 'dtbaker_elementor_style_nonce' );
?>
<label class="screen-reader-text" for="dtbaker_page_style"><?php esc_html_e( 'Page Style', 'stylepress' ); ?></label>
<p>
	<small><?php
		// Translators: The first %s is a link <a href=""> and the second %s is a closing link </a>.
		printf( esc_html__( 'You can override the default style here. Choose the style to apply to this particular page. Edit these styles from the %1$sStylePress%2$s page.', 'stylepress' ), '<a href="' . esc_url( admin_url( 'admin.php?page=dtbaker-stylepress' ) ) . '">', '</a>' ); ?></small>
</p>
<p>
    <small><?php
    // Translators: The %s is the current post type
	    printf( esc_html__( 'This page type is: %s', 'stylepress' ), ucwords( str_replace('_',' ',$this->get_current_page_type()) )); ?>
        </small>
</p>

<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="dtbaker_page_style"><?php _e('Outer Style');?></label></p>
<select name="dtbaker_style[style]" id="dtbaker_page_style">
	<option value="0"><?php
		// Translators: %s contains the current default style.
		printf( esc_html__( 'Default %s', 'stylepress' ), esc_attr( isset($styles[ $current_default ]) ? '(' . $styles[ $current_default ] . ')' : '' ) ); ?></option>
    <option value="-1"><?php esc_html_e('Original Theme Output', 'stylepress')?></option>
	<?php foreach ( $styles as $option_id => $option_val ) {
		?>
		<option value="<?php echo esc_attr( $option_id ); ?>"<?php echo $current_style && (int) $current_style === (int) $option_id ? ' selected' : ''; ?>><?php echo esc_attr( $option_val ); ?></option>
		<?php
	}
	?>
</select>


