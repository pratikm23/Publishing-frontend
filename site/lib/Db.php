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
		// $dbConnection = new PDO("mysql:host=" . DBHOST . ";" .
  //             "port=" . '3306' . ";" .
  //             "dbname=" . $this->database . ";charset=utf8;"
  //             , $this->userName
  //             , $this->password
  //           );             
            
  //            $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //            foreach($dbConnection->query('SELECT * FROM icn_store') as $row) {
  //   			echo $row['st_id']; //etc...
		// 	}
           // print_r( $dbConnection );
           // exit;
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