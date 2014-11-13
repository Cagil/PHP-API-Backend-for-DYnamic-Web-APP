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
class roomsController extends Controller{
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
		// if($id === null){
		// 	$available_rooms = $this->db->getAvailableRoomIds()
		// 	//return json_encode($this->db->getRoomList());
		// }else{
		// 	$id = htmlentities($id);
		// 	$id = (int)$id;

		// 	return json_encode($this->db->getRoom($id));
		// }
		if(isset($_GET['start_date']) && isset($_GET['end_date']) && isset($_GET['capacity'])){
			$start_date = $_GET['start_date'];
			$end_date = $_GET['end_date'];
			$capacity = $_GET['capacity'];
			$start_date = htmlentities($start_date);
			$end_date = htmlentities($end_date);
			$capacity = htmlentities($capacity);
			$capacity = (int)$capacity;

			$available_rooms = $this->db->getAvailableRoomIds($start_date, $end_date, $capacity);


			if($available_rooms !== false){
				return json_encode($this->db->getRoomList($available_rooms));
			}
		}

		return false;
	}
		
	protected function post(){
		$is_admin = $_SESSION['admin'];
		$is_admin = intval($is_admin);
		$room_id = htmlentities($this->BODY_DATA['room_id']);
		$start_date = htmlentities($this->BODY_DATA['start_date']);
		$end_date = htmlentities($this->BODY_DATA['end_date']);
		$room_id = intval($room_id);

		if ($is_admin === 1){
			$timetable_id = $this->db->createTimetable($room_id, $start_date, $end_date);

			if($timetable_id !== false){
				$timetable_id = intval($timetable_id);
				return json_encode($this->db->getTimetable($timetable_id));
			}else{
				return false;
			}

			//return json_encode($this->db->createTimetable($room_id, $start_date, $end_date));
		//return json_encode($this->db->createEvent($time_id, $creator_user_id, $title, $description, $location, $start_date, $end_date, $is_free, $price));
		} else {
			header("HTTP/1.1 405 ADMIN PRIVILEGES REQUIRED");
			return false;
		}
	}

	protected function put($id){
		$is_admin = $_SESSION['admin'];
		$is_admin = intval($is_admin);

		if($this->BODY_DATA === null) return false;
		if($id !== null){

			$id = htmlentities($id);
			$room_id = htmlentities($this->BODY_DATA['room_id']);
			$start_date = htmlentities($this->BODY_DATA['start_date']);
			$end_date = htmlentities($this->BODY_DATA['end_date']);

			$id = intval($id);
			$room_id = intval($room_id);
			if ($is_admin === 1){
				return json_encode($this->db->updateTimetable($id, $room_id, $start_date, $end_date));
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
			return json_encode($this->db->deleteTimetable($id));
		}

		header("HTTP/1.1 405 ADMIN PRIVILEGES REQUIRED");
		return false;
	}
}
?>