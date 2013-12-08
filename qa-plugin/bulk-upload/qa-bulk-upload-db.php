<?php
	
/*Create database table
TABLE NAME: qa_forUpload
*/

class qa_bulk_upload_database{
	
	function init_queries($tableslc){
		
		$tablename='qa_forupload'; 
		if(!in_array($tablename, $tableslc)) { 

			return 'CREATE TABLE IF NOT EXISTS `'.$tablename.'` ( 
			`id` int primary key not null auto_increment, 
			`content` text,
			`title` tinytext,
			`uploadDate` date
			)'; 
	}
}
	
}