<?php

class roster_maintenance {

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
                        'title' => 'Roster Maintenance', // title of page
                        'request' => '?qa=rostermaintenance', // request name
                        'nav' => 'M', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
                    ),
            );
    }
    
    // for url query
    function match_request($request)
    {
            if ($request=='rostermaintenance') {
                    return true;
            }

            return false;
    }
	
	function process_request($request)
	{
		$qa_content=qa_content_prepare();
        $qa_content['title'] = "Roster Maintenance"; // Page Title

        if (qa_clicked('bulk')) {
        	error_reporting(E_ALL ^ E_NOTICE);
			require_once 'excel_reader2.php';
			require_once 'qa-include/qa-app-users-edit.php';
			require_once 'qa-include/qa-db.php';

			$data = new Spreadsheet_Excel_Reader($_FILES['file']['tmp_name'],false);

			$student_count = $data->rowcount($sheet_index=0)+1;
			echo "Rows parsed: " . $student_count;
			
			for ($i=2; $i<$student_count; $i++){
				$id = $data->val($i,2);
				$role = $data->val($i,3);
				$email = "$id@live.unc.edu";
				$password = 1234;
				
				$check = true;
				
				$users = qa_db_query_raw('SELECT * FROM qa_users');
				while ($row = mysql_fetch_assoc($users)){
					if($id == $row['handle']){
						$check = false;
					}
				}
				
				if($check){
					qa_create_new_user($email, $password, $id);
					if($role != 'Student'){
						qa_set_user_level(qa_handle_to_userid($id), $id, 100, 0);
					}
				}
			}
			
			$users = qa_db_query_raw('SELECT * FROM qa_users');
			while ($row = mysql_fetch_assoc($users)){
				$check = false;
				for ($i=2; $i<$student_count; $i++){
					$id = $data->val($i,2);
					if($id == $row['handle']){
						$check = true;
					}
				}
				if($check == false){
					if($row['level'] < 120){
						$userid = qa_handle_to_userid($row['handle']);
						qa_delete_user($userid);
					}
				}
			}
		}

        
        if (qa_get_state()=='noFile')
            $qa_content['error']="No file submitted.";

        if (qa_get_state()=='xmlBad')
            $qa_content['error']="Improperly formatted or non-XML file submitted";

        if(strpos(qa_get_state(),"xmlGood"))
        {
            $state=qa_get_state();
            $loc=strpos($state,"x");
            $numfiles=substr($state,0,$loc);
            $qa_content['form_content']['ok']="Successfully uploaded ".$numfiles." file(s).";
        }

        $form = array(
        'style' => 'tall', // or 'wide'
        'title' => 'Submit Roster', // <h2>My File Form</h2>
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

        $qa_content['form']=$form;

        return $qa_content;
	}
}