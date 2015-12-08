<?php
// Database Class
class Db{
	public function __construct($userName, $password, $database){
		//Declaring Member variables -- localhost
		$this->userName = $userName;
		$this->password = $password;
		$this->database = $database;
	}
	// Get Connection resource object
	public function getConnection(){
		//function getConnection() opens here
		
		$con = new mysqli(DBHOST,$this->userName,$this->password, $this->database);
		if($con->connect_errno > 0){
			die('Unable to connect to database [' . $con->connect_error . ']');
		}
		return $con;	
	}

	public function execute($dbCon, $query){
		return $result = $dbCon->query($query);
	}
	
	public function getData($result){
		if($this->getRecordsCount($result) > 0 ){
			return $result->fetch_assoc();
		}else{
			return array();
		}
	}
	
	public function getRecordsCount($result){
		return $result->num_rows;
	}
}
// class 'db' ends here
?>