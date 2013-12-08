<?php
	require_once 'qa-include/qa-base.php';
	require_once 'qa-include/qa-app-users.php';
	
	$inuserid = qa_handle_to_userid($_POST['ext_sakai_eid']);

	qa_set_logged_in_user($inuserid, true);
	qa_redirect('');