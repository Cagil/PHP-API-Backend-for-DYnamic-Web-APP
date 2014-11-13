<?php
include_once ROOT.'/config/db.php';
//include_once '/home/users/web/b970/moo.cagilozdemiragcom/subs/iss/server/config/db.php';
include_once 'Controller.php';

/**
* usersController
*
* @uses     Controller for statistics.
* 			Data validation is assumed to be done on client-side. 
*
* @category Category
* @package  Package
* @author   Cagil Ozdemirag   
*/
class statsController extends Controller{
	public function __construct($request_method, $uri_data= null, $body_data = null){
		$this->HTTP_METHOD = "".strtolower($request_method);		
		if(isset($uri_data['id'])){
			$this->URI_DATA = $uri_data['id'];
		}else{
			$this->URI_DATA = null;	
		}
		$this->BODY_DATA = $body_data;

		$this->db = DatabaseConnection::getInstance();
	}	

	/**
	* call
	* 
	* @uses 	The only function to be called in all the Controllers.
	* 			Depending on the request method, call() function makes the call for the appropriate function.
	*
	* @access public
	*
	* @return values from get()/post()/put()/delete() functions.
	*/
	public function call(){
		$method = "".$this->HTTP_METHOD;
		$data = $this->URI_DATA;
		 
		return self::$method($data);
	}	

	protected function get($id=null){
		if($id !== null){
			$answer_list = $this->db->getPollAnswerList($id);
			$data = array();
			$sum = 0;
			for($i = 0; $i < sizeof($answer_list); $i++){
				$sum += (int)$answer_list[$i]["count"];
			}
			$data["answer_list"] = $answer_list;
			$data["total_votes"] = $sum;

			return json_encode($data);
		}
		
		return false;
	}
		
	protected function post(){
		header("HTTP/1.1 405 METHOD NOT ALLOWED");
    	return false;
	}

	protected function put($id){
		header("HTTP/1.1 405 METHOD NOT ALLOWED");
    	return false;;
	}

	public function delete($id){
		header("HTTP/1.1 405 METHOD NOT ALLOWED");
    	return false;
	}
}
?>