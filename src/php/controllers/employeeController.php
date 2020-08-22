<?php

class Employee {
    private $id;
    private $email;
    private $name;
    private $password;
    private $_24hclock;
    private $endianness;
    private $companies_id;
    private $daily_minutes;
    private $admin;

    public function __construct() {
        $database = new Database();
        $dbSet = $database->dbSet();
        $this->conn = $dbSet;
    }

    public function insert() {
        try{
            $stmt = $this->conn->prepare("INSERT INTO `employees`(`email`,`name`,`password`,`24hclock`,`endianness`,`companies_id`) VALUES(:email, :name,:password,:_24hclock,:endianness,:companies_id)");
            
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":password", $this->password);
            $stmt->bindParam(":_24hclock", $this->_24hclock);
            $stmt->bindParam(":endianness", $this->endianness);
            $stmt->bindParam(":companies_id", $this->companies_id);
            $stmt->execute();
            return "sucess! <a href='log-in.php'>now log in.</a>";
        }catch(PDOException $e){
            return "error, please try again later."; 
        }
    }

    public function log_in(){
        try{
            $stmt = $this->conn->prepare("SELECT * FROM `employees` WHERE `email` = :email AND `password` = :password");

            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":password", $this->password);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        }catch(PDOException $e){
            return 0;
        }
    }

    public function change_password($new_password){
        try{
            $stmt = $this->conn->prepare("SELECT * FROM `employees` WHERE `id` = :id AND `password` = :password");

            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":password", $this->password);
            $stmt->execute();

            if($stmt->fetch(PDO::FETCH_OBJ)) {
                $this->set_password($new_password);
                
                $stmt = $this->conn->prepare("UPDATE `employees` SET `password` = :password WHERE `id` = :id;");
    
                $stmt->bindParam(":id", $this->id);
                $stmt->bindParam(":password", $this->password);
                $stmt->execute();
                return "success!";
            } else {
                return "error, old password doesn't match.";
            }
        }catch(PDOException $e){
            return "error, please try again later.";
        }
    }
    

    public function change_preferences(){
        try{
            $stmt = $this->conn->prepare("UPDATE `employees` SET `endianness` = :endianness, `24hclock` = :_24hclock WHERE `id` = :id;");

            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":endianness", $this->endianness);
            $stmt->bindParam(":_24hclock", $this->_24hclock);
            $stmt->execute();
            return "success!";
        }catch(PDOException $e){
            return "error, please try again later.";
        }
    }

    public function view_email(){
        try{
            $stmt = $this->conn->prepare("SELECT * FROM `employees` WHERE `email` = :email");

            $stmt->bindParam(":email", $this->email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        }catch(PDOException $e){
            return 0;
        }
    }
    
    public function delete($id){
        try{
            $stmt = $this->conn->prepare("DELETE FROM `employees` WHERE `id` = :id");

            $stmt->bindParam(":id", $this->id);
            $stmt->execute();
            return 1;
        }catch(PDOException $e){
            return 0;
        }
    }

    public function set_id($value) {
        $this->id = $value;
    }

    public function set_email($value) {
        $this->email = $value;
    }

    public function set_name($value) {
        $this->name = $value;
    }

    public function set_password($value) {
        $this->password = sha1($value);
    }

    public function set_24hclock($value) {
        $this->_24hclock = $value;
    }

    public function set_endianness($value) {
        $this->endianness = $value;
    }

    public function set_companies_id($value) {
        $this->companies_id = $value;
    }

    public function set_daily_minutes($value) {
        $this->daily_minutes = $value;
    }

    public function set_admin($value) {
        $this->admin = $value;
    }
}