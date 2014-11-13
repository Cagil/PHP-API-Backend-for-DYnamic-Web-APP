<?php
include_once ROOT.'/config/db.php';
//include_once '/home/users/web/b970/moo.cagilozdemiragcom/subs/iss/server/config/db.php';
include_once 'Controller.php';

/**
* usersController
*
* @uses     Controller for events.
* 			Data validation is assumed to be done on client-side. 
*
* @category Category
* @package  Package
* @author   Thamer Alshammari
*   	  
*   Improved by Cagil Ozdemirag  
*/
class eventsController extends Controller{
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
			return json_encode($this->db->getEventList());
		}else{
			$id = htmlentities($id);
			$id = (int)$id;

			return json_encode($this->db->getEvent($id));
		}

		return false;
	}
		
	protected function post(){		
		$creator_user_id = $_SESSION['id'];
		$is_admin = $_SESSION['admin'];
		$is_admin = intval($is_admin);

		$price = 0;
		$title = htmlentities($this->BODY_DATA['title']);
		$description = htmlentities($this->BODY_DATA['description']);
		$timetable_id = htmlentities($this->BODY_DATA['timetable_id']);
		$is_free = htmlentities($this->BODY_DATA['is_free']);

		if(isset($this->BODY_DATA['price'])){
			$price = htmlentities($this->BODY_DATA['price']);	
		}

		$creator_user_id = (int)$creator_user_id;
		$timetable_id = (int)$timetable_id;
		$is_free = (boolean)$is_free;
		$price = (float)$price;

		if ($is_admin === 1){
			return json_encode($this->db->createEvent($creator_user_id, $title, $description, $timetable_id, $is_free, $price));
		} else {
			header("HTTP/1.1 405 ADMIN PRIVILEGES REQUIRED");
			return false;
		}
	}

	protected function put($id){
		$is_admin = $_SESSION['admin'];
		$is_admin = intval($is_admin);
		//if($this->BODY_DATA === null) return 'body is null =='.$id;
		if($id !== null){
			$id = htmlentities($id);
			$title = htmlentities($this->BODY_DATA['title']);
			$description = htmlentities($this->BODY_DATA['description']);
			//$timetable_id = htmlentities($this->BODY_DATA['timetable_id']);
			$start_date = htmlentities($this->BODY_DATA['start_date']);
			$end_date = htmlentities($this->BODY_DATA['end_date']);
			$is_free = htmlentities($this->BODY_DATA['is_free']);
			$price = 0;
			if(isset($this->BODY_DATA['price'])){
				$price = htmlentities($this->BODY_DATA['price']);	
			}

			//print_r($this->BODY_DATA);

			$id = intval($id);
			//$timetable_id = (int)$timetable_id;
			$is_free = (boolean)$is_free;
			$price = (float)$price;

			if ($is_admin === 1){
				
			return json_encode($this->db->updateEvent($id, $title, $description, $is_free, $price));
			} else {
				header("HTTP/1.1 405 ADMIN PRIVILEGES REQUIRED");
				return false;
			}
		}

		return false;
	}

	public function delete($id){
		$is_admin = $_SESSION['admin'];
		$is_admin = intval($is_admin);
		if($_SESSION['id'] !== null && $is_admin === 1){
			$id = intval($id);
			return json_encode($this->db->deleteEvent($id));
		}
		header("HTTP/1.1 405 ADMIN PRIVILEGES REQUIRED");
		return false;
	}
}
?>