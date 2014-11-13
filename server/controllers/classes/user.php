<?php
class user{
	private $id;
	private $username;
	private $email;
	private $student_number;
	private $isPaid;
	private $isAdmin;

	public function __construct($data){
		$this->id = $data['id'];
		$this->username = $data['username'];
		$this->email = $data['email'];
		$this->student_number = $data['student_number'];
		$this->isPaid = $data['isPaid'];
		$this->isAdmin = $data['isAdmin'];
	}

	public function getId(){
		return $this->id;
	}	

	public function getUsername(){
		return $this->username;
	}

	public function getEmail(){
		return $this->email;
	}

	public function getStudentNumber(){
		return $this->student_number;
	}

	public function getIsPaid(){
		return $this->isPaid;
	}

	public function getIsAdmin(){
		return $this->isAdmin;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function setUsername($uname){
		$this->username = $uname;
	}

	public function setEmail($email){
		$this->email = $email;
	}

	public function setStudentNumber($num){
		$this->student_number = $num;
	}

	public function setIsPaid($ispaid){
		$this->isPaid = $ispaid;
	}

	public function setIsAdmin($isadmin){
		$this->isAdmin = $isadmin;
	}


}
?>