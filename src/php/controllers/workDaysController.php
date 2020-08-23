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

    public function update() {
        try{
            $stmt = $this->conn->prepare("UPDATE `work_days` SET `start` = :start, `lunch_start` = :lunch_start, `lunch_end` = :lunch_end, `end` = :end  WHERE `date` = :date AND `employees_id` = :employees_id;");
            
            $stmt->bindParam(":date", $this->date);
            $stmt->bindParam(":employees_id", $this->employees_id);
            $stmt->bindParam(":start", $this->start);
            $stmt->bindParam(":lunch_start", $this->lunch_start);
            $stmt->bindParam(":lunch_end", $this->lunch_end);
            $stmt->bindParam(":end", $this->end);
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

    public function format_from_12hform($hours, $minutes, $seconds, $pm) {
        if ($hours=='12') {
            $hours = '00';
        }
        return (sprintf("%02d",$hours)+(12*$pm)).':'.sprintf("%02d",$minutes).':'.sprintf("%02d",$seconds);
    }

    public function format_from_24hform($hours, $minutes, $seconds) {
        return sprintf("%02d",$hours).':'.sprintf("%02d",$minutes).':'.sprintf("%02d",$seconds);
    }

    public function set_date($date) {
        $this->date = $date;
    }
    
    public function get_month() {
        $date = explode('/', $this->month);
        $dateObj   = DateTime::createFromFormat('!m', $date[1]);
        return strtolower($dateObj->format('F ')) . $date[0];
    }

    public function set_month($month) {
        $stmt = $this->month_index();
        while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            if($row->month == $month) {
                $this->month = $month;
            }
        } 
    }

    public function set_start($start) {
        $this->start = $start;
    }

    public function set_lunch_start($lunch_start) {
        $this->lunch_start = $lunch_start;
    }

    public function set_lunch_end($lunch_end) {
        $this->lunch_end = $lunch_end;
    }

    public function set_end($end) {
        $this->end = $end;
    }
}