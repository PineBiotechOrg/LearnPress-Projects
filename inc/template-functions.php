<?php

if ( ! function_exists( 'lp_project_info' ) ) {
	function lp_project_info() {
		$course    = LP()->global['course'];
		$course_id = get_the_ID();

		$course_skill_level = get_post_meta( $course_id, 'thim_course_skill_level', true );
		$course_language    = get_post_meta( $course_id, 'thim_course_language', true );
		$course_duration    = get_post_meta( $course_id, 'thim_course_duration', true );

		?>
        <div class="thim-course-info">
            <h3 class="title"><?php esc_html_e( 'Project Features', 'lp-projects' ); ?></h3>
            <ul>
                <li class="lectures-feature">
                    <i class="fa fa-files-o"></i>
                    <span class="label"><?php esc_html_e( 'Lectures', 'lp-projects' ); ?></span>
                    <span class="value"><?php echo $course->get_curriculum_items( 'lp_lesson' ) ? count( $course->get_curriculum_items( 'lp_lesson' ) ) : 0; ?></span>
                </li>
                <li class="quizzes-feature">
                    <i class="fa fa-puzzle-piece"></i>
                    <span class="label"><?php esc_html_e( 'Quizzes', 'lp-projects' ); ?></span>
                    <span class="value"><?php echo $course->get_curriculum_items( 'lp_quiz' ) ? count( $course->get_curriculum_items( 'lp_quiz' ) ) : 0; ?></span>
                </li>
				<?php if ( ! empty( $course_duration ) ): ?>
                    <li class="duration-feature">
                        <i class="fa fa-clock-o"></i>
                        <span class="label"><?php esc_html_e( 'Duration', 'lp-projects' ); ?></span>
                        <span class="value"><?php echo $course_duration; ?></span>
                    </li>
				<?php endif; ?>
				<?php if ( ! empty( $course_skill_level ) ): ?>
                    <li class="skill-feature">
                        <i class="fa fa-level-up"></i>
                        <span class="label"><?php esc_html_e( 'Skill level', 'lp-projects' ); ?></span>
                        <span class="value"><?php echo esc_html( $course_skill_level ); ?></span>
                    </li>
				<?php endif; ?>
				<?php if ( ! empty( $course_language ) ): ?>
                    <li class="language-feature">
                        <i class="fa fa-language"></i>
                        <span class="label"><?php esc_html_e( 'Language', 'lp-projects' ); ?></span>
                        <span class="value"><?php echo esc_html( $course_language ); ?></span>
                    </li>
				<?php endif; ?>
                <li class="students-feature">
                    <i class="fa fa-users"></i>
                    <span class="label"><?php esc_html_e( 'Students', 'lp-projects' ); ?></span>
					<?php $user_count = $course->get_users_enrolled() ? $course->get_users_enrolled() : 0; ?>
                    <span class="value"><?php echo esc_html( $user_count ); ?></span>
                </li>
				<?php thim_course_certificate( $course_id ); ?>
                <li class="assessments-feature">
                    <i class="fa fa-check-square-o"></i>
                    <span class="label"><?php esc_html_e( 'Assessments', 'lp-projects' ); ?></span>
                    <span class="value"><?php echo ( get_post_meta( $course_id, '_lp_course_result', true ) == 'evaluate_lesson' ) ? esc_html__( 'Yes', 'lp-projects' ) : esc_html__( 'Self', 'lp-projects' ); ?></span>
                </li>
            </ul>
			<?php do_action( 'thim_after_course_info' ); ?>
        </div>
		<?php
	}
}