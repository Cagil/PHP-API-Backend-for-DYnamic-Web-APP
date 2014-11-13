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
class languagesController extends Controller{
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

    /**
     * get
     * 
     * @param $id Description.
     *
     * @access protected
     *
     * @return mixed Value.
     */
	protected function get($id=null){
		
		if($id === null){
			$dir_name = ROOT.'/languages/';
			$token = $dir_name;
			$dir_name .= '*.ini';
			$glo = glob($dir_name);

			$languages = array();

			for($i = 0; $i < sizeof($glo); $i++){
				$file_name = explode($token, $glo[$i]);
				$file_name = $file_name[1];

				$file_name = explode('.ini', $file_name);
				$file_name = $file_name[0];
				
				if(!empty($file_name)) {
					$languages[$file_name] = $file_name;
					$languages[] =$file_name;
				}			
			}


			return json_encode($languages);
		}else{
			$id = (int)$id;
			$dir_name = ROOT.'/languages/';
			$token = $dir_name;
			$dir_name .= '*.ini';
			$glo = glob($dir_name);

			$languages = array();

			for($i = 0; $i < sizeof($glo); $i++){
				$file_name = explode($token, $glo[$i]);
				$file_name = $file_name[1];

				$file_name = explode('.ini', $file_name);
				$file_name = $file_name[0];
				
				if(!empty($file_name)) {
					$languages[$file_name] = $file_name;
					$languages[] =$file_name;
				}			
			}

			if(isset($languages[$id])){
				return $languages[$id];
			}else{
				return "en";
			}

			return false;
		}
		
		return false;
	}
		
	protected function post(){
		header("HTTP/1.1 405 METHOD NOT ALLOWED");
		return false;
	}

	protected function put($id){
		
		//echo "ID".$id;
		if($id !== null){
			$idi = intval($id);		
			$lang = $this->get($idi); 
			if($lang !== false){		
				

				$file_name = "".ROOT."/languages/".$lang.".ini";
				
				$file = fopen($file_name, "r");

				$line = "";
				while($data = fread($file, filesize($file_name))){
					$line .= $data;
				}

				fclose($file);

				$en = array();
				$en = explode(';', $line);
				$words = array();
				for($i = 0; $i < sizeof($en); $i++){
					$pieces = explode('=', $en[$i]);

					$words[$pieces[0]] = ucfirst(strtolower($pieces[1]));
				}

				//print_r("words" => $words);
				//$r['words'] = $words;
				$words = json_encode( $words);
				//echo $words;
				return $words;
			}
		}	


		return false;
	}

	public function delete($id){
		header("HTTP/1.1 405 METHOD NOT ALLOWED");
		return false;
	}
}
?>