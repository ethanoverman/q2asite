<?php

	define('__ROOT__', dirname(dirname(__FILE__)));

    require_once(__ROOT__.'/qa-include/qa-base.php');
    require_once QA_INCLUDE_DIR.'qa-db-admin.php';

    $categoryName=$_POST['name'];

    $checker = qa_db_query_raw("SELECT * from qa_categories where title='".$categoryName."'");//db select on inputted category
    $checker = qa_db_read_all_assoc($checker);

    if(count($checker)==0) //check to see if category name exists
    {
		echo qa_db_category_create(NULL, $categoryName, $categoryName); //create new category upon safe check
    }

    else
    {
    	header('HTTP/1.1 400 Bad Request'); //return error upon existing category
    	print("Category already exists");
    }



