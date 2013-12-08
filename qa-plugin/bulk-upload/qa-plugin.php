<?php

/*
	Plugin Name: Bulk Upload
	Plugin URI: 
	Plugin Description: Allows for the bulk upload of questions from an XML file
	Plugin Version: 1.0
	Plugin Date: 2013-10-21
	Plugin Author: Arie Wolf
	Plugin Author URI:
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: 
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

qa_register_plugin_module(
	'page', // type of module
	'qa-bulk-upload-page.php', // PHP file containing module class
	'qa_bulk_upload_page', // name of module class
	'Bulk Upload' // human-readable name of module
);

qa_register_plugin_module(
	'module', // type of module
	'qa-bulk-upload-db.php', // PHP file containing module class
	'qa_bulk_upload_database', // module class name in that PHP file
	'BU Database Load' // human-readable name of module
);