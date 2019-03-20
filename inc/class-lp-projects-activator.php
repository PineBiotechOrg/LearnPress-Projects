<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/JoshuaMcKendall/LearnPress-Projects-Plugin/inc/
 * @since      1.0.0
 *
 * @package    lp-projects
 * @subpackage LearnPress-Projects/inc
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    lp-projects
 * @subpackage LearnPress-Projects/inc
 * @author     Joshua McKendall <mail@joshuamckendall.com>
 */
class LP_Projects_Activator {

	/**
	 *
	 *
	 * @since    1.0.0
	 */
	public static function activate(LP_Projects_Activator $lp_projects) {
		
		$lp_projects->create_options();

		flush_rewrite_rules();
		
	}
	
	private function create_options() {

		update_option( 'project_structure', 'projects/%project%' );	

	}
	

}