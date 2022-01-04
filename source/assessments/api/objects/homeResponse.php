<?php
    class HomeResponse{
        private $conn;
        private $table_name = 'homeResponses';
    
        public $id;
        public $sessionDate;
        public $clientId;
        public $staffId;
        public $affirm;
        public $responseDate;
        public $sentDate;
        public $notes;
        public $notesDate;
        public $link;
    
        public function __construct($db){
            $this->conn = $db;
        }

        function read($programId){
            if($programId) {
                $whereClause = 'WHERE programId = :programId';
            } else {
                $whereClause = '';
            }
            $query = "SELECT * FROM $this->table_name JOIN users ON staffId = users.id $whereClause ORDER BY sessionDate DESC";
            $stmt = $this->conn->prepare($query);
            if($programId) {
                $stmt->bindParam(':programId', $programId);
            }
            $stmt->execute();
            return $stmt;
        }

        function responsesByStaff($staffId) {
            $query = "SELECT * FROM $this->table_name WHERE staffId=:staffId ORDER BY sessionDate DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':staffId', $staffId);
            $stmt->execute();
            return $stmt;
        }

        function create(){
            $this->link = randomPassword();
            $query = "INSERT INTO $this->table_name SET sessionDate=:sessionDate, clientId=:clientId, staffId=:staffId, link=:link";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':sessionDate', $this->sessionDate);
            $stmt->bindParam(':clientId', $this->clientId);
            $stmt->bindParam(':staffId', $this->staffId);
            $stmt->bindParam(':link', $this->link);
            if($stmt->execute()){
                return $this->link;
            }
            return false;
        }

        function readOne(){
            $query = "SELECT * FROM $this->table_name WHERE link=? LIMIT 0,1";
            $stmt = $this->conn->prepare( $query );
            $stmt->bindParam(1, $this->link);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->sessionDate = $row['sessionDate'];
            $this->clientId = $row['clientId'];
            $this->staffId = $row['staffId'];
            $this->affirm = $row['affirm'];
            $this->sentDate = $row['sentDate'];
            $this->responseDate = $row['responseDate'];
            $this->notes = $row['notes'];
            $this->notesDate = $row['notesDate'];
        }

        function update(){
            $updateVars = '';
            foreach($this as $key => $value) {
                if($key !=='link' && $key !=='id' && $value !== null && $key !== 'conn' && $key !== 'table_name') {
                    if ($updateVars !== '') {
                        $updateVars .= ', ';
                    }
                    $updateVars .= "$key = :$key";
                    
                    switch($key) {
                        case 'notes':
                            $updateVars .= ', notesDate=NOW()';
                            break;
                        
                        case 'affirm':
                            $updateVars .= ', responseDate=NOW()';
                            break;
                    }
                }
            }

            $query = "UPDATE $this->table_name SET $updateVars WHERE link=:link";
            $stmt = $this->conn->prepare($query);
            $debug = '';
            foreach($this as $key => $value) {
                if($key !=='link' && $key !=='id' && $value !== null && $key !== 'conn' && $key !== 'table_name') {
                    $stmt->bindParam(":$key", $this->$key);
                }
            }

            $stmt->bindParam(':link', $this->link);
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