<?php
	/*
		Plugin Name: Roster Maintenance
		Plugin URI:
		Plugin Description: Edit Q2A users to match Sakai roster
		Plugin Version: 1.0
		Plugin Date: 2013-11-18
		Plugin Author: Diyang Qu, Ethan Overman
		Plugin Author URI:
		Plugin License: GPLv2
		Plugin Minimum Question2Answer Version: 1.6
		Plugin Update Check URI: 
	*/

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}


	qa_register_plugin_module('page', 'roster-maintenance.php', 'roster_maintenance', 'Roster Maintenance');

/*
	Omit PHP closing tag to help avoid accidental output
*/