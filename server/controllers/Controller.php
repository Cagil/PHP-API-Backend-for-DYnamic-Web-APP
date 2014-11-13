<?php 
session_start();
abstract class Controller{
	protected $HTTP_METHOD;
	protected $URI_DATA;
	protected $BODY_DATA;
	protected $db;

	abstract public function call();

	abstract protected function get($id=null);
	abstract protected function post();
	abstract protected function put($id);
	abstract protected function delete($id);
}

 ?>