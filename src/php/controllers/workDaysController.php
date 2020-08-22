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

    public function view() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `work_days` WHERE `employees_id` = :employees_id AND `date` = :date");

            $stmt->bindParam(":employees_id", $this->employees_id);
            $stmt->bindParam(":date", $this->date);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function index() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `work_days` WHERE `employees_id` = :employees_id AND `month` = :month");

            $stmt->bindParam(":employees_id", $this->employees_id);
            $stmt->bindParam(":month", $this->month);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function month_index() {
        try {
            $stmt = $this->conn->prepare("SELECT DISTINCT `month` FROM `work_days` WHERE `employees_id` = :employees_id ORDER BY `month` DESC");

            $stmt->bindParam(":employees_id", $this->employees_id);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function today() {
        try {
            $stmt = $this->conn->prepare("SELECT `companies_id` FROM `employees` WHERE `id` = :employees_id");

            $stmt->bindValue(":employees_id", $this->employees_id);
            $stmt->execute();
            $employee = $stmt->fetch(PDO::FETCH_OBJ);

            $stmt = $this->conn->prepare("SELECT `gmt_difference` FROM `companies` WHERE `id` = :company_id");

            $stmt->bindParam(":company_id", strval($employee->companies_id));
            $stmt->execute();
            $timezone = $stmt->fetch(PDO::FETCH_OBJ);

            return time() + 3600*$timezone->gmt_difference;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function generate_month() {
        $today = $this->today();
        $date = gmdate("Y-m-", $today);
        $day = (int) gmdate("j", $today);
        while($day>0) {
            $stmt = $this->conn->prepare("SELECT `date` FROM `work_days` WHERE `date` = :date AND `employees_id` = :employees_id");
            
            $check_date = $date . sprintf("%02d", $day);
            $stmt->bindParam(":date", $check_date);
            $stmt->bindValue(":employees_id", $this->employees_id);
            $stmt->execute();
            if (!$stmt->fetch(PDO::FETCH_OBJ)) {
                $stmt = $this->conn->prepare("INSERT INTO `work_days` (`date`, `employees_id`, `month`) VALUES (:date, :employees_id, :month);");
                

                $stmt->bindParam(":date", $check_date);
                $stmt->bindValue(":employees_id", $this->employees_id);
                $stmt->bindParam(":month", $this->month);
                $stmt->execute();
            }
            $day--;
        }
        return $day;
    }

    public function format_date($date) {
        $stmt = $this->conn->prepare("SELECT `endianness` FROM `employees` WHERE `id` = :employees_id");
        
        $stmt->bindValue(":employees_id", $this->employees_id);
        $stmt->execute();
        $endianness = $stmt->fetch(PDO::FETCH_OBJ)->endianness;

        $date = str_replace('-','/',$date);
        if($endianness == 'L') {
            $split = explode('/',$date);
            $date = $split[2] . '/' . $split[1] . '/' . $split[0];
        } else if ($endianness == 'M') {
            $split = explode('/',$date);
            $date = $split[1] . '/' . $split[2] . '/' . $split[0];
        }

        return $date;
    }

    public function get_24hclock() {
        try {
            $stmt = $this->conn->prepare("SELECT `24hclock` AS `_24hclock` FROM `employees` WHERE `id` = :employees_id");
            
            $stmt->bindValue(":employees_id", $this->employees_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ)->_24hclock;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }
    

    public function format_clock($time) {
        $_24hclock = $this->get_24hclock();

        if($_24hclock == '1') {
            $split = explode(':',$time);
            $time = $split[0] . ':' . $split[1];
        } else {
            $split = explode(':',$time);
            $time = (int)$split[0]%12;
            $time = ($time == 0 ? '12' : $time) . ':' . $split[1] . ((int)$split[0]/12 > 1 ? ' PM' : ' AM');
        }

        return $time;
    }

    public function set_date($date) {
        $this->date = $date;
    }

    public function set_month($month) {
        $stmt = $this->month_index();
        while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            if($row->month == $month) {
                $this->month = $month;
            }
        } 
    }
    
    public function get_month() {
        $date = explode('/', $this->month);
        $dateObj   = DateTime::createFromFormat('!m', $date[1]);
        return strtolower($dateObj->format('F ')) . $date[0];
    }
}