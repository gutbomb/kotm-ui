<?php
    class HomeClient{
        private $conn;
        private $table_name = 'homeClients';
    
        public $id;
        public $email;
        public $firstName;
        public $lastName;
        public $language;
    
        public function __construct($db){
            $this->conn = $db;
        }

        function read(){
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        function clientsByStaff($staffId) {
            $query = "SELECT DISTINCT(clientId) AS id, homeClients.firstName, homeClients.lastName, email, language FROM homeClients JOIN homeResponses ON clientId = homeClients.id WHERE staffId = :staffId ORDER BY lastName ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':staffId', $staffId);
            $stmt->execute();
            return $stmt;
        }

        function create(){
            $query = "INSERT INTO $this->table_name SET email = :email, firstName = :firstName, lastname = :lastName, language = :language";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':firstName', $this->firstName);
            $stmt->bindParam(':lastName', $this->lastName);
            $stmt->bindParam(':language', $this->language);
            if($stmt->execute()){
                return $this->conn->lastInsertId();
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
            $this->email = $row['email'];
            $this->language = $row['language'];
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