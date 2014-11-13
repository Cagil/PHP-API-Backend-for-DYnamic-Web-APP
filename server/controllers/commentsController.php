<?php
include_once ROOT.'/config/db.php';
include_once 'Controller.php';

class commentsController extends Controller {
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

	//
	protected function get($id=null){
		if($id !== null){
			$id = htmlentities($id);
			$id = (int)$id; // event_id
			return json_encode($this->db->getCommentList($id));	
		}
		return false;
	}
	
	//to post a new comment 
	protected function post(){
		$user_id = $_SESSION['id'];
		//echo $_SESSION['id'];
		$event_id = htmlentities($this->BODY_DATA['event_id']);
		$content = htmlentities($this->BODY_DATA['content']);
		$user_id = (int)$user_id;
		$event_id = (int)$event_id;


		return json_encode($this->db->createComment($user_id, $event_id, $content));
	}

	protected function put($id){
		//no need to update comment function!
	}

	protected function delete($id){
		if($_SESSION['id'] !== null){
			$id = (int)$id;
			return json_encode($this->db->deleteComment($id));
		}
		return 'only admin delete comment';
	}
}
?>