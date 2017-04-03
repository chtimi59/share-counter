<?PHP

// SESSION CREATION
session_start();
if (isset($_SESSION['clientUpdate']))
{
	/* session already created, but timeout ? */
	if ((time()-$_SESSION['clientUpdate'])> 60*SESSION_TIMEOUT_MINUTES)
	{
		if (isset($_SESSION['authkey']))  unset($_SESSION['authkey']);
		if (isset($_SESSION['clientIP'])) unset($_SESSION['clientIP']);
		if (isset($_SESSION['clientUpdate'])) unset($_SESSION['clientUpdate']);
		session_destroy();
	}
}
$_SESSION['clientIP'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['clientUpdate'] = time(); 

/* Global USER object */
$USER = NULL;

/* re-check user identity on each load */
logIn(NULL);



// ------------- log methods ----------

/* LOGIN */
function logIn($key) {
    $authkey = NULL;
    if (isset($_SESSION['authkey']))
        $authkey = $_SESSION['authkey'];
    if (isset($_GET['authkey'])) 
        $authkey = $_GET['authkey'];
    if ($key)
        $authkey = $key;
	
    if ($authkey) {		
        $sql = 'SELECT * FROM `'.LOG_USER_TABLE.'` WHERE UUID="'.$authkey.'"';
        $req = mysql_query($sql) or sqldie($sql);
        $data = mysql_fetch_assoc($req);
        if (!$data) {
            logOff();
			return false;
		}
		
		/* update connection date */
		$sql = 'UPDATE `'.LOG_USER_TABLE.'` SET `LAST_CONNECTION`=now() WHERE UUID="'.$authkey.'"';
		$req = mysql_query($sql) or sqldie($sql);
		
        $_SESSION['authkey'] = $data['UUID'];
        $GLOBALS['USER'] = $data;		
    } else {
        logOff();
		return false;
    }
	return true;
}

/* LOGOFF */
function logOff() {
    $GLOBALS['USER'] = NULL;
    if (isset($_SESSION['authkey'])) unset($_SESSION['authkey']);
}

/* CHECK IF WERE ARE ADMIN */
function IsAdmin() {
	if ($GLOBALS['USER']==NULL) return false;
	return ($GLOBALS['USER']['UUID'] == ADMIN_UUID);
}

?>
