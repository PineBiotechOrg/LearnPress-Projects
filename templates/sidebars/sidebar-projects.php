<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package thim
 */
if ( !is_active_sidebar( 'sidebar_projects' ) ) {
	return;
}
$theme_options_data = get_theme_mods();
//$sticky_sidebar     = ( !isset( $theme_options_data['thim_sticky_sidebar'] ) || $theme_options_data['thim_sticky_sidebar'] == true ) ? ' sticky-sidebar' : '';
$sticky_sidebar     = !empty( $theme_options_data['thim_sticky_sidebar'] ) ? ' sticky-sidebar' : '';
$cls_sidebar = '';
if ( get_theme_mod( 'thim_header_style', 'header_v1' ) == 'header_v4' ) {
    $cls_sidebar = ' sidebar_' . get_theme_mod( 'thim_header_style' );
}
?>

<aside id="sidebar" class="widget-area col-sm-3<?php echo esc_attr( $sticky_sidebar ); ?><?php echo $cls_sidebar;?>" role="complementary">
	<?php if ( !dynamic_sidebar( 'sidebar_projects' ) ) :
		dynamic_sidebar( 'sidebar_projects' );
	endif; // end sidebar widget area ?>
</aside><!-- #secondary -->