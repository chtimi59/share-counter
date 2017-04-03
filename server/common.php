<?PHP

 
/* Free record in a counter */
function freeingRecords($cuid, $nb_place_needed) {

	/* 1- grap info from table description */
	$sql = "SELECT * FROM `".COUNTERLIST_TABLE."` WHERE `CUID`='".$cuid."'";
	$req = mysql_query($sql) or sqldie($sql);  
	$table = mysql_fetch_assoc($req);
	if ($table==NULL) 
		return false;
	
	/* 2- unlimited ? */
	if ($table['MAX_RECORD']==0) return; 
	
	/* 3- Delete too old records (if neeed) */
	$count = tableCount($cuid)+$nb_place_needed;
	$need_to_delete = ($count<$table['MAX_RECORD'])?0:($count-$table['MAX_RECORD']);
	if ($need_to_delete>0) {
		$sql = "SELECT `ID` FROM `$cuid`  ORDER BY ID ASC LIMIT $need_to_delete";
		$req = mysql_query($sql) or sqldie($sql);
		$list = "("; $coma="";
		while ($data = mysql_fetch_assoc($req)){ $list .= $coma.$data['ID'];$coma=","; }
		$list .= ")";
		$sql = "DELETE FROM `$cuid` WHERE `ID` in ".$list;
		$req = mysql_query($sql) or sqldie($sql);  
	}

}

/* Counter number of element in a table */
function tableCount($table, $where="1") {
	$sql = 'SELECT COUNT(*) FROM `'.$table.'` WHERE '.$where;
	$req = mysql_query($sql);
	if ($req==NULL) return 0;  
	$tmp = mysql_fetch_assoc($req);
	return ($tmp)?$tmp['COUNT(*)']:0;	
}

/* Create a counter */
function helper_create_counter($USER, $data) {

	/* Check parameters */
	if ( (!isset ($data['MAX_RECORD'])) ||
		 (!isset ($data['NAME'])) ||
		 (!isset ($data['WRITE_KEY'])) ||
		 (!isset ($data['READ_KEY']))
		)
		return false;
        
    /* 0- to avoid sql injection */
    if (isset($data['MAX_RECORD']))   { $data['MAX_RECORD']  =  (int)($data['MAX_RECORD']); }
    if (isset($data['NAME']))         { $data['NAME']        =  mysql_real_escape_string($data['NAME']); }
    if (isset($data['WRITE_KEY']))    { $data['WRITE_KEY']   =  mysql_real_escape_string($data['WRITE_KEY']); }
    if (isset($data['READ_KEY']))     { $data['READ_KEY']    =  mysql_real_escape_string($data['READ_KEY']); }
    
    
	if ($data['NAME']=="") return false;
	if (($data['MAX_RECORD']>$USER['MAX_RECORD']) && $USER['MAX_RECORD']!=0) return false;
	$num_rows = tableCount(COUNTERLIST_TABLE, 'UUID="'.$USER['UUID'].'"');
	if ($num_rows>=$USER['MAX_COUNTER'] && $USER['MAX_COUNTER']!=0) return false;

	/* Insert counter entry */
	$sql = "INSERT INTO `".COUNTERLIST_TABLE."` (`CUID`, `MAX_RECORD`, `LAST_UPDATE`, `UUID`, `NAME`, `WRITE_KEY`, `READ_KEY`) VALUES ("
		."'".$data['CUID']."',"
		."'".$data['MAX_RECORD']."',"
		."now(),"
		."'".$USER['UUID']."',"
		."'".$data['NAME']."',"
		."'".$data['WRITE_KEY']."',"
		."'".$data['READ_KEY']."'"
		.");";				
	$req = mysql_query($sql) or sqldie($sql);  

    /* Create counter table */
	$sql = "CREATE TABLE `".$data['CUID']."` (";
	$sql .= "    `id`     int(11) NOT NULL AUTO_INCREMENT,";
	$sql .= "    `date`   timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,"; /* auto | 1442124574 */
	$sql .= "    `lat`    double DEFAULT NULL,";      /* latitude:180.5292840 */
	$sql .= "    `lng`    double DEFAULT NULL,";      /* longitude:-90.6240360 */
	$sql .= "    `alt`    double DEFAULT NULL,";      /* Altitude":52.024 */
	$sql .= "    `author` nvarchar(40) DEFAULT NULL,"; /* B3E992F7-AFFA-4AD2-A124-36FC8D4E0AC0 | someone@somewhere.com */
    $sql .= "    `text`   nvarchar(255) DEFAULT NULL,"; /* text */
	$sql .= "    `value`  double DEFAULT NULL,";      /* double */
	$sql .= "    PRIMARY KEY (`ID`)";
	$sql .= ")";
	$req = mysql_query($sql) or sqldie($sql);  
	if (!$req) {
		$sql2 = 'DELETE FROM `'.COUNTERLIST_TABLE.'` WHERE WRITE_KEY="'.$data['WRITE_KEY'].'"';
		mysql_query($sql2) or sqldie($sql2);  
		sqldie($sql);
	}
	
	return true;
}

/* Push new record in a counter */
function pushNewRecord($cuid, $data)
{	
    /* 0- to avoid sql injection */
    if ($cuid) { $cuid =  mysql_real_escape_string($cuid); }
    if (isset($data['date']))   { $data['date']   =  mysql_real_escape_string($data['date']); }
    if (isset($data['lat']))    { $data['lat']    = (float)($data['lat']); }
    if (isset($data['lng']))    { $data['lng']    = (float)($data['lng']); }
    if (isset($data['alt']))    { $data['alt']    = (float)($data['alt']); }
    if (isset($data['author'])) { $data['author'] =  mysql_real_escape_string($data['author']); }
    if (isset($data['text']))   { $data['text']   =  mysql_real_escape_string($data['text']); }
    if (isset($data['value']))  { $data['value']    = (float)($data['value']); }
    
	/* 1- make some place for a new record */
	freeingRecords($cuid,1);
			
	/* 2- Insert new record */
	$sql = "INSERT INTO `$cuid` (";
		$coma = "";	
		if (isset($data['date']))   { $sql .= $coma."date"; $coma=", "; }
		if (isset($data['lat']))    { $sql .= $coma."lat"; $coma=", "; }
		if (isset($data['lng']))    { $sql .= $coma."lng"; $coma=", "; }
		if (isset($data['alt']))    { $sql .= $coma."alt"; $coma=", "; }
		if (isset($data['author'])) { $sql .= $coma."author"; $coma=", "; }
		if (isset($data['text']))   { $sql .= $coma."text"; $coma=", "; }
		if (isset($data['value']))  { $sql .= $coma."value"; $coma=", "; }
	$sql .= ") VALUES (";
	$coma = "";
		if (isset($data['date']))   { $sql .= $coma."'".$data['date']."'"; $coma=", "; }
		if (isset($data['lat']))    { $sql .= $coma."'".$data['lat']."'"; $coma=", "; }
		if (isset($data['lng']))    { $sql .= $coma."'".$data['lng']."'"; $coma=", "; }
		if (isset($data['alt']))    { $sql .= $coma."'".$data['alt']."'"; $coma=", "; }
		if (isset($data['author'])) { $sql .= $coma."'".$data['author']."'"; $coma=", "; }
		if (isset($data['text']))   { $sql .= $coma."'".$data['text']."'"; $coma=", "; }
		if (isset($data['value']))  { $sql .= $coma."'".$data['value']."'"; $coma=", "; }
	$sql .= ")";
    if ($coma=="") return 0;
	$req = mysql_query($sql);
	if($req==NULL) return -1;  
	$id = mysql_insert_id();
    
	/* 4- Update table description */
	$sql = "UPDATE `".COUNTERLIST_TABLE."` SET LAST_UPDATE=now() WHERE `CUID`='".$cuid."'";
	$req = mysql_query($sql) or sqldie($sql);  
	return $id;
}


/* Update a record in a counter */
function updateRecord($cuid, $id, $data)
{					
    /* 0- to avoid sql injection */
    if ($cuid) { $cuid =  mysql_real_escape_string($cuid); }
    if ($id)   { $id   =  (int)($id); }
    if (isset($data['date']))   { $data['date']   =  mysql_real_escape_string($data['date']); }
    if (isset($data['lat']))    { $data['lat']    = (float)($data['lat']); }
    if (isset($data['lng']))    { $data['lng']    = (float)($data['lng']); }
    if (isset($data['alt']))    { $data['alt']    = (float)($data['alt']); }
    if (isset($data['author'])) { $data['author'] =  mysql_real_escape_string($data['author']); }
    if (isset($data['text']))   { $data['text']   =  mysql_real_escape_string($data['text']); }
    if (isset($data['value']))  { $data['alt']    = (float)($data['value']); }
    
	/* 1- Update a record */
	$sql = "UPDATE `$cuid` SET ";
    $coma = "";
	if (isset ($data['date'])) {
		$sql .= $coma." `date` =  '".$data['date']."'";
		$coma = ",";
	}
    if (isset ($data['lat'])) {
		$sql .= $coma." `lat` =  '".$data['lat']."'";
		$coma = ",";
	}
	if (isset ($data['lng'])) {
		$sql .= $coma." `lng` =  '".$data['lng']."'";
		$coma = ",";
	}
	if (isset ($data['alt'])) {
		$sql .= $coma." `alt` =  '".$data['alt']."'";
		$coma = ",";
	}
	if (isset ($data['author'])) {
		$sql .= $coma." `author` =  '".$data['author']."'";
		$coma = ",";
	}
	if (isset ($data['text'])) {
		$sql .= $coma." `text` =  '".$data['text']."'";
		$coma = ",";
	}
	if (isset ($data['value'])) {
		$sql .= $coma." `value` =  '".$data['value']."'";
		$coma = ",";
	}
    if ($coma=="") return false;
	$sql .= " WHERE `id`=".$id;
	$req = mysql_query($sql) or sqldie($sql);  
	
	/* 2- Update table description */
	$sql = "UPDATE `".COUNTERLIST_TABLE."` SET LAST_UPDATE=now() WHERE `CUID`='".$cuid."'";
	$req = mysql_query($sql) or sqldie($sql);  
	return true;
}

/* create guid */
function guid(){
    if (function_exists('com_create_guid')){
        return trim(com_create_guid(), '{}');
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
         $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
        return $uuid;
    }
}

/* Clean users in temporay based who are too old */
function cleanUserAntiChambre() {
	$sql = "DELETE FROM `".LOG_TMP_USER_TABLE."` WHERE (DATE + INTERVAL 30 MINUTE < NOW())";
    mysql_query($sql);
}

// https://en.wikipedia.org/wiki/Universally_unique_identifier#Definition
function check_UUID4($uuid) {
    $regex = '/^[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}$/'; 
    return (preg_match($regex, $uuid));
}

/* basic regex to check email valididty, TODO: Do better ? */
function check_email_address($email) {
    $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
    return (preg_match($regex, $email));
}

/* send email */
function sendMail($mailTo, $title, $messge) {
	$mail = new PHPMailer();
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
											   // 1 = errors and messages
											   // 2 = messages only		
											   
	$mail->Host       = $GLOBALS['CONFIG']['smtp_Host'];	
	$mail->Port       = $GLOBALS['CONFIG']['smtp_port'];
	$mail->Username   = $GLOBALS['CONFIG']['smtp_login'];	
	$mail->Password   = $GLOBALS['CONFIG']['smtp_pw'];											   
	$mail->SMTPAuth   = $GLOBALS['CONFIG']['smtp_auth'];
	$mail->SMTPSecure = $GLOBALS['CONFIG']['smtp_secure'];
	
	$mail->IsHTML(true);
	$mail->SetFrom(LOG_NO_REPLY_EMAIL, TITLE);
	$mail->AddReplyTo(LOG_NO_REPLY_EMAIL, TITLE);	
	$mail->AddAddress($mailTo);
	$mail->Subject  = $title;            
	$mail->Body = $messge;
	return $mail->Send();
}
?>