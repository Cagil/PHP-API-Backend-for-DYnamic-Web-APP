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
class linksController extends Controller {
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

        if($id !== null){
            $id = intval($id);
            return json_encode($this->db->getLink($id));
        }else{
            return json_encode($this->db->getLinkList());
        }
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
        if(isset($_SESSION['id']) && isset($_SESSION['admin'])){   
            $is_admin = $_SESSION['admin'];
            $is_admin = intval($is_admin);
            if($is_admin === 1 || $is_admin === true){
                //print_r($this->BODY_DATA);
                $title = htmlentities($this->BODY_DATA['title']);
                $desc = htmlentities($this->BODY_DATA['description']);
                $link = htmlentities($this->BODY_DATA['link']);    

                $result = $this->db->createLink($title, $desc, $link);
               // echo "hi";
                return json_encode($result); 
            }else{
               // echo "hi2";
                return false;
            }               
        }else{
          //  echo "hi3";
            return json_encode(false);
        }
       // echo "hi4";
        return false;
	}

	protected function put($id){
        if($id === null) return false;
        $id = intval($id);

        if(isset($_SESSION['id']) && isset($_SESSION['admin'])){   

            $is_admin = $_SESSION['admin'];
            $is_admin = intval($is_admin);
            if($is_admin === 1 || $is_admin === true){
                // print_r($this->BODY_DATA);
                $title = htmlentities($this->BODY_DATA['title']);
                $desc = htmlentities($this->BODY_DATA['description']);
                $link = htmlentities($this->BODY_DATA['link']);    

                $result = $this->db->updateLink($id, $title, $desc, $link);

                return json_encode($result); 
            }else{
                return false;
            }               
        }else{
            return json_encode(false);
        }

        return false;
	}

	protected function delete($id){
        if($id === null) return false;
        if(isset($_SESSION['id']) && isset($_SESSION['admin'])){   
            $is_admin = $_SESSION['admin'];
            $is_admin = intval($is_admin);
            if($is_admin === 1 || $is_admin === true){
                $id = intval($id);

                $result = $this->db->deleteLink($id);

                return json_encode($result); 
            }else{

            }               
        }else{
            return json_encode(false);
        }

		return false;
	}
}
?>