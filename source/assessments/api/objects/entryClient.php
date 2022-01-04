<?php
    class EntryClient{
        private $conn;
        private $table_name = 'entryClients';
    
        public $id;
        public $color;
        public $firstName;
        public $lastName;
        public $number;
    
        public function __construct($db){
            $this->conn = $db;
        }

        function read(){
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        function create(){
            $query = "INSERT INTO $this->table_name SET color = :color, firstName = :firstName, lastname = :lastName, number = :number";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':color', $this->color);
            $stmt->bindParam(':firstName', $this->firstName);
            $stmt->bindParam(':lastName', $this->lastName);
            $stmt->bindParam(':number', $this->number);
            if($stmt->execute()){
                return true;
            }
            return false;
        }

        function readOne(){
            $query = "SELECT * FROM $this->table_name WHERE id = ? LIMIT 0,1";
            $stmt = $this->conn->prepare( $query );
            $stmt->bindParam(1, $this->id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->firstName = $row['firstName'];
            $this->lastName = $row['lastName'];
            $this->color = $row['color'];
            $this->number = $row['number'];
        }

        function update(){
            $updateVars = '';
            foreach($this as $key => $value) {
                if($key !=='id' && $value !== null && $key !== 'conn' && $key !== 'table_name') {
                    if ($updateVars !== '') {
                        $updateVars .= ', ';
                    }
                    $updateVars .= "$key = :$key";
                }
            }

            $query = "UPDATE $this->table_name SET $updateVars WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $debug = '';
            foreach($this as $key => $value) {
                if($key !=='id' && $value !== null && $key !== 'conn' && $key !== 'table_name') {
                    $stmt->bindParam(":$key", $this->$key);
                }
            }
            $stmt->bindParam(':id', $this->id);
            if($stmt->execute()){
                return true;
            }
            return false;
        }
        
        function delete(){
            $query = "DELETE FROM $this->table_name WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);
            if($stmt->execute()){
                return true;
            }
            return false;
        }
    }
?>