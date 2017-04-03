<?PHP
include '../header.php'; 
include 'PHPLog/log.php';
include 'common.php';

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
//if ($_Action==null) die_bad_request('invalid action');

if (!$USER) die_forbidden();

$rows=array();
		
switch ($_SERVER['REQUEST_METHOD'])
{
    // ----------------------------------------------------------
    // GET Counter list
    // ----------------------------------------------------------
    // $http.get($scope.SrvUrl("counter"));
    //
	case 'GET':
		$sql = 'SELECT * FROM `'.COUNTERLIST_TABLE.'` WHERE UUID="'.$USER['UUID'].'"';
		$req = mysql_query($sql) or sqldie($sql);
		while ($data = mysql_fetch_assoc($req)) {
			$row=array();
			$row['name'] = $data['NAME'];
			$row['cuid'] = $data['CUID'];
			$row['last_update'] = $data['LAST_UPDATE'];
			$row['wkey'] = $data['WRITE_KEY'];
			$row['rkey'] = $data['READ_KEY'];
			$row['max'] = (int)$data['MAX_RECORD'];
			$row['count'] = (int)tableCount($data['CUID']);
			$rows[] = $row;
		}
		header('HTTP/1.1 200 OK', true, 200);
		print json_encode($rows);
		break;
        
        
    // ----------------------------------------------------------
    // CREATE a Counter
    // ----------------------------------------------------------
    // $http.post($scope.SrvUrl("counter"));
    //	
	case 'POST':
        $num_rows = tableCount(COUNTERLIST_TABLE, 'UUID="'.$USER['UUID'].'"');
        if ($num_rows>=$USER['MAX_COUNTER'] && $USER['MAX_COUNTER']!=0) {
            die_bad_request("Maximum counter reached!", "COUNTER_ERROR_MAXIMUMREACHED");
        } else {
            /* default value */		
            $rows['CUID'] = guid();
            $rows['MAX_RECORD'] = $USER['MAX_RECORD'];
            $rows['NAME'] = "Counter $num_rows";
            $rows['WRITE_KEY'] = guid();
            $rows['READ_KEY'] = guid();
            if (!helper_create_counter($USER, $rows))
                die_bad_request("couldn't create counter");
        }        
        header('HTTP/1.1 200 OK', true, 200);
        break;	
    
    // ----------------------------------------------------------
    // DELETE a Counter
    // ----------------------------------------------------------
    // $http.delete($scope.SrvUrl("counter"));
    //
	case 'DELETE':
		if (!isset ($_DATA['cuid']))
			die_bad_request("no CUID");
        if (!check_UUID4 ($_DATA['cuid']))
			die_bad_request("no CUID");

		// Maybe a paranoiac security, but I don't want to use the posted CUID directly cause it's used later to drop a whole table
		$sql = 'SELECT `CUID` FROM `'.COUNTERLIST_TABLE.'` WHERE (UUID="'.$USER['UUID'].'" and CUID="'.$_DATA['cuid'].'")';
		$req = mysql_query($sql) or sqldie($sql);
		$data = mysql_fetch_assoc($req);
		if (!$data) die_bad_request();
		$CUID = $data['CUID'];
		
		
		$sql = 'DELETE FROM `'.COUNTERLIST_TABLE.'` WHERE (UUID="'.$USER['UUID'].'" and CUID="'.$CUID.'")';
		$req = mysql_query($sql) or sqldie($sql);
		$num_rows = mysql_affected_rows();		
		if ($num_rows==1) {
			$sql = 'DROP TABLE `'.$CUID.'`';
			$req = mysql_query($sql) or sqldie($sql);
		}
		header('HTTP/1.1 200 OK', true, 200);
		break;
	
                    
		
    // ----------------------------------------------------------
    // UPDATE a Counter
    // ----------------------------------------------------------
    // $http.put($scope.SrvUrl("counter"));
    //	
	case 'PUT':
        if (!isset ($_DATA['cuid']))
			die_bad_request('missing CUID');
		if (!check_UUID4 ($_DATA['cuid']))
			die_bad_request("no valid CUID");	
        if (($USER['MAX_RECORD']!=0) && (isset($_DATA['max']) && $_DATA['max']>$USER['MAX_RECORD']))
            die_bad_request('invalid max record value');
    
        /* 0- to avoid sql injection */
        if (isset($_DATA['name']))      { $_DATA['name']    =  mysql_real_escape_string($_DATA['name']); }
        if (isset($_DATA['wkey']))      { $_DATA['wkey']    =  mysql_real_escape_string($_DATA['wkey']); }
        if (isset($_DATA['rkey']))      { $_DATA['rkey']    =  mysql_real_escape_string($_DATA['rkey']); }
        if (isset($_DATA['max']))       { $_DATA['max']     = (int)($_DATA['max']); }
        
        $sql = "UPDATE `".COUNTERLIST_TABLE."` SET ";
		$coma = "";
        if ((isset($_DATA['name'])) && ($_DATA['name']!="")) {
            $sql .= $coma." `NAME` =  '".$_DATA['name']."'";
            $coma = ",";
        }
		if ((isset($_DATA['wkey'])) && ($_DATA['wkey']!="")) {
            $sql .= $coma." `WRITE_KEY` =  '".$_DATA['wkey']."'";
            $coma = ",";
        }
		if ((isset($_DATA['rkey'])) && ($_DATA['rkey']!="")) {
            $sql .= $coma." `READ_KEY` =  '".$_DATA['rkey']."'";
            $coma = ",";
        }
		if (isset($_DATA['max'])) {
            $sql .= $coma." `MAX_RECORD` =  '".((int)$_DATA['max'])."'";
            $coma = ",";
        }
		$sql .= ' WHERE (UUID="'.$USER['UUID'].'" and CUID="'.$_DATA['cuid'].'")';
        $req = mysql_query($sql) or sqldie($sql); 

		
        // meets the MAX_RECORD requirement ?
        $current = tableCount($_DATA['cuid']);
        if ($current>$_DATA['max']) {
            freeingRecords($_DATA['cuid'],$_DATA['max']-$current);
        }
        header('HTTP/1.1 200 OK', true, 200);
        break;
			
			
	default:
		die_bad_request('invalid method');
		break;			
}
	
include '../footer.php'; 


