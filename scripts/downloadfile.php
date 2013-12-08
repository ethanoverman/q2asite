<?php
    
    //properly set headers and read file generating file-download dialogue

	$backupFile=realpath('Q2A_BACKUP_FILE.xml');

	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($backupFile));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($backupFile));
    ob_clean();
    flush();
    readfile($backupFile);
    exit;