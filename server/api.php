<?php
//@TODO look into preg patterns
define("ROOT", __DIR__);
date_default_timezone_set("Europe/London");

// include_once "Router.php";

// $router = new Router('local');


/**
*
* @uses Getting all available controller prefixes.
*/
$dir_name = __DIR__.'/controllers/';
$token = $dir_name;
$dir_name .= '*.php';
$glo = glob($dir_name);

$controllers = array();

for($i = 0; $i < sizeof($glo); $i++){
	$file_name = explode($token, $glo[$i]);
	$file_name = $file_name[1];

	$cont = explode('Controller.php', $file_name);
	$cont = trim($cont[0]);
	
	if(!empty($cont)) {
		$controllers[] = $cont;
	}
	
}



$uri_pieces = explode('/', $_SERVER['REQUEST_URI']);

$controller_prefix = "";
$URI_DATA = null;

$URI_INDEX = 3;
$URI_DATA_INDEX = $URI_INDEX+ 1;
//the 4 - index number in uri_pieces depends on the URI. 
//first index in this case which is 4 should be one of the $controller array items
if(in_array($uri_pieces[$URI_INDEX], $controllers)){
	$controller_prefix = $uri_pieces[$URI_INDEX];
	$URI_DATA = array();

	//PUT id.
	if(isset($uri_pieces[$URI_DATA_INDEX])){
		$URI_DATA['id'] = $uri_pieces[$URI_DATA_INDEX];
	}

}

$REQ_METH = $_SERVER['REQUEST_METHOD'];
$REQ_METH = strtolower($REQ_METH);

$BODY_DATA = null;
if($REQ_METH === 'put' || $REQ_METH === 'delete' || $REQ_METH === 'post'){	
// 	$data = http_get_request_body();
// }elseif($REQ_METH === 'post'){
	//echo "getting data";
	//$data = @file_get_contents('php://input');
	//$data = fopen("php://input", "r");
	
	/* PUT data comes in on the stdin stream */
	
//$data = @file_get_contents(STDIN);
//$data = http_get_request_body();
 $putdata = fopen("php://input", "r");
 $data = '';

//  Read the data 1 KB at a time
//    and write to the file 
 while ($d = fread($putdata, 1024))
   $data .= $d;

// /* Close the streams */
 fclose($putdata);


	//print_r($data);
	$BODY_DATA =  json_decode($data, true);

	
}
                   
//echo $controller_prefix;
 // echo "\n\n\nDATA->";
 // print_r($BODY_DATA);
 // echo "<-DATA!";


$data_bundle = array();
$data_bundle['request_method'] = $_SERVER['REQUEST_METHOD'];
$data_bundle['uri_data'] = $URI_DATA;
$data_bundle['body_data'] = $BODY_DATA;


// $bundle = $router->getDataBundle();
// print_r($bundle);
// $prefix = $router->getPrefix();
// echo $prefix;
include 'ControllerFactory.php';

$controller = ControllerFactory::createController($controller_prefix, $data_bundle);
//$_content_type = "application/json";

exit($controller->call());
?>