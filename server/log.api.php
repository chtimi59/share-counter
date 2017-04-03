<?PHP
include '../header.php'; 
include 'PHPLog/log.php';
include 'PHPMailer/class.phpmailer.php';
include 'common.php';

$MAILSTRINGS = array();
$MAILSTRINGS['en'] = array();
$MAILSTRINGS['en']['TITLE'] = 'Share-Counter - new account request';
$MAILSTRINGS['en']['GREETING'] = 'Welcome! please click on the following link to valid your account.<br>';
$MAILSTRINGS['en']['FORGET_TITLE'] = 'Share-Counter - your personal information';
$MAILSTRINGS['en']['FORGET_USERNAME'] = 'Username: ';
$MAILSTRINGS['en']['FORGET_PASSWORD'] = 'Password: ';

$MAILSTRINGS['fr'] = array();
$MAILSTRINGS['fr']['TITLE'] = 'Share-Counter - nouveau compte';
$MAILSTRINGS['fr']['GREETING'] = 'Bienvenue! Cliquez sur le liens suivant pour valider votre compte.<br>';
$MAILSTRINGS['fr']['FORGET_TITLE'] = 'Share-Counter - vos informations personnelles';
$MAILSTRINGS['fr']['FORGET_USERNAME'] = 'Nom: ';
$MAILSTRINGS['fr']['FORGET_PASSWORD'] = 'Mot de passe: ';
                
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

$_DATA=array();
$_Action=null;

switch ($_SERVER['REQUEST_METHOD']) {
    case 'DELETE':
    case 'GET':
        if (isset ($_GET['data']))   $_DATA=json_decode($_GET['data'], TRUE);
        if (isset ($_GET['action'])) $_Action=$_GET['action'];
        break;        
    case 'PUT':
    case 'POST':
        $json = file_get_contents('php://input');
        $post_data = json_decode($json,true); // $post_data==NULL if not data
        if ($post_data!=NULL) { $_POST=$post_data; } // overwrite POST
        if (isset ($_POST['data']))   $_DATA=$_POST['data'];
        if (isset ($_POST['action'])) $_Action=$_POST['action'];
        break;  
}
if ($_Action==null) die_bad_request('invalid action');


switch($_Action)
{
    // ----------------------------------------------------------
    // Log an error in php_error
    // ----------------------------------------------------------
    // $http.post($scope.SrvUrl("log"),{'action':'error', 'data': obj });
    // TODO: need/used?
    /* 
       DATA='{"action":"error","data":{';\
       DATA=$DATA'  "pi":3.141516 '; \
       DATA=$DATA'}}';\
       curl -H "Content-Type: application/json" -X POST -d "$DATA" http://localhost/share-counter/server/log.api.php
    */
    case 'error':
        if ($_SERVER['REQUEST_METHOD']!='POST') die_bad_request('invalid method');
        $msg = "[".$_SERVER['REMOTE_ADDR']."] ";
        $msg .= json_encode($_DATA);
        error_log(substr($msg,0, 255));
        header('HTTP/1.1 200 OK', true, 200);
        break;


        
    // ----------------------------------------------------------
    // Send Forget email
    // ----------------------------------------------------------
    // $http.post($scope.SrvUrl("log"),{'action':'forget','data':{
    //      'lang':'fr',                        /* language for the email */
    //      'email':'someone@domain.com'        /* Email to use */
    //}})
    /* 
       DATA='{"action":"forget","data":{';\
       DATA=$DATA'  "lang":"fr"                   '; \
       DATA=$DATA'  "email":"someone@domain.com"  '; \
       DATA=$DATA'}}';\
       curl -H "Content-Type: application/json" -X POST -d "$DATA" http://localhost/share-counter/server/log.api.php
    */
    case 'forget':
        if ($_SERVER['REQUEST_METHOD']!='POST') die_bad_request('invalid method');
        
        /* 0- to avoid sql injection */
        if (isset($_DATA['email']))      { $_DATA['email']     =  mysql_real_escape_string($_DATA['email']); }
        if (isset($_DATA['lang']))       { $_DATA['lang']      =  mysql_real_escape_string($_DATA['lang']); }
                
        if ((!isset ($_DATA['email'])) || ($_DATA['email']=="")) {
            die_bad_request('invalid email');
            break;
        }
        if ((!isset ($_DATA['lang'])) || ($_DATA['lang']=="")) {
            die_bad_request('invalid lang');
            break;
        }
        if (!check_email_address($_DATA['email'])) {
            die_bad_request('invalid email', 'LOGIN_ERROR_UNKNOWMEMAIL');
            break;
        }
        
        // Is this email exist ?
        $sql = 'SELECT * FROM `'.LOG_USER_TABLE.'` WHERE EMAIL="'.$_DATA['email'].'"';
        $req = mysql_query($sql) or sqldie($sql);  
        $data = mysql_fetch_assoc($req);
        if (!$data) {
            die_bad_request('unknown email', 'LOGIN_ERROR_UNKNOWMEMAIL');
            break;
        }

        // OK, send mail with credential
        $str = $MAILSTRINGS[$_DATA['lang']];
        $message  = "<html><body>";
        $message .= $str['FORGET_USERNAME'].$data['NAME'].'<br>';
        $message .= $str['FORGET_PASSWORD'].$data['PASSWORD'].'<br>';
        $message .="</body></html>";
        if(!sendMail($_DATA['email'], $str['FORGET_TITLE'], $message)) {
            die_bad_request("couldn't send email");
        }
        header('HTTP/1.1 200 OK', true, 200);
        break;

        
    // ----------------------------------------------------------
    // ADD OR GET potential future USER from an "Antichambre"
    // ----------------------------------------------------------
    // $http.post($scope.SrvUrl("log"),{'action':'register','data':{
    //      'lang':'fr',                     /* language for the email */
    //      'email':'someone@domain.com'     /* Email to use */
    // }})
    //
    // $http.get($scope.SrvUrl("log"),{'action':'register','data':{
    //      'tmp_uuid':'xxxx-xx..'          /* Someone UUID in Antichambre */
    // }})   
    case 'register':
        switch ($_SERVER['REQUEST_METHOD'])
        {
           /* 
               DATA='{"tmp_uuid":"B3E992F7-AFFA-4AD2-A124-36FC8D4E0AC0"}'; \
               curl -X GET -G http://localhost/share-counter/server/log.api.php \
               -d action=register -d data="$DATA"
            */
            case 'GET':
                if ((!isset ($_DATA['tmp_uuid'])) || ($_DATA['tmp_uuid']=="")) {                   
                    die_bad_request('invalid uuid');
                    break;
                }            

                /* 0- to avoid sql injection */
                if (isset($_DATA['tmp_uuid']))      { $_DATA['tmp_uuid']      =  mysql_real_escape_string($_DATA['tmp_uuid']); }
                
                // Get temporary user
                $sql = 'SELECT * FROM `'.LOG_TMP_USER_TABLE.'` WHERE UUID="'.$_DATA['tmp_uuid'].'"';
                $req = mysql_query($sql) or sqldie($sql);  
                $data = mysql_fetch_assoc($req);
                if (!$data) {
                    die_bad_request('unknown tmp_uuid', "LOGIN_ERROR_PLZREGISTER");
                }
                
                header('HTTP/1.1 200 OK', true, 200);
                print json_encode($data);
                break;
        
            /* 
               DATA='{"action":"register","data":{';\
               DATA=$DATA'  "lang":"fr"                   '; \
               DATA=$DATA'  "email":"someone@domain.com"  '; \
               DATA=$DATA'}}';\
               curl -H "Content-Type: application/json" -X POST -d "$DATA" http://localhost/share-counter/server/log.api.php
            */
            case 'POST':
                if ((!isset ($_DATA['email'])) || ($_DATA['email']=="")) {
                    die_bad_request('invalid email');
                    break;
                }
                
                if ((!isset ($_DATA['lang'])) || ($_DATA['lang']=="")) {
                    die_bad_request('invalid lang');
                    break;
                }
                
                /* 0- to avoid sql injection */
                if (isset($_DATA['lang']))      { $_DATA['lang']      =  mysql_real_escape_string($_DATA['lang']); }
                if (isset($_DATA['email']))     { $_DATA['email']     =  mysql_real_escape_string($_DATA['email']); }
        
                /* Seems the best moment to do it */
                cleanUserAntiChambre();
                
                if (!check_email_address($_DATA['email'])) {
                    die_bad_request('invalid email', 'LOGIN_REGISTER_INVALIDEMAIL');
                    break;
                }
                
                // Already exist ?
                $sql = 'SELECT * FROM `'.LOG_USER_TABLE.'` WHERE EMAIL="'.$_DATA['email'].'"';
                $req = mysql_query($sql) or sqldie($sql);  
                $data = mysql_fetch_assoc($req);
                if ($data) {
                    die_bad_request('email already register', 'LOGIN_ERROR_EMAILEXIST');
                    break;
                }
                
                // Add to temporary user db
                $verifurl = "#";
                $sql = 'SELECT * FROM `'.LOG_TMP_USER_TABLE.'` WHERE EMAIL="'.$_DATA['email'].'"';
                $req = mysql_query($sql) or sqldie($sql);  
                $data = mysql_fetch_assoc($req);
                if (!$data) {
                    // save request
                    $uid = guid();
                    $sql = "INSERT INTO `".LOG_TMP_USER_TABLE."` (`UUID`, `EMAIL`) VALUES ('".$uid."', '".$_DATA['email']."');";
                    $req = mysql_query($sql) or sqldie($sql);  
                    $verifurl = CurrentPageBaseUrl()."index.php?action=".ACTION_LOGIN."&verif=".$uid;
                } else {
                    // already existing request
                    $verifurl = CurrentPageBaseUrl()."index.php?action=".ACTION_LOGIN."&verif=".$data['UUID'];
                }
                
                //send mail 
                $str = $MAILSTRINGS[$_DATA['lang']];
                $message  = "<html><body>";
                $message .= $str['GREETING'];
                $message .='<a href="'.$verifurl.'">'.$verifurl.'</a>';
                $message .="</body></html>";
                if(!sendMail($_DATA['email'], $str['TITLE'], $message)) {
                    die_bad_request("couldn't send email");
                }
                header('HTTP/1.1 200 OK', true, 200);
                break;
                            
            default:
                die_bad_request('invalid method');
                break;
        }
        break;
    

    // ----------------------------------------------------------
    // ADD new user (from AntiChambre -- see "register")
    // ----------------------------------------------------------
    // $http.post($scope.SrvUrl("log"),{'action':'add','data':{
    //      'tmp_uuid': "xxxx-xxx...",     /* An AntiChambre UUID */
    //      'lang':'fr',                   /* Default language for this new USER */
    //      'name':'bob',                  /* User name */
    //      'email':'t@t.com',             /* User email */
    //      'pw':'123'                     /* User password */
    //}})
    /* 
       DATA='{"action":"add","data":{';\
       DATA=$DATA'  "tmp_uuid":"6A512157-80A6-424C-8963-598E627966A4"  '; \
       DATA=$DATA'  "lang":"fr"        '; \
       DATA=$DATA'  "name":"bob"       '; \
       DATA=$DATA'  "email":"t@t.com"  '; \
       DATA=$DATA'  "pw":"123"         '; \
       DATA=$DATA'}}';\
       curl -H "Content-Type: application/json" -X POST -d "$DATA" http://localhost/share-counter/server/log.api.php
    */
    case 'add':
        if ($_SERVER['REQUEST_METHOD']!='POST') die_bad_request('invalid method');
        
        /* 0- to avoid sql injection */
        if (isset($_DATA['tmp_uuid']))  { $_DATA['tmp_uuid']  =  mysql_real_escape_string($_DATA['tmp_uuid']); }
        if (isset($_DATA['lang']))      { $_DATA['lang']      =  mysql_real_escape_string($_DATA['lang']); }
        if (isset($_DATA['name']))      { $_DATA['name']      =  mysql_real_escape_string($_DATA['name']); }
        if (isset($_DATA['pw']))        { $_DATA['pw']        =  mysql_real_escape_string($_DATA['pw']); }
        if (isset($_DATA['email']))     { $_DATA['email']     =  mysql_real_escape_string($_DATA['email']); }

        if ((!isset ($_DATA['tmp_uuid'])) || ($_DATA['tmp_uuid']=="")) {
            die_bad_request('invalid uuid');
            break;
        }
        if ((!isset ($_DATA['lang'])) || ($_DATA['lang']=="")) {
            die_bad_request('invalid lang');
            break;
        }
        if ((!isset ($_DATA['name'])) || ($_DATA['name']=="")) {
            die_bad_request('invalid name');
            break;
        }
        if ((!isset ($_DATA['pw'])) || ($_DATA['pw']=="")) {
            die_bad_request('invalid pw');
            break;
        }
        if ((!isset ($_DATA['email'])) || ($_DATA['email']=="")) {
            die_bad_request('invalid email');
            break;
        }
        if (($err = check_user_account_setting($_DATA))) {
            die_bad_request($err, $err);
            break;
        }
        
        // Add New USER!
        $uuid = guid();
        $sql = "INSERT INTO `".LOG_USER_TABLE."` (`UUID`, `NAME`, `LANG`, `PASSWORD`, `EMAIL`, `CREATION`) VALUES ("
                ."'".$uuid."',"
                ."'".$_DATA['name']."',"
                ."'".$_DATA['lang']."',"                        
                ."'".$_DATA['pw']."',"
                ."'".$_DATA['email']."',"
                ."now());";            
        $req = mysql_query($sql) or sqldie($sql);
        
        // Remove temporary user
        $sql = 'DELETE FROM `'.LOG_TMP_USER_TABLE.'` WHERE UUID="'.$_DATA['tmp_uuid'].'"';
        $req = mysql_query($sql) or sqldie($sql);  
        header('HTTP/1.1 200 OK', true, 200);
        break;    
              

        
    // ----------------------------------------------------------
    // UPDATE current $USER (should be logged)
    // ----------------------------------------------------------
    // $http.post($scope.SrvUrl("log"),{'action':'add','data':{
    //      'lang':'fr',                   /* [optional] Default language for this new USER */
    //      'name':'bob',                  /* [optional] User name */
    //      'email':'t@t.com',             /* [optional] User email */
    //      'pw':'123'                     /* [optional] User password */
    //}})      
    /* 
       DATA='{"action":"update","data":{';\
       DATA=$DATA'  "lang":"fr"        '; \
       DATA=$DATA'  "name":"bob"       '; \
       DATA=$DATA'  "email":"t@t.com"  '; \
       DATA=$DATA'  "pw":"123"         '; \
       DATA=$DATA'}}';\
       curl -H "Content-Type: application/json" -X POST -d "$DATA" http://localhost/share-counter/server/log.api.php
    */
    case 'update':   
        if ($_SERVER['REQUEST_METHOD']!='POST') die_bad_request('invalid method');            
        if (!isset ($USER)) {
            die_forbidden();
            break;
        }
        if (count($_DATA)==0) { // nothing to update
            header('HTTP/1.1 200 OK', true, 200);
            break;
        }
        
        /* 0- to avoid sql injection */
        if (isset($_DATA['lang']))   { $_DATA['lang']   =  mysql_real_escape_string($_DATA['lang']); }
        if (isset($_DATA['name']))   { $_DATA['name']   =  mysql_real_escape_string($_DATA['name']); }
        if (isset($_DATA['pw']))     { $_DATA['pw']     =  mysql_real_escape_string($_DATA['pw']); }
        if (isset($_DATA['email']))  { $_DATA['email']  =  mysql_real_escape_string($_DATA['email']); }
            
        if (($err = check_user_account_setting($_DATA,$USER))) {
            die_bad_request($err, $err);
            break;
        }
        
        $sql = "UPDATE `".LOG_USER_TABLE."` SET ";
        $coma = "";
        if ((isset ($_DATA['lang'])) && ($_DATA['lang']!="")) {
            $sql .= $coma." `LANG` =  '".$_DATA['lang']."'";
            $coma = ",";
        }
        if ((isset ($_DATA['name'])) && ($_DATA['name']!="")) {
            $sql .= $coma." `NAME` =  '".$_DATA['name']."'";
            $coma = ",";
        }
        if ((isset ($_DATA['pw'])) && ($_DATA['pw']!="")) {
            $sql .= $coma." `PASSWORD` =  '".$_DATA['pw']."'";
            $coma = ",";
        }
        if ((isset ($_DATA['email'])) && ($_DATA['email']!="")) {
            $sql .= $coma." `EMAIL` =  '".$_DATA['email']."'";
            $coma = ",";
        }
        $sql .= " WHERE `UUID` =  '".$USER['UUID']."'";
        $req = mysql_query($sql) or sqldie($sql);                      
        header('HTTP/1.1 200 OK', true, 200);
        break;
        
    
    // ----------------------------------------------------------
    // LOG IN
    // ----------------------------------------------------------
    // $http.post($scope.SrvUrl("log"),{'action':'login','data':{
    //      'name':'bob',                  /* User name */
    //      'pw':'123'                     /* User password */
    //}}) 
    /* 
       DATA='{"action":"login","data":{';\
       DATA=$DATA'  "name":"bob"       '; \
       DATA=$DATA'  "pw":"123"         '; \
       DATA=$DATA'}}';\
       curl -H "Content-Type: application/json" -X POST -d "$DATA" http://localhost/share-counter/server/log.api.php
    */    
    case 'login':
        if ($_SERVER['REQUEST_METHOD']!='POST') die_bad_request('invalid method');
        
        if ((!isset ($_DATA['name'])) || ($_DATA['name']=="")) {
            die_bad_request('login: no name');
            break;
        }
        if ((!isset ($_DATA['pw'])) || ($_DATA['pw']=="")) {
            die_bad_request('login: no pw');
            break;
        }
        
        /* 0- to avoid sql injection */
        $_DATA['name'] =  mysql_real_escape_string($_DATA['name']);
        $_DATA['pw'] =  mysql_real_escape_string($_DATA['pw']);

        $sql = 'SELECT * FROM `'.LOG_USER_TABLE.'` WHERE NAME="'.$_DATA['name'].'"';
        $req = mysql_query($sql) or sqldie($sql);  
        $data = mysql_fetch_assoc($req);
        
        if (!$data) {
            die_forbidden();
        }
        if ($data['PASSWORD']!=$_DATA['pw']) {
            die_forbidden();
        }
        
        if (logIn($data['UUID'])) {
            header('HTTP/1.1 200 OK', true, 200);
        } else {
            die_internal_error('login: *');
        }
        break;
         
         
    // ----------------------------------------------------------
    // LOG OUT
    // ----------------------------------------------------------
    // $http.post($scope.SrvUrl("log"),{'action':'logout'}) 
    /* 
       curl -H "Content-Type: application/json" -X POST -d '{"action":"logout"}' http://localhost/share-counter/server/log.api.php
    */        
    case 'logout':
        logOff();
        header('HTTP/1.1 200 OK', true, 200);
        break;
            

                
    default:
        die_bad_request('invalid action');
        break;        
}
    
include '../footer.php'; 


// --- LOG HELPER ---

function check_user_account_setting($ARRAY, $ORIGNAL=null) {

    if (isset($ARRAY['name']))
    {
        if ((strlen($ARRAY['name'])<1) || (strlen($ARRAY['name'])>20)) {
            return "LOGIN_ERROR_INVALIDNAME";
        }

        if (strpos($ARRAY['name'],"'")) 
            return "LOGIN_ERROR_INVALIDCHARACTER";
        
        $sql = 'SELECT * FROM `'.LOG_USER_TABLE.'` WHERE NAME="'.$ARRAY['name'].'"';
        $req = mysql_query($sql) or sqldie($sql);  
        $data = mysql_fetch_assoc($req);
        if ($data) {
            if ($ORIGNAL!=null) 
            {
                if ($ORIGNAL['NAME']!=$data['NAME']) {
                    return "LOGIN_ERROR_NAMEEXIST";
                }
            } else {
                return "LOGIN_ERROR_NAMEEXIST";
            }
        }
    }
    
    if (isset($ARRAY['email']))
    {        
        if (strpos($ARRAY['email'],"'")) 
            return "LOGIN_ERROR_INVALIDCHARACTER";
            
        if (!check_email_address($ARRAY['email'])) {
            return "LOGIN_ERROR_INVALIDEMAIL";
        }
        
        $sql = 'SELECT * FROM `'.LOG_USER_TABLE.'` WHERE EMAIL="'.$ARRAY['email'].'"';
        $req = mysql_query($sql) or sqldie($sql);  
        $data = mysql_fetch_assoc($req);
        if ($data) {
            if ($ORIGNAL!=null) 
            {
                if ($ORIGNAL['EMAIL']!=$data['EMAIL']) {
                    return "LOGIN_ERROR_EMAILEXIST";
                }
            } else {
                return "LOGIN_ERROR_EMAILEXIST";
            }
        }
    }
    
    if (isset($ARRAY['pw']))
    {       
        if (strpos($ARRAY['pw'],"'")) 
            return "LOGIN_ERROR_INVALIDPASSWORD";
            
        if ((strlen($ARRAY['pw'])<1) || (strlen($ARRAY['pw'])>32)) {
            return "LOGIN_ERROR_INVALIDPASSWORD";
        }        
    }

    return NULL;
}

