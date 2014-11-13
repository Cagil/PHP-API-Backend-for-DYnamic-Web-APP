<?php 
/**
* ControllerFactory
*
* @uses Creator for the Controller classes.    
*
* @author   Cagil Ozdemirag & Thamer Alshammari   
*/
class ControllerFactory{
	
    /**
     * createController
     * 
     * @param string	$type   type of the controller to be createad..
     * @param array 	$bundle parameters required for the controllers' contructor.
     *
     * @access public
     * @static
     *
     * @return instance of a requested controller.
     * @return false, if there is an error with the requested controller.
     */
	public static function createController($type, $bundle){
		$type = strtolower(trim($type));
          if(empty($type)) return false;
          
		$className = $type.'Controller';
		include_once __DIR__.'/controllers/'.$className.'.php';

		$handle = null;
		$handle = new $className($bundle['request_method'], $bundle['uri_data'], $bundle['body_data']);
		if($handle === null || $handle === false)	return false;

		return $handle;
	}
}
?>