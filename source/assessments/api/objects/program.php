<?php
    class Program{
        private $conn;
        private $table_name = 'programs';
    
        public $id;
        public $name;
    
        public function __construct($db) {
            $this->conn = $db;
        }

        function read() {
            $query = "SELECT * FROM $this->table_name ORDER BY name ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        function create() {
            $query = "INSERT INTO $this->table_name SET name = :name";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $this->name);
            if($stmt->execute()){
                return true;
            }
            return false;
        }

        function readOne($ext = false) {
            $query = "SELECT * FROM $this->table_name WHERE id = ? LIMIT 0,1";
            $stmt = $this->conn->prepare( $query );
            $stmt->bindParam(1, $this->id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->name = $row['name'];
        }

        function update() {
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
            if($stmt->execute()) {
                return true;
            }
            return false;
        }
        
        function delete() {
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