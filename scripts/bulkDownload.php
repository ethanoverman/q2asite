<?php
    define('__ROOT__', dirname(dirname(__FILE__)));
    

    require_once(__ROOT__.'/qa-include/qa-base.php'); //require appropriate q2a php scripts
    require_once QA_INCLUDE_DIR.'qa-app-users.php';

    $userid=qa_get_logged_in_userid(); //get logged in user (admin)

    $catTF = $_POST['byCat'];

    if($catTF=="true") //handle 'by-category' backup
    {
        $cat = $_POST['cID'];
        $questions=qa_db_query_raw("SELECT title, content from qa_posts where type='Q' and userid=".$userid." and categoryid=".$cat);
    }
    else //handle full backup
    {
        $questions=qa_db_query_raw("SELECT title, content from qa_posts where type='Q' and userid=".$userid);
    } 
    
    $questions=qa_db_read_all_assoc($questions);
    
    $newDoc = new DOMDocument;
    $backupFile=realpath('Q2A_BACKUP_FILE.xml'); //file to be written to


    if($catTF=="true") //create XML comment node detailing category backed up with date
    {
        $comment = $newDoc->createComment("This is a backup of category: ".$_POST['catName'] . " from ". date(DATE_RFC2822));
        $comment = $newDoc->appendChild($comment);
    }
    else //create XML comment node detailing full back up with date
    {
        $comment = $newDoc->createComment("This is a full question backup from ". date(DATE_RFC2822));
        $comment = $newDoc->appendChild($comment);
    }

    $root = $newDoc->createElement('questions'); //create root xml element questions
    $root = $newDoc->appendChild($root); 

    foreach($questions as $q) //generate xml for each question to be backed up
    {
        
        $contentText =$q['content'];
        $titleText = $q['title'];

        $question = $newDoc->createElement('question'); //create question element and append to root
        $question = $root->appendChild($question);

        $title = $newDoc->createElement('title'); //create title element and append to question
        $title = $question->appendChild($title);

        $content = $newDoc->createElement('content'); //create content element and appand to question
        $content = $question->appendChild($content);

        $text = $newDoc->createTextNode($titleText); //create text node for title and append to title
        $text = $title->appendChild($text);


        $text2 = $newDoc->createCDATASection($contentText); //create cdata section for content and append to content
        $text2 = $content->appendChild($text2);

    }

    $newDoc ->save($backupFile); //save backup file to be downloaded
?>