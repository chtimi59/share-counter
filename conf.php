<?PHP	
	$systemRoot = $_SERVER['DOCUMENT_ROOT'];
	
	// -------- SHOULD BE CONFIGURED ACCORDING YOUR NEEDS ----------------
	// -------------------------------------------------------------------
	
	/* Configuration file secret Hash */
	$secretHash = '1289rtpf%7+9';
	
	/* Configuration file path */
	$confPath = '';
	if(strncmp(PHP_OS, 'WIN', 3) === 0) {
		
		/* On Windows */
		$confPath = 'c:/dev/share-counter.conf';
		
	} else {
		
		/* On UNIX */
		//$confPath = '/var/share-counter.conf';
		
		/* My Own OVH set */
		$confPath = '/homez.767/jdodev/var/share-counter.conf';		
	}
	// -------------------------------------------------------------------
	// -------------------------------------------------------------------
	
	/* read conf file */
	{
		$encryptedMessage = file_get_contents ($confPath);	
		if (!$encryptedMessage) die("Invalid setup");
		$iv = substr($encryptedMessage, 0, 16);
		$encryptedMessage = substr($encryptedMessage, 16);
		$encryptionMethod = 'aes128';
		$string = openssl_decrypt($encryptedMessage, $encryptionMethod, $secretHash, 0, $iv);
		$GLOBALS['CONFIG'] = json_decode($string, true);
	}

?>