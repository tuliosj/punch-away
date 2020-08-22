<?php

class WorkDay {
    private $date;
    private $employees_id;
    private $month;
    private $start;
    private $lunch_start;
    private $lunch_end;
    private $end;
    private $total;

    public function __construct($id) {
        $database = new Database();
        $dbSet = $database->dbSet();
        $this->conn = $dbSet;

        $this->employees_id = $id;
        $this->month = gmdate("Y/m", $this->today());
    }

    public function insert() {
        try{
            $stmt = $this->conn->prepare("INSERT INTO `work_days`(`date`, `employees_id`, `month`) VALUES(:date, :employees_id, :month)");
            
            $stmt->bindParam(":date", $this->date);
            $stmt->bindParam(":employees_id", $this->employees_id);
            $stmt->bindParam(":month", $this->month);
            $stmt->execute();
            return 1;
        }catch(PDOException $e){
            return 0; 
        }
    }

    public function index() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `companies_id` WHERE employees_id = :employees_id AND month = :month");

            $stmt->bindParam(":employees_id", $this->employees_id);
            $stmt->bindParam(":month", $this->month);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function today() {
        try {
            $stmt = $this->conn->prepare("SELECT `companies_id` FROM `employees` WHERE id = :employees_id");

            $stmt->bindParam(":employees_id", $this->employees_id);
            $stmt->execute();
            $company_id = $stmt->fetch(PDO::FETCH_OBJ);

            $stmt = $this->conn->prepare("SELECT `gmt_difference` FROM `companies` WHERE id = :company_id");

            $stmt->bindParam(":employees_id", $this->employees_id);
            $stmt->execute();
            $timezone = $stmt->fetch(PDO::FETCH_OBJ);

            return time() + 3600*$timezone;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function generateMonth() {
        $today = $this->today();
        $day = (int) gmdate("j",$today);
        while($day>0) {
            $stmt = $this->conn->prepare("SELECT * FROM `work_days` WHERE employees_id = :employees_id" and month = :month);
            $day--;
        }
    }
}