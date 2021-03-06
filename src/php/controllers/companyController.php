<?php

class Company {
    private $id;
    private $name;
    private $gmt_difference;

    public function __construct() {
        $database = new Database();
        $dbSet = $database->dbSet();
        $this->conn = $dbSet;
    }

    public function insert() {
        try{
            $stmt = $this->conn->prepare("INSERT INTO `companies`(`name`,`gmt_difference`) VALUES(:name,:gmt_difference)");
            
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":gmt_difference", $this->gmt_difference);
            $stmt->execute();
            return 1;
        }catch(PDOException $e){
            return 0; 
        }
    }

    public function index() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `companies` WHERE 1");

            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }
    
    public function delete($id){
        try{
            $stmt = $this->conn->prepare("DELETE FROM `companies` WHERE `id` = :id");

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

    public function set_name($value) {
        $this->name = $value;
    }
}