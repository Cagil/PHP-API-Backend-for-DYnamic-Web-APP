<?php
include_once ROOT.'/config/db.php';
include_once 'Controller.php';

class answersController extends Controller {
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
			$id = htmlentities($id);
			$id = (int)$id;

			return json_encode($this->db->getPollAnswerList($id));
		}
		return false;
	}
		
	protected function post(){
		$poll_id = htmlentities($this->BODY_DATA['poll_id']);
		$answer = htmlentities($this->BODY_DATA['answer']);	
		$answers = json_decode($answer);
		$poll_id = (int)$poll_id;

		$results = array();

		for($i = 0; $i < sizeof($answers); $i++){
			$results[] = $db->createAnswer($poll_id, $answers[$i]);
		}

		return json_encode($results);
	}

	protected function put($id){
		header("HTTP/1.1 405 METHOD NOT ALLOWED");
		return false;
	}

	protected function delete($id){
	    header("HTTP/1.1 405 METHOD NOT ALLOWED");
		return false;	
	}
}

?>