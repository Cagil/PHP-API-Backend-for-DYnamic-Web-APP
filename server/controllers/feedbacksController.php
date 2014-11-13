<?php
include_once ROOT.'/config/db.php';
include_once 'Controller.php';

class feedbacksController extends Controller {
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

	protected function get($id = null){
		if($id === null){
			return json_encode($this->database->getFeedbackList());
		}else{
			$id = (int)$id;
			return json_encode($this->database->getFeedback($id));
		}
		return false;
	}
		
	protected function post($_SESSION['id'], $title, $content){
		$user_id = (int)$_SESSION['id'];
		$title = htmlentities($this->BODY_DATA['title']);
		$content = htmlentities($this->BODY_DATA['content']);
		
		return json_encode($this->database->makeFeedback($user_id, $title, $content));
	}

	// no need to update feedbacks
	protected function put(){	}

	//should implement removeMessage($id) in db.php
	protected function delete($id){
		if ($_SESSION['admin'] === 1){
			$id = (int)$id;
			return json_encode($this->database->removeFeedback($id));
		} else {
			return 'Only Admin can delete feedback'
		}
	}
}

?>