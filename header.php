<?PHP
// WARNING: NO TEXT SHOULD BE WRITE HERE (EXCEPT DIE ERRORS)
// BE CAREFULL THAT THEY ALSO NO TEXT (EVEN SPACE) OUTSIDE PHP BALISES
// -> cause this file is used under download.php
include 'conf.php';
include 'defines.php';


// DEBUG SWITCHED
// ---------------------
if (($_SERVER['SERVER_ADDR']=='localhost' ) || $_SERVER["SERVER_ADDR"]=="127.0.0.1" || ($_SERVER["SERVER_ADDR"]=="::1")) {
    $GLOBALS['DEBUG'] = true;
} else {
    $GLOBALS['DEBUG'] = false;
}
    
// MAIN DATABASE ($db)
// -----------------
if ($GLOBALS['CONFIG']['sql_isPW']) {
    $db = mysql_connect($GLOBALS['CONFIG']['sql_Host'], $GLOBALS['CONFIG']['sql_login'], $GLOBALS['CONFIG']['sql_pw']); 
} else {
    $db = mysql_connect($GLOBALS['CONFIG']['sql_Host'], $GLOBALS['CONFIG']['sql_login']); 
}
if ($GLOBALS['DEBUG']) {
    if (!$db)  die('Could not connect: ' . mysql_error());
    if(!mysql_select_db($GLOBALS['CONFIG']['main_db'],$db)) die('Could not connect db: ' . mysql_error());
} else {
    if (!$db) die();
    if(!mysql_select_db($GLOBALS['CONFIG']['main_db'],$db)) die();
}
date_default_timezone_set('America/Montreal');



// ACTION ($action)
// -----------------
$action = ACTION_NONE;
if (isset ($_GET['action'])) $action = $_GET['action'];

// Gnrl print error
function perr($msg) {
    return '<span class="error">'.$msg.'</span><br>';
}


/* dying.... */

function sqldie($sql) {
    $bt = debug_backtrace();
    $caller = array_shift($bt);
    $dbg =  "[".$_SERVER['REMOTE_ADDR']."] ";
    $dbg .= "[".$caller['file'].":".$caller['line']."] ";
	$err = mysql_error();
    error_log($dbg);
    error_log($sql);
    error_log($err);
    
    header('HTTP/1.1 500 Internal error', true, 500);
    
	if ($GLOBALS['DEBUG']) {
		die("SQL: $err\n$sql"); 
	} else {
		die('<p>Server internal error</p>Sorry for the inconvenience!<br><a href="index.php">click here</a> to return to main menu'); 
	}	
}

function die_internal_error($msg="") {
    $bt = debug_backtrace();
    $caller = array_shift($bt);
    $dbg =  "[".$_SERVER['REMOTE_ADDR']."] ";
    $dbg .= "[".$caller['file'].":".$caller['line']."] ";
    $dbg .= $msg;
    error_log($dbg);
    
    header('HTTP/1.1 500 Internal error', true, 500);
    exit($msg);
}

function die_bad_request($msg="", $errorIdx=null) {
    $bt = debug_backtrace();
    $caller = array_shift($bt);
    $dbg =  "[".$_SERVER['REMOTE_ADDR']."] ";
    $dbg .= "[".$caller['file'].":".$caller['line']."] ";
    $dbg .= $msg;
    error_log($dbg);
    
    header('HTTP/1.1 400 Bad Request', true, 400);    
    exit('{"msg":"'.$msg.'", "idx":"'.$errorIdx.'"}');
}    

function die_forbidden($msg="") {    
    header('HTTP/1.1 403 Forbidden', true, 403);
    exit($msg);
}

function die_redirect($url) {
    header('Location: '.$url);    
    exit();
}

function die_redirect_pre() {
    if (isset($_GET['pre'])) die_redirect($_GET['pre']); 
    die_redirect('index.php'); 
    exit();
}



/* url */

/** PreArg()
* Gives 'pre' Argument for url forwarding
* return 'pre=currentPageUrl_WithoutPreArg'
*/
function PreArg() {
    
    $arg = false;
    $pageURL = CurrentPageBaseUrl();
    $pageURL .= "index.php";
    foreach ($_GET as $key => $value){
        if ($key=='pre') continue;
        $pageURL .= ($arg)?"&":"?";
        $arg = true;
        $pageURL .= $key;
        if (!empty($value)) $pageURL .= '='.urlencode($value);
    }
    return 'pre='.urlencode($pageURL);
}

/** CurrentPageBaseUrl()
* return "http://localhost/share-counter/"
*/
function CurrentPageBaseUrl() {
    /* TODO improve that */
    $pageURL = ((isset($_SERVER['HTTPS'])) && ($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://';
    $pageURL .= $_SERVER['SERVER_PORT'] != '80' ? $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"] : $_SERVER['SERVER_NAME'];
    return "$pageURL/share-counter/";
}

/*
Somes try...

function CurrentPageURL() {
    $pageURL = ((isset($_SERVER['HTTPS'])) && ($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://';
    $pageURL .= $_SERVER['SERVER_PORT'] != '80' ? $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"] : $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    return $pageURL;
}

function CurrentPageBaseUrl() {
    $pageURL =CurrentPageURL();
    return preg_replace('/([^\/]*)$/', '', $pageURL);
}*/
?>