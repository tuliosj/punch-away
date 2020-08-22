<?php 

include_once('./php/database.php');
include_once('./php/controllers/companyController.php');
include_once('./php/controllers/employeeController.php');

$company = new Company();

if(isset($_POST['sign-up'])) {
    $employee = new Employee();

    if ($_POST['password'] == $_POST['confirm-password']) {

        $employee->set_email($_POST['email']);
        if (!$employee->view_email()) {       
            $employee->set_name($_POST['name']);
            $employee->set_password($_POST['password']);
            $employee->set_24hclock($_POST['clock']);
            $employee->set_endianness($_POST['date']);
            $employee->set_companies_id($_POST['company']);
            
            $success = $employee->insert();
        } else {
            $success = -2;
        }
    } else {
        $success = -1;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" sizes="32x32" href="./img/favicon-32x32.png" />
    <title>sign up</title>
    <link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&display=swap" rel="stylesheet" />

    <script>
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty("--vh", `${vh}px`);
    const vw = window.innerWidth * 0.01;
    document.documentElement.style.setProperty("--vw", `${vw}px`);
    </script>

    <link rel="stylesheet" href="./css/styles.css" />
</head>

<body>
    <main>
        <header>
            <img src="./img/punch-away.png" alt="punch away" class="logo" />
            <div class="navigation">
                <a href="about.php">about</a>
                <h1>signing up</h1>
            </div>
        </header>
        <form method="post" action="sign-up.php" enctype="multipart/form-data">
            <div class="form">
                <?php 
                if(isset($success)) {
                    echo '<div class="alert">';
                    if($success == 1) {
                        echo 'sucess! <a href="log-in.php">now log in.</a>';
                    } else if ($success == -1) {
                        echo 'error, passwords don\'t match!';
                    } else if ($success == -2) {
                        echo 'error, email already exists!';
                    } else {
                        echo 'error, please try again later.';
                    }
                    echo '</div>';
                }
                ?>
                <h2>account</h2>
                <div class="input-group">
                    <label for="company">🏢 company</label><select name="company" id="company">
                        <?php 
                        $companies = $company->index();
                        while ($row = $companies->fetch(PDO::FETCH_OBJ)) {
                            echo "<option value='$row->id'>$row->name</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="input-group">
                    <label for="name">👷 your name</label>
                    <input type="text" name="name" id="name" required />
                </div>
                <div class="input-group">
                    <label for="email">📧 email</label>
                    <input type="email" name="email" id="email" required />
                </div>
                <div class="input-group">
                    <label for="password">🔑 password</label>
                    <input type="password" name="password" id="password" required />
                </div>
                <div class="input-group">
                    <label for="confirm-password">🔑 confirm password</label>
                    <input type="password" name="confirm-password" id="confirm-password" required />
                </div>
                <h2>preferences</h2>
                <div class="input-group">
                    <label for="date">📅 a perfect date</label>
                    <select name="date" id="date">
                        <option value="L">dd/mm/yyyy</option>
                        <option value="M">mm/dd/yyyy</option>
                        <option value="B">yyyy/mm/dd</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="clock">⏰ 24h or 12h</label>
                    <select name="clock" id="clock">
                        <option value="0">11:59 p.m.</option>
                        <option value="1">23:59</option>
                    </select>
                </div>
                <div class="input-group">
                    <button name="sign-up" type="submit" class="send">👉</button>
                </div>
            </div>
        </form>
    </main>
</body>

</html>