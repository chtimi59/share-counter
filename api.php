<?PHP
include 'header.php'; 
include "server/common.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, x-xsrf-token');
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header('Content-type: application/json');

$json = file_get_contents('php://input');
$post_data = json_decode($json,true); //$post_data==NULL if not data
if ($post_data==null) $post_data=$_POST;

$key=null;
if (!isset ($_GET['key'])) die_bad_request('missing \'key\' parameter ');
$key=$_GET['key'];

if (!check_UUID4($key))   die_bad_request('bad \'key\' parameter ');
if (isset ($_GET['callback'])) $jsonp = $_GET['callback'];

$OUT="";
$rows=array();

switch ($_SERVER['REQUEST_METHOD'])
{
	//curl -d "" -X POST "http://localhost/share-counter/api.php?key=E50B11C3-A3AE-4664-A327-C7AB8AE8A912&value=5"
	case 'POST':
		/* 1- grap info from table description */
		$sql = "SELECT * FROM `".COUNTERLIST_TABLE."` WHERE `WRITE_KEY`='$key'";
		$req = mysql_query($sql);  
		if (NULL==$req) die_internal_error();
		$table = mysql_fetch_assoc($req);
		if (NULL==$table) die_forbidden('invalid key');
		
		/* 2- prepare params */
		$epoch = false;
        $format = "json";
        
        $list = array();
        if (isset($_GET['date']))   { $list['date']=$_GET['date'];     $format = "get"; }
        if (isset($_GET['lat']))    { $list['lat']=$_GET['lat'];       $format = "get"; }
        if (isset($_GET['lng']))    { $list['lng']=$_GET['lng'];       $format = "get"; }
        if (isset($_GET['alt']))    { $list['alt']=$_GET['alt'];       $format = "get"; }
        if (isset($_GET['author'])) { $list['author']=$_GET['author']; $format = "get"; }
        if (isset($_GET['text']))   { $list['text']=$_GET['text'];     $format = "get"; }
        if (isset($_GET['value']))  { $list['value']=$_GET['value'];   $format = "get"; }
        
		if (isset($_GET['epoch']))   { $epoch = true; }

		if ($format == "json") {
			if ($post_data==null) break; // sucess as there is nothing to do ;-)
			if (isset($post_data['date']))   { $list['date']=$post_data['date'];     }
			if (isset($post_data['lat']))    { $list['lat']=$post_data['lat'];       }
			if (isset($post_data['lng']))    { $list['lng']=$post_data['lng'];       }
			if (isset($post_data['alt']))    { $list['alt']=$post_data['alt'];       }
			if (isset($post_data['author'])) { $list['author']=$post_data['author']; }
			if (isset($post_data['text']))   { $list['text']=$post_data['text'];     }
			if (isset($post_data['value']))  { $list['value']=$post_data['value'];   }			
		}

		if($epoch and isset($list['date'])) $list['date']=date(DATE_W3C, $list['date']);
		//2015-09-14 22:09:53
		//1442282993
		//print_r($list);
		
		// This fonction has protection against SQL injection
		pushNewRecord($table['CUID'], $list) or die_internal_error();
		$OUT.=json_encode($list);		
		break;
	
	//curl "http://localhost/share-counter/api.php?key=C63D5D05-0C3A-448F-8353-76D0242411C7"
	case 'GET':

		/* 1- grap info from table description */
		$sql = "SELECT * FROM `".COUNTERLIST_TABLE."` WHERE `READ_KEY`='$key'";
		$req = mysql_query($sql);  
		if (NULL==$req) die_internal_error();
		$table = mysql_fetch_assoc($req);
		if (NULL==$table) die_forbidden('invalid key'.$key);
		
        /* 2- prepare params */
		$epoch = false;
        $format = "json";
		$limit = "";
		$from = 0;
        $count = 1;
        
        $list = array();
        if (isset($_GET['date']))   { $list[]='date';   $format = "csv"; }
        if (isset($_GET['lat']))    { $list[]='lat';    $format = "csv"; }
        if (isset($_GET['lng']))    { $list[]='lng';    $format = "csv"; }
        if (isset($_GET['alt']))    { $list[]='alt';    $format = "csv"; }
        if (isset($_GET['author'])) { $list[]='author'; $format = "csv"; }
        if (isset($_GET['text']))   { $list[]='text';   $format = "csv"; }
        if (isset($_GET['value']))  { $list[]='value';  $format = "csv"; }
        
        if (isset($_GET['from']))   { $from = $_GET['from']; }
        if (isset($_GET['count']))  { $count = $_GET['count']; $count = min($count,256); }
		$limit = "LIMIT $from,$count";		
		
		if (isset($_GET['epoch']))   { $epoch = true; }
		
		/* 3- read table */
		$sql = "SELECT * FROM `".$table['CUID']."` ORDER BY  `ID` DESC ".$limit;
		$req = mysql_query($sql);  
		if (NULL==$req) die_forbidden('invalid');
		$num_rows = mysql_num_rows($req);
		
		/* 4- output */
        $OUT='';
        switch($format)
        {
            case "csv":
                while ($data = mysql_fetch_assoc($req)) {
                   $coma="";
				   if($epoch and isset($data['date'])) $data['date']=strtotime($data['date']);
                   foreach($list as $t) { $OUT.=$coma.$data[$t]; $coma=", "; }        
                   $OUT.="\n";
                }
                break;
                
            case "json":
                if (isset($jsonp)) $OUT.= "$jsonp(";
                $rows['from'] = $from;
                $rows['count'] = $count;
                $rows['datas'] = array();
                while ($data = mysql_fetch_assoc($req)) {
				   if($epoch and isset($data['date'])) $data['date']=strtotime($data['date']);
                   $rows['datas'][] = $data ;
                }
                $OUT.=json_encode($rows);		
                if (isset($jsonp))$OUT.= ")";
                break;                
        }
		break;
}

include 'footer.php'; 

// --------------------------------------------------

header('HTTP/1.1 200 OK', true, 200);
print $OUT;
exit();

?>

