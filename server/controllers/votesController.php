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
		header("HTTP/1.1 405 METHOD NOT ALLOWED");
		return false;
	}
		
	protected function post(){
		$user_id = htmlentities($this->BODY_DATA['user_id']);
		$poll_id = htmlentities($this->BODY_DATA['poll_id']);
		$answer_id = htmlentities($this->BODY_DATA['answer_id']);

		$user_id = (int)$user_id;
		$poll_id = (int)$poll_id;
		$answer_id = (int)$answer_id;

		$canVote = $this->db->canVotePoll($user_id, $poll_id);
		if($canVote === true){
			$vote_id = $this->db->votePoll($user_id, $poll_id);
			$vote_id = (int)$vote_id;
			if(is_numeric($vote_id)){
				$inc_answer_vote = $this->incAnswerCount($answer_id);

				return json_encode($inc_answer_vote);
			}

			return json_encode(false);
		}
		return json_encode(false);

		//return json_encode($this->db->createVote($user_id, $poll_id, $answer_id));
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