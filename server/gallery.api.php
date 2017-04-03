<?PHP
include '../header.php'; 
include 'PHPLog/log.php';

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

$gfolder = "../gallery";
$gurl = CurrentPageBaseUrl()."gallery";

$sql = "SELECT * FROM `".GALLERY_TABLE."`";
$req = mysql_query($sql); 
$rows = array(); 
if (NULL==$req) die_internal_error(sqldie($sql));
while ($data = mysql_fetch_assoc($req))
{
	
	$folder = $gfolder."/".$data['FOLDER'];
	$url = $gurl."/".$data['FOLDER'];
	
	$tmp = [];
	$tmp['folder'] = $data['FOLDER'];
	$tmp['is_viewer'] = $data['IS_VIEWER'];
	$tmp['is_viewer'] = $data['IS_VIEWER'];
	$tmp['is_record'] = $data['IS_RECORDER'];
	$tmp['isInstall'] = false;
	
	$sql2 = "SELECT * FROM `".COUNTERLIST_TABLE."` WHERE `CUID`='".$data['DEFAULT_CUID']."'";
	$req2 = mysql_query($sql2) or sqldie($sql2);  
	$counter = mysql_fetch_assoc($req2);
	if ($counter==NULL) {
		$tmp['default_url'] = null;
	} else {
		$tmp['default_url'] = "$url/index.php?rkey=".$counter['READ_KEY'];
	}
	
	$tmp['img'] = file_exists ("$folder/preview.png")?"$url/preview.png":"$gurl/default.png";
	
	do {
		$test = "$folder/description.$lang.html";
		if (file_exists($test)) { $tmp['html'] = file_get_contents($test); break; }
		$test = "$folder/description.html";
		if (file_exists($test)) { $tmp['html'] = file_get_contents($test); break; }
		$test = "$folder/description.en.html";
		if (file_exists($test)) { $tmp['html'] = file_get_contents($test); break; }
		$test = "$folder/description.fr.html";
		if (file_exists($test)) { $tmp['html'] = file_get_contents($test); break; }		
		$tmp['html'] = "";
	} while(0);
	
	$rows[] = $tmp;
}

header('HTTP/1.1 200 OK', true, 200);
print json_encode($rows);

include '../footer.php'; 