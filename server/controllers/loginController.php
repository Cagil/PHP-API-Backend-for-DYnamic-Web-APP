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
class loginController extends Controller {
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

	protected function get($id=null){
        header("HTTP/1.1 405 METHOD NOT ALLOWED");
    	return false;
	}

    /**
     * post
     * 
     * @uses checking if provided user details are valid, and registered in the database while logging in.
     *
     * @access protected
     *
     * @return user's id, and user's name
     * @return false, if the details provided are wrong or there is an error in the databse.
     */
	protected function post(){
        session_destroy();
        
		$email = htmlentities($this->BODY_DATA['email']);
		$password = htmlentities($this->BODY_DATA['password']);

        $result = $this->db->login($email, $password);
        if($result !== false){
            session_start();
            $_SESSION['id'] = $result['id'];
            $_SESSION['admin'] = $result['admin'];
            $_SESSION['lang'] = "en";
            //exit( "session created: ".$_SESSION['id']);
        }

		return json_encode($result);
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