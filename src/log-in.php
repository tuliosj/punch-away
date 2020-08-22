<?php 

include_once('./php/database.php');
include_once('./php/controllers/employeeController.php');

if(isset($_POST['log-in'])) {
    $employee = new Employee();

    $employee->set_email($_POST['email']);
    $employee->set_password($_POST['password']);

    $success = $employee->log_in();
    if (isset($success->id)) {
        session_start();
        header('Location: index.php');
        $_SESSION['id'] = $success->id;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" sizes="32x32" href="./img/favicon-32x32.png" />
    <title>log in</title>
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
                <h1>logging in</h1>
            </div>
        </header>
        <form method="post" action="log-in.php" enctype="multipart/form-data">
            <div class="form">
                <?php 
                if(isset($success)) {
                    echo '<div class="alert" style="margin-bottom:4rem;">';
                    if($success == 0) {
                        echo 'error, email and password don\'t match!';
                    }
                    echo '</div>';
                }
                ?>
                <div class="input-group">
                    <label for="email">📧 email</label>
                    <input type="email" name="email" id="email" required />
                </div>
                <div class="input-group">
                    <label for="password">🔑 password</label>
                    <input type="password" name="password" id="password" required />
                </div>
                <div class="input-group">
                    <button name="log-in" type="submit" class="send">👉</button>
                </div>
            </div>
        </form>
    </main>
</body>

</html>