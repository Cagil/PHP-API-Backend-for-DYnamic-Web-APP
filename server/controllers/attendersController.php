<?php
include_once ROOT.'/config/db.php';
include_once 'Controller.php';

class attendersController extends Controller {
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
			$id = (int)$id;		//casting integers.

			return json_encode($this->db->getEventUserList($id));
		}
		
		return false;
	}

	//to post a new comment 
	protected function post(){
		$user_id = htmlentities($this->BODY_DATA['user_id']);
		$event_id = htmlentities($this->BODY_DATA['event_id']);
		$paid = htmlentities($this->BODY_DATA['paid']);
		$paid = (boolean)$paid;
		$user_id = (int)$user_id;		//casting integers.
		$event_id = (int)$event_id;		//casting integers.

		return json_encode($this->db->createAttender($user_id, $event_id, $paid));
	}

	protected function put($id){
		if($id !== null){
			$id = htmlentities($id);
			$paid = htmlentities($this->BODY_DATA['paid']);
			$id = (int)$id;		//casting integers.
			$paid = (boolean)$paid;

			return json_encode($this->db->updateAttender($id, $paid));	
		}
		return false;
	}

	protected function delete($id){
		if($id !== null){
			$id = htmlentities($id);
			$id = (int)$id;
			return json_encode($this->db->deleteAttender($id));
		}
		return false;
	}
}
?>