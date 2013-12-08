<?php

class qa_bulk_upload_page {

    var $directory;
    var $urltoroot;
    
    function load_module($directory, $urltoroot)
    {
            $this->directory=$directory;
            $this->urltoroot=$urltoroot;
    }
    
    // for display in admin interface under admin/pages
    function suggest_requests()
    {        
            return array(
                    array(
                        'title' => 'Bulk Upload', // title of page
                        'request' => '?qa=bulk', // request name
                        'nav' => 'M', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
                    ),
            );
    }
    
    // for url query
    function match_request($request)
    {
            if ($request=='bulk') {
                    return true;
            }

            return false;
    }
	
	function process_request($request)
	{

        $qa_content=qa_content_prepare();
        $qa_content['title'] = "Bulk Upload"; // Page Title

        if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
        header('Location: ../');
        exit;
        } 

        require_once QA_INCLUDE_DIR.'qa-app-admin.php';

        if (!qa_admin_check_privileges($qa_content)) //make sure user is an admin to access page
        return $qa_content;
        
        //////////////////////////////////////////Begin Bulk Upload Processing
        if (qa_clicked('bulk')) {
            
            if (is_array(@$_FILES['file']) && $_FILES['file']['size']) { //check for file existence
                require_once QA_INCLUDE_DIR.'qa-app-limits.php';
                require_once QA_INCLUDE_DIR.'qa-app-users.php';   //require applicable q2a files
                require_once QA_INCLUDE_DIR.'qa-app-posts.php';
                
                $myFile= file_get_contents($_FILES['file']['tmp_name']); //get contents of file

                $questions = simplexml_load_string($myFile); //load file string into simplexml

                if($questions->question) //if questions properly formatted
                {
                    foreach ($questions->question as $key=> $q){ //prepare each uploaded question for db insertion
                        
                        $title= $q->title;
                        $content=$q->content;
                        $content=htmlspecialchars($content,ENT_QUOTES);
                        $title=htmlspecialchars($title,ENT_QUOTES);
                        qa_db_query_raw("INSERT INTO qa_forupload (content,title,uploadDate) VALUES ('".$content."','".$title."',CURRENT_TIMESTAMP)");
                        //insert into temp forUpload db 
                    }
                    qa_redirect('bulk', array('state' => count($questions->question).'xmlGood')); //redirect with good state
                    break;
                }
                else
                    qa_redirect('bulk', array('state' => 'xmlBad')); //redirect with xmlBad error state
                
            }
            else
            {
                qa_redirect('bulk', array('state' => 'noFile')); //redirect with noFile error state
            }
            
        }

        if (qa_get_state()=='noFile') //handle noFile error state
            $qa_content['error']="No file submitted.";

        if (qa_get_state()=='xmlBad') //handle xmlBad error state
            $qa_content['error']="Improperly formatted or non-XML file submitted";

        if(strpos(qa_get_state(),"xmlGood")) //handle xmlGood state
        {
            $state=qa_get_state();
            $loc=strpos($state,"x");
            $numfiles=substr($state,0,$loc);
            $qa_content['form_content']['ok']="Successfully uploaded ".$numfiles." question(s) from file.";
        }

        $form = array( //prepare bulk upload form to be handled by q2a HTML generation (convenient for file handling)
        'style' => 'tall', // or 'wide'
        'title' => 'Bulk Upload Form', 
        'tags' => 'NAME="myform" METHOD=POST ENCTYPE="multipart/form-data"',
        'fields' => array(
        array(
                'type' => 'static',
                'value' => '<INPUT TYPE="FILE" NAME="file">',
            ),
        ),
        'buttons' => array(
                    'ok' => array(
                        'tags' => 'onclick="qa_show_waiting_after(this, false);" NAME="submitButton",action="'.qa_self_html().'"',
                        'label' => 'Submit',
                        'value' => '1',
                    ),
                ),
        'hidden'=>array(
            'bulk' => '1'
            ),
        );

        
        //create category select options to be used throughout page
        $categories = qa_db_query_raw("SELECT categoryid,title from qa_categories");
        $catOptions="";
        $categories = qa_db_read_all_assoc($categories);
        foreach ($categories as $category){
            $catOptions.="<option id='c".$category['categoryid']."'>".$category['title']."</option>";
        }

        $qa_content['form']=$form;
        //////////////////////////////////////////Begin Bulk Download HTML
        $qa_content['custom'].="<script src='scripts/bulkDownload.js'></script>";
        $qa_content['custom'].="<button class='cus bulkDownload qa-form-tall-button'>Back Up Current Questions</button>";
        $qa_content['custom'].="<span>By Category:</span>";
        $qa_content['custom'].="<input type='checkbox' id='catCheck'>";
        $qa_content['custom'].="<select id='bCat' name='category'>";
        $qa_content['custom'].=$catOptions;
        $qa_content['custom'].="</select>";
        $qa_content['custom'].="<div id='backUpStatus' class='postSuccess'></div>";
        //////////////////////////////////////////Begin Post Checked HTML
        $qa_content['custom'].="<script src='scripts/postChecked.js'></script>";
        $qa_content['custom'].="<button class='cus postChecked qa-form-tall-button'>Post Checked Question</button>";
        $qa_content['custom'].="<select id='manyCategory' name='category'>";
        $qa_content['custom'].=$catOptions;
        $qa_content['custom'].="<option id='other'>Other</option>";
        $qa_content['custom'].="</select>";
        $qa_content['custom'].="<div id='otherCatInput'><span id='catFail'></span></div>";
        //////////////////////////////////////////Begin Display of Questions 

        $questions = qa_db_query_raw("SELECT * from qa_forupload"); //db select of uploaded questions
        $questions = qa_db_read_all_assoc($questions);
        $qa_content['custom'].="<script src='scripts/regex.js'></script>";
        $qa_content['custom'].="<script src='scripts/postQuestion.js'></script>";
        $qa_content['custom'].="<link href='scripts/buss.css' rel='stylesheet' />";        
        
        $qa_content['custom'].="<button id='checkAll' class='cus'>Check All</button>";
        $qa_content['custom'].="<button id='uncheckAll' class='cus' hidden='true'>Uncheck All</button>";
        
        $qa_content['custom'].="<table>";

        if (count($questions)) {
        foreach ($questions as $key => $question) { //question HTML preparation
            
            $qa_content['custom'].="<tr>";
            $qa_content['custom'].="<td id=cb".$question['id']." valign='top'>";
            $qa_content['custom'].="<input type='checkbox' class='postMany' value='".$question['id']."'>";
            $qa_content['custom'].="</td>";
            $qa_content['custom'].="<td>";
            $qa_content['custom'].="<div id=p".$question['id'].">";
            $qa_content['custom'].="<p class='qa_bu_title'>".$question['title']."</p>";
            $qa_content['custom'].="<p class='qa_bu_content'>".$question['content']."</p>";
            $qa_content['custom'].="<p class='qa_bu_date'>Uploaded on ".$question['uploadDate']."</p>";
            $qa_content['custom'].="<form id=postid".$question['id'].">";
            $qa_content['custom'].="<input type='hidden' class='pid' name='id' value=".$question['id'].">";
            $qa_content['custom'].="<button class='postButton qa-form-tall-buttons pB'>Post Question</button>";
            $qa_content['custom'].="<select class='category' name='category'>";
            $qa_content['custom'].=$catOptions;
            $qa_content['custom'].="</select>";
            $qa_content['custom'].="</form>";
            $qa_content['custom'].="<button class='deleteButton qa-form-tall-buttons'>Delete Question</button>";
            $qa_content['custom'].="</div>";
            $qa_content['custom'].="</td>";
            $qa_content['custom'].="</tr>";
        }
        $qa_content['custom'].="</table>";

    }
        return $qa_content; //return content for q2a HTML generating code
	}
	
}