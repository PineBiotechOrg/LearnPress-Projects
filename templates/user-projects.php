<?php
/**
 * Template for displaying archive course content.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/content-archive-course.php
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

global $post, $wp_query, $lp_tax_query;

$show_description = get_theme_mod( 'thim_learnpress_cate_show_description' );
$show_desc   = !empty( $show_description ) ? $show_description : '';
$cat_desc = term_description();

$total = $query->found_posts;

if ( $total == 0 ) {
    $message = '<p class="message message-error">' . esc_html__( 'No projects found!', 'eduma' ) . '</p>';
    $index   = esc_html__( 'There are no available projects!', 'eduma' );
} elseif ( $total == 1 ) {
    $index = esc_html__( 'Showing only one result', 'eduma' );
} else {
    $courses_per_page = absint( LP()->settings->get( 'archive_course_limit' ) );
    $paged            = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;

    $from = 1 + ( $paged - 1 ) * $courses_per_page;
    $to   = ( $paged * $courses_per_page > $total ) ? $total : $paged * $courses_per_page;

    if ( $from == $to ) {
        $index = sprintf(
            esc_html__( 'Showing last project of %s results', 'eduma' ),
            $total
        );
    } else {
        $index = sprintf(
            esc_html__( 'Showing %s-%s of %s results', 'eduma' ),
            $from,
            $to,
            $total
        );
    }
}

$cookie_name = 'course_switch';
$layout      = ( !empty( $_COOKIE[$cookie_name] ) ) ? $_COOKIE[$cookie_name] : 'grid-layout';

/**
 * @deprecated
 */
do_action( 'learn_press_before_main_content' );

/**
 * @since 3.0.0
 */
do_action( 'learn-press/before-main-content' );

/**
 * @deprecated
 */
do_action( 'learn_press_archive_description' );

/**
 * @since 3.0.0
 */
do_action( 'learn-press/archive-description' );

?>

<?php if ( $total == 0 ) : ?>
    <?php if( $show_desc && $cat_desc ) {?>
        <div class="desc_cat">
            <?php echo $cat_desc;?>
        </div>
    <?php }?>
    <div id="thim-course-archive" class="<?php echo ( $layout == 'list-layout' ) ? 'thim-course-list' : 'thim-course-grid'; ?>" data-cookie="grid-layout">
        <?php echo $message; ?>
    </div>
<?php else: ?>
    <?php
    /**
     * @deprecated
     */
    do_action( 'learn_press_before_courses_loop' );
    ?>
<div class="projects-tabs-container">
    <?php

    /**
     * @since 3.0.0
     */
    do_action( 'learn-press/before-courses-loop' );

    ?>

</div>

    <?php

    learn_press_begin_courses_loop();

    ?>

    <?php if( $show_desc && $cat_desc ) {?>
        <div class="desc_cat">
            <?php echo $cat_desc;?>
        </div>
    <?php }?>

    <div id="lp-project-archive" class="thim-course-list" data-cookie="grid-layout">
        <?php while ( $query->have_posts() ) : $query->the_post(); ?>

            	<?php 

                    if( function_exists('bp_is_active') ) {

                        if( bp_is_user() ) {

                            $user_id = bp_displayed_user_id();
                            $user = learn_press_get_user( $user_id );

                        }

                    }

                    if( is_null( $user ) ) {

                        $user = LP_Global::user();

                    }

        			

        			if( $user->has_purchased_course( $post->ID ) ) {

        				learn_press_projects_template_part( 'content', 'course' ); 
        				
        			}



            	?>


        <?php endwhile; ?>

        <?php wp_reset_postdata(); ?>
    </div>

    <?php

    learn_press_end_courses_loop();

    learn_press_paging_nav(
			array(
				'num_pages'     => $query->max_num_pages,
				'wrapper_class' => 'learn-press-pagination',
				'paged'         => get_query_var( 'projects_page' ),
				'format'		=> '?projects_page=%#%'
			)
		);

    /**
     * @since 3.0.0
     */
    do_action( 'learn_press_after_courses_loop' );

    /**
     * @deprecated
     */
    do_action( 'learn-press/after-courses-loop' );

    wp_reset_postdata();
    ?>
<?php endif; ?>

<?php

/**
 * @since 3.0.0
 */
do_action( 'learn-press/after-main-content' );

/**
 * @deprecated
 */
do_action( 'learn_press_after_main_content' );