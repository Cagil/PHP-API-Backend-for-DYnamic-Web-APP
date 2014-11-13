<?php
include_once ROOT.'/config/db.php';
include_once 'Controller.php';

class messagesController extends Controller {
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

	// @TODO :: check again later when the authentication is implemented;
	protected function get($_SESSION['id'] = null){
		if($_SESSION['id'] === null) return false;
		$action = null;	
		if(isset($this->BODY_DATA['action']))	{ $action = $this->BODY_DATA['action']; }

		if($action !== null){
			switch($action){
				case 'sent':
					$user_id = $_SESSION['id'];
					return json_encode($this->db->getMessageSentList($user_id));
				break;
				case 'received':
					$user_id = $_SESSION['id'];
					return json_encode($this->db->getMessageList($user_id));
				break;
				default:
					$message_id = (int)$id;
					return json_encode($this->db->getMessage($message_id));
			}
		}

	}
		
	protected function post(){
		$sender_id = $_SESSION['id'];
		$sender_id = (int)$sender_id;
		$receiver_id = htmlentities($this->BODY_DATA['receiver_id']);
		$receiver_id = (int)$receiver_id;
		$title = htmlentities($this->BODY_DATA['title']);
		$content = htmlentities($this->BODY_DATA['content']);

		return json_encode($this->db->createMessage($sender_id, $receiver_id, $title, $content));
	}

	// no need to update messages
	public function put($id){	}

	//should implement removeMessage($id) in db.php
	public function delete($id){
		$message_id = (int)$id;
		$user_id = $_SESSION['id'];
		echo $user_id.'==='.$message_id;
		return json_encode($this->db->deleteMessage($message_id, $user_id));
	}
}

?>