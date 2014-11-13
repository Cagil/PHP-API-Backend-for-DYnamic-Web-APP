<?php
include_once ROOT.'/config/db.php';
include_once 'Controller.php';

/**
* usersController
*
* @uses     Controller for users.
* 			Data validation is assumed to be done on client-side. 
*
* @category Category
* @package  Package
* @author   Cagil Ozdemirag
*   
*/
class usersController extends Controller {
	protected $HTTP_METHOD;
	protected $URI_DATA;
	protected $BODY_DATA;
	protected $db;

    /**
     * __construct
     * 
     * @param $request_method request method.
     * @param $uri_data       uri data for GET/PUT/DELETE requests, only used for resource ids and special case of distinguishing login/registration (both POST) requests..
     * @param $body_data      body data for POST/PUT/DELETE requests.
     *
     * @access public
     *
     * @return usersController instance.
     */
	public function __construct($request_method, $uri_data = null, $body_data = null){
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

    /**
     * get
     * 
     * @param $id OPTIONAL id of the user.
     *
     * @access protected
     *
     * @return single user's details if param 'id' is provided
     * @return all the user's details if param 'id' IS NOT provided
     * @return false, if there is an error in db or with the param 'id'
     */
	protected function get($id=null){
		if($id === null){
			return json_encode($this->db->getUserList());
		}else{
			$id = (int)$id;
			return json_encode($this->db->getUser($id));
		}
		return false;
	}

    /**
     * post
     * 
     * @uses registering/logging in users
     * @uses checking if provided user details are valid, and registered in the database while logging in.
     *
     * @access protected
     *
     * @return user's id, and user's name, in the case of logging in.
     * @return user's id, in the case of registering.
     * @return false, if the details provided are wrong or there is an error in the databse.
     */
	protected function post(){
		$action = htmlentities($this->BODY_DATA['action']);

		switch($action){
			case 'login':
				session_destroy();
				$email = htmlentities($this->BODY_DATA['email']);
				$password = htmlentities($this->BODY_DATA['password']);

				$result = $this->db->login($email, $password);
				if($result !== false){
					$month = time() + (60*60*24*30);
					setcookie("user_id", $result['id'], $month);
					setcookie("admin", $result['admin'], $month);

					if(isset($_COOKIE['user_id'])){
						session_start();
						$_SESSION['id'] = $result['id'];
						$_SESSION['admin'] = $result['admin'];
					}else{
						return false;
					}
					return json_encode($result);
					
				}
				return false;
				break;
			case 'register':
				$name = htmlentities($this->BODY_DATA['username']);
				$email = htmlentities($this->BODY_DATA['email']);
				$password = htmlentities($this->BODY_DATA['password']);
				$student_number = htmlentities($this->BODY_DATA['studentnumber']);

				return json_encode($this->db->registerUser($name, $email, $password, $student_number));
				break;
			default:
				return false;

		}
	}

    /**
     * put
     * 
     * @uses updating user's details
     *
     * @param $id user's id.
     *
     * @access protected
     *
     * @return true on successful update.
     * @return false, if update cannot be done, or the param 'id' is not provided.
     */
	protected function put($id){	
		if($_SESSION['id'] !== null){		
			$id = $_SESSION['id'];
			$name = htmlentities($this->BODY_DATA['username']);
			$email = htmlentities($this->BODY_DATA['email']);
			$password = htmlentities($this->BODY_DATA['password']);
			$id = (int)$id;
			
			return json_encode($this->db->updateUser($id, $name, $email, $password));
		}
		return false;
	}

    /**
     * delete
     * 
     * @uses removing user from the database.
     * 
     * @param $id user's id.
     *
     * @access protected
     *
     * @return true on successful removal.
     * @return false, if removing user cannot be done, or the param 'id' is not provided.
     */
	protected function delete($id){
		if($_SESSION['id'] !== null){
			$id = $_SESSION['id'];
			$id = (int)$id;

			return json_encode($this->db->deleteUser($id));
		}

		return false;
	}
}
?>