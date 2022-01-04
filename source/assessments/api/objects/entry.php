<?php
    class Entry{
        private $conn;
        private $table_name = 'entries';
    
        public $id;
        public $color;
        public $number;
        public $status;
        public $date;
    
        public function __construct($db){
            $this->conn = $db;
        }

        function read(){
            $query = "SELECT * FROM $this->table_name ORDER BY date DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        function getLatestDenial($color, $number){
            $query = "SELECT * FROM $this->table_name WHERE status = 0 AND color = :color AND number = :number ORDER BY date DESC LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':color', $color);
            $stmt->bindParam(':number', $number);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->date = $row['date'];
        }

        function create(){
            $query = "INSERT INTO $this->table_name SET color = :color, number = :number, status = :status";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':color', $this->color);
            $stmt->bindParam(':number', $this->number);
            $stmt->bindParam(':status', $this->status);
            if($stmt->execute()){
                return true;
            }
            return false;
        }

        function readOne(){
            $query = "SELECT * FROM $this->table_name WHERE color=:color AND number=:number ORDER BY date DESC";
            $stmt = $this->conn->prepare( $query );
            $stmt->bindParam(':color', $this->color);
            $stmt->bindParam(':number', $this->number);
            $stmt->execute();
            return $stmt;
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

            $query = "UPDATE $this->table_name SET $updateVars WHERE id=:id";
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
            $query = "DELETE FROM $this->table_name WHERE id=?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);
            if($stmt->execute()){
                return true;
            }
            return false;
        }
    }
?>