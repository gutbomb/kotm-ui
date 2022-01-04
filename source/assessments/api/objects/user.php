<?php
    class User{
        private $conn;
        private $table_name = 'users';
    
        public $id;
        public $email;
        public $password;
        public $firstName;
        public $lastName;
        public $passwordMustChange;
        public $newPassword;
        public $currentAttest;
        public $userLevel;
        public $lastLogin;
        public $created;
        public $status;
        public $statusDate;
        public $enabled;
        public $programId;
        public $programName;
    
        public function __construct($db) {
            $this->conn = $db;
        }

        function read($programId) {
            if($programId) {
                $whereClause = 'WHERE programId = :programId';
            } else {
                $whereClause = '';
            }
            $query = "SELECT a.id, a.enabled, a.firstName, a.lastName, a.email, a.userLevel, a.lastLogin, a.created, (SELECT b.date FROM staffAttest AS b WHERE b.userId = a.id ORDER BY b.date DESC LIMIT 1) AS statusDate, (SELECT c.status FROM staffAttest AS c WHERE c.userId = a.id ORDER BY c.date DESC LIMIT 1) AS status, programs.name AS programName, programId FROM $this->table_name AS a LEFT JOIN programs ON programs.id = programId $whereClause ORDER BY a.lastName ASC, a.firstName ASC";
            $stmt = $this->conn->prepare($query);
            if($programId) {
                $stmt->bindParam(':programId', $programId);
            }
            $stmt->execute();
            return $stmt;
        }

        function create() {
            $query = "SELECT COUNT(id) AS idCount FROM $this->table_name WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['idCount']) {
                return false;
            } else {
                $query = "INSERT INTO $this->table_name SET email = :email, password = :password, firstName = :firstName, lastname = :lastName, programId = :programId";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':email', $this->email);
                $stmt->bindParam(':password', password_hash($this->password, PASSWORD_DEFAULT));
                $stmt->bindParam(':firstName', $this->firstName);
                $stmt->bindParam(':lastName', $this->lastName);
                $stmt->bindParam(':programId', $this->programId);
                if($stmt->execute()){
                    return true;
                }
                return false;
            }
        }

        function readOne($ext = false) {
            $query = "SELECT users.id AS id, firstName, lastName, email, passwordMustChange, userLevel, lastLogin, created, enabled, programs.name AS programName, programId FROM $this->table_name LEFT JOIN programs ON programs.id = programId WHERE users.id = ? LIMIT 0,1";
            $stmt = $this->conn->prepare( $query );
            $stmt->bindParam(1, $this->id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->firstName = $row['firstName'];
            $this->lastName = $row['lastName'];
            $this->email = $row['email'];
            $this->userLevel = $row['userLevel'];
            $this->lastLogin = $row['lastLogin'];
            $this->created = $row['created'];
            $this->enabled = boolval($row['enabled']);
            $this->programName = $row['programName'];
            $this->programId = $row['programId'];
            $this->passwordMustChange = $row['passwordMustChange'];
            $query = "SELECT COUNT(id) AS idCount FROM staffAttest WHERE userId = ? AND DATE(date) = DATE(NOW())";
            $stmt = $this->conn->prepare( $query );
            $stmt->bindParam(1, $this->id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->currentAttest = $row['idCount'];
            if ($ext) {
                unset($this->password);
            }
        }

        function update() {
            $updateVars = '';
            foreach($this as $key => $value) {
                if($key !=='id' && $value !== null && $key !== 'conn' && $key !== 'table_name') {
                    if ($updateVars !== '') {
                        $updateVars .= ', ';
                    }
                    if ($key === 'password') {
                        $updateVars .= 'password =:password, passwordMustChange = 0';
                    } else {
                        $updateVars .= "$key = :$key";
                    }
                }
            }

            $query = "UPDATE $this->table_name SET $updateVars WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $debug = '';
            foreach($this as $key => $value) {
                if($key !=='id' && $value !== null && $key !== 'conn' && $key !== 'table_name') {
                    if ($key === 'password') {
                        $stmt->bindParam('password', password_hash($this->password, PASSWORD_DEFAULT));
                    } else {
                        $stmt->bindParam(":$key", $this->$key);
                    }
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

        function auth() {
            $query = "SELECT * FROM $this->table_name WHERE email = :email AND enabled = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $this->email);
            if($stmt->execute()){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($this->password, $row['password'])) {
                    unset($this->password);
                    $this->firstName = $row['firstName'];
                    $this->lastName = $row['lastName'];
                    $this->userLevel = $row['userLevel'];
                    $this->email = $row['email'];
                    $this->id = $row['id'];
                    $this->programId = $row['programId'];
                    $query = "UPDATE $this->table_name SET lastLogin = NOW() WHERE id = :id";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':id', $this->id);
                    $stmt->execute();
                    return true;
                }
            }
            return false;
        }

        function resetPassword() {
            $query = "SELECT COUNT(id) AS idCount FROM $this->table_name WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['idCount']) {
                $newPassword = randomPassword();
                $query = "UPDATE $this->table_name SET password = :password, passwordMustChange = 1 WHERE email = :email";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':password', password_hash($newPassword, PASSWORD_DEFAULT));
                $stmt->bindParam(':email', $this->email);
                if($stmt->execute()){
                    return $newPassword;
                }
                return false;
            } else {
                return 'notfound';
            }
            return false;
        }

        function changePassword() {
            $query = "SELECT password FROM $this->table_name WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $this->email);
            if($stmt->execute()){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($this->password, $row['password'])) {
                    $newQuery = "UPDATE $this->table_name SET password = :newPassword, passwordMustChange = 0 WHERE email = :email";
                    $newStmt = $this->conn->prepare($newQuery);
                    $newStmt->bindParam(':newPassword', password_hash($this->newPassword, PASSWORD_DEFAULT));
                    $newStmt->bindParam(':email', $this->email);
                    if($newStmt->execute()) {
                        return true;
                    }
                } else {
                    return false;
                }
            }
            return false;
        }

        function staffAttest($affirm) {
            $query = "INSERT INTO staffAttest SET userId = :userId, status = :affirm";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':userId', $this->id);
            $stmt->bindParam(':affirm', $affirm);
            if($stmt->execute()){
                return true;
            }
            return false;
        }

        function getAttestHistory($userId) {
            $query = 'SELECT date, status AS historicalStatus FROM staffAttest WHERE userId = :userId ORDER BY date DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            return $stmt;
        }
    }
?>