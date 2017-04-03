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
//$_DATA['cuid'] = '06a3d28f-64f4-47c3-bf08-7788d7f34ad4';
if (!isset ($_DATA['cuid'])) die_bad_request("no CUID 1");
$CUID = $_DATA['cuid'];
if (!check_UUID4($CUID))  die_bad_request("no CUID (".$CUID.")");
$rows=array();
		
switch ($_SERVER['REQUEST_METHOD'])
{
    // ----------------------------------------------------------
    // GET Record list
    // ----------------------------------------------------------
    // $http.get($scope.SrvUrl("records"));
    //
	case 'GET':
		$sql = 'SELECT * FROM `'.$CUID.'`';
		$req = mysql_query($sql) or sqldie($sql);
        while ($data = mysql_fetch_assoc($req)) {
			$row=array();
			if (isset($data['id']))     $row['id']       = (int)$data['id'];
			if (isset($data['date']))   $row['date']     = $data['date'];
			if (isset($data['lat']))    $row['lat']      = (float)$data['lat'];
			if (isset($data['lng']))    $row['lng']      = (float)$data['lng'];
			if (isset($data['alt']))    $row['alt']      = (float)$data['alt'];
			if (isset($data['author'])) $row['author']   = $data['author'];
			if (isset($data['text']))   $row['text']     = $data['text'];
            if (isset($data['value']))  $row['value']    = (float)$data['value'];
			$rows[] = $row;
		}
		header('HTTP/1.1 200 OK', true, 200);
		print json_encode($rows);
		break;
        
        
    // ----------------------------------------------------------
    // CREATE a Record
    // ----------------------------------------------------------
    // $http.post($scope.SrvUrl("records"));
    //	
	case 'POST':
        $num_rows = tableCount($CUID);
        if ($num_rows>=$USER['MAX_RECORD'] && $USER['MAX_RECORD']!=0)
			die_bad_request("Maximum record reached!", "COUNTER_ENTERMAXRECORD");
        $rows['id'] = pushNewRecord($CUID, $_DATA);
        if ($rows['id']==-1) die_bad_request("couldn't push a new record");
        print json_encode($rows);
        header('HTTP/1.1 200 OK', true, 200);
        break;	
    
    // ----------------------------------------------------------
    // DELETE one specific record
    // ----------------------------------------------------------
    // $http.delete($scope.SrvUrl("records"));
    //
	case 'DELETE':	
		$sql = 'DELETE FROM `'.$CUID.'` WHERE  `id`='.(int)($_DATA['id']);
		$req = mysql_query($sql) or sqldie($sql);		
		header('HTTP/1.1 200 OK', true, 200);
		break;

		
    // ----------------------------------------------------------
    // UPDATE a record
    // ----------------------------------------------------------
    // $http.put($scope.SrvUrl("records"));
    //	
	case 'PUT':
        if (!isset ($_DATA['id']))
            die_bad_request('missing id');
		if (!updateRecord($CUID, (int)($_DATA['id']), $_DATA))
			die_bad_request("couldn't update a record");
        header('HTTP/1.1 200 OK', true, 200);
        break;
			
			
	default:
		die_bad_request('invalid method');
		break;			
}
	
include '../footer.php'; 


