<?php
include_once ROOT.'/config/db.php';
include_once 'Controller.php';

class pollsController extends Controller {
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
		if($id === null){
			return json_encode($this->db->getPollList());
		}else{
			$id = htmlentities($id);
			$id = (int)$id;
			return json_encode($this->db->getPoll($id));
		}
		return false;
	}
		
	protected function post(){
		$question = htmlentities($this->BODY_DATA['question']);	
		return json_encode($this->db->createPoll($question));
	}

	protected function put($id){
		$id = htmlentities($id);
		$question = htmlentities($this->BODY_DATA['question']);	
		$state = htmlentities($this->BODY_DATA['state']);

		$id = (int)$id;

		return json_encode($this->db->updatePoll($id, $question, $state));
	}

	protected function delete($id){
		if ($_SESSION['admin'] ===1){
			$id = htmlentities($id);
			$id = (int)$id;
			return json_encode($this->db->deletePoll($id));
		} else {
			return 'Only admin can delete polls';
		}
	}
}

?>