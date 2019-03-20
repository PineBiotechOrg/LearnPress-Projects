<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/JoshuaMcKendall/Illustrator-Plugin/inc
 * @since      1.0.0
 *
 * @package    lp-projects
 * @subpackage LeanrPress-Projects/inc
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    lp-projects
 * @subpackage LeanrPress-Projects/inc
 * @author     Joshua McKendall <mail@joshuamckendall.com>
 */
class LP_Projects_Deactivator {

	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		flush_rewrite_rules();

	}

}
