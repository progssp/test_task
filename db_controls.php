<?php
    class DatabaseControls {
        private $host,$user, $password, $db, $conn;

        public function __construct(){
            $this->host = "localhost";
            $this->user = "user";
            $this->password = "useruser";
            $this->db = "task_db";
        }

        public function connect(){
            $this->conn = new mysqli(
                $this->host,
                $this->user,
                $this->password,
                $this->db
            );
            if($this->conn->connect_errno){
                die('could not connect to database');
            }
            return $this->conn;
        }

        public function close(){
            try {
                $this->conn->close();
            } 
            catch (\Exception $e) {
            
            }
        }

        public function save_query($query){
            $conn = $this->connect();
            try {
                if($conn->query($query)){}
                else{}
                if ($conn->errno) {
                    echo "Could not update table: " . $conn->error;
                 }
                $conn->close();
            } 
            catch (\Exception $th) {
                echo $th;
            }
        }

        public function select_query($query){
            $response_obj = new \stdClass;
            $response_arr = [];
            $conn = $this->connect();
            try {
                $result = $conn->query($query);
                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        $response_arr[] = $row;
                    }
                    return json_encode($response_arr);
                }
                $conn->close();
            } 
            catch (\Exception $th) {
                echo $th;
            }
        }

        public function select_max($query){
            $response_vol = 0;
            $conn = $this->connect();
            try {
                $result = $conn->query($query);
                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        $response_vol = $row['volume'];
                    }
                    return $response_vol;
                }
                $conn->close();
            } 
            catch (\Exception $th) {
                echo $th;
            }
        }

        

    }
?>