<?php
	define('__ROOT__', dirname(dirname(__FILE__)));
	require_once(__ROOT__.'/qa-include/qa-base.php');
	require_once QA_INCLUDE_DIR.'qa-app-users.php';
    require_once QA_INCLUDE_DIR.'qa-app-posts.php';

    if($_POST['pid']&&$_POST['categoryid']) //check for existence of post id and category id
    {
	$postid= $_POST['pid'];

	$toBePosted=qa_db_query_raw("SELECT * from qa_forupload where id=".$postid); //find post in forUpload database
	$postArray=qa_db_read_all_assoc($toBePosted);

	//prepare content to be used by the q2a post_create method
	$content = htmlspecialchars_decode($postArray[0]['content']);
	echo $content;
	$title = $postArray[0]['title'];
	$title = htmlspecialchars_decode($title,ENT_QUOTES);
	$tags=array('');
    $categoryid=$_POST['categoryid']; // assume no category
    $type='Q'; // question
    $parentid=null; // does not follow another answer
    $format='html';
    $userid=qa_get_logged_in_userid();

    //create post
    qa_post_create($type, $parentid, $title, $content, $format, $categoryid, $tags, $userid);
    //delete post from forupload database
    qa_db_query_raw("DELETE from qa_forupload where id=".$postid);

    echo "post successful";
	}
	else if($_POST['delete']) //delete post for forupload database
	{
		$postid = $_POST['delete'];
		qa_db_query_raw("DELETE from qa_forupload where id=".$postid);
	}

