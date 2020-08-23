<?php

include_once('./php/database.php');
include_once('./php/controllers/employeeController.php');
include_once('./php/controllers/workDaysController.php');

session_start();

if(isset($_SESSION)) {

    if(isset($_POST['password'])) {
        if ($_POST['new-password'] == $_POST['confirm-password']) {
            $employee = new Employee();
            $employee->set_id($_SESSION['id']);
            $employee->set_password($_POST['old-password']);
            $success = $employee->change_password($_POST['new-password']);
        } else {
            $success = "error, passwords don't match!";
        }
    }
    
    if(isset($_POST['preferences'])) {
        $employee = new Employee();
        $employee->set_id($_SESSION['id']);
        $employee->set_endianness($_POST['date']);
        $employee->set_24hclock($_POST['clock']);
        $success = $employee->change_preferences();
    }

    $work_days = new WorkDay($_SESSION['id']);
    if (isset($_GET['month'])) {
        $work_days->set_month($_GET['month']);
    }
    $work_days->generate_month();
    

    if(isset($_POST['date_change'])) {
        $work_days->set_date($_POST['edited-date']);
        if($_POST['_24hclock'] == '0') {
            $work_days->set_start($work_days->format_from_12hform($_POST['start_hours'],$_POST['start_minutes'],$_POST['start_seconds'],$_POST['start_ampm']));
            $work_days->set_lunch_start($work_days->format_from_12hform($_POST['lunch_start_hours'],$_POST['lunch_start_minutes'],$_POST['lunch_start_seconds'],$_POST['lunch_start_ampm']));
            $work_days->set_lunch_end($work_days->format_from_12hform($_POST['lunch_end_hours'],$_POST['lunch_end_minutes'],$_POST['lunch_end_seconds'],$_POST['lunch_end_ampm']));
            $work_days->set_end($work_days->format_from_12hform($_POST['end_hours'],$_POST['end_minutes'],$_POST['end_seconds'],$_POST['end_ampm']));
        } else {
            $work_days->set_start($work_days->format_from_24hform($_POST['start_hours'],$_POST['start_minutes'],$_POST['start_seconds']));
            $work_days->set_lunch_start($work_days->format_from_24hform($_POST['lunch_start_hours'],$_POST['lunch_start_minutes'],$_POST['lunch_start_seconds']));
            $work_days->set_lunch_end($work_days->format_from_24hform($_POST['lunch_end_hours'],$_POST['lunch_end_minutes'],$_POST['lunch_end_seconds']));
            $work_days->set_end($work_days->format_from_24hform($_POST['end_hours'],$_POST['end_minutes'],$_POST['end_seconds']));
        }
        $work_days->update();
    }

} else {
    header('Location: about.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" sizes="32x32" href="./img/favicon-32x32.png" />
    <title>your clock</title>
    <link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&display=swap" rel="stylesheet" />

    <script>
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty("--vh", `${vh}px`);
    const vw = window.innerWidth * 0.01;
    document.documentElement.style.setProperty("--vw", `${vw}px`);
    </script>

    <link rel="stylesheet" href="./css/styles.css" />
    <link rel="stylesheet" href="./css/micromodal.css" />
</head>

<body>
    <main>
        <header>
            <img src="./img/punch-away.png" alt="punch away" class="logo" />
            <div class="navigation">
                <a href="_log-out.php">log out</a>
                <a href="#" data-micromodal-trigger="modal-preferences">preferences</a>
                <a href="#" data-micromodal-trigger="modal-password">password</a>
                <h1>home</h1>
            </div>
        </header>
        <form method="get" action="index.php" enctype="multipart/form-data">
            <div class="form">
                <?php if(isset($success)) {
                    echo "<div class='alert' style='margin-bottom:4rem;'>$success</div>";
                } ?>
                <div class="input-group">
                    <label for="month">📅 view month</label>
                    <select name="month" id="month" onchange="this.form.submit()">
                        <?php 
                    $stmt = $work_days->month_index();
                    while($row = $stmt->fetch(PDO::FETCH_OBJ)) { 
                        echo "<option value='$row->month' ". (isset($_GET['month']) && $row->month==$_GET['month'] ? 'selected' : '') .">$row->month</option>";
                    } ?>
                    </select>
                </div>
            </div>
        </form>
        <div class="table">
            <h2><?php echo $work_days->get_month() ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>📅</th>
                        <th>punched in</th>
                        <th>went out</th>
                        <th>got back</th>
                        <th>punched out</th>
                        <th>edit</th>
                        <th>day hours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $stmt = $work_days->index();
                    while($row = $stmt->fetch(PDO::FETCH_OBJ)) { ?>
                    <tr>
                        <td><?php echo $work_days->format_date($row->date) ?></td>
                        <td><?php echo $work_days->format_clock($row->start) ?></td>
                        <td><?php echo $work_days->format_clock($row->lunch_start) ?></td>
                        <td><?php echo $work_days->format_clock($row->lunch_end) ?></td>
                        <td><?php echo $work_days->format_clock($row->end) ?></td>
                        <td>
                            <button data-micromodal-trigger="modal-date" onclick="edit('<?php echo $row->date ?>')">
                                ✏️
                            </button>
                        </td>
                        <td class="table__day_hours"><?php echo $row->total ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan='6'>Total</td>
                        <td id='table__total'>3</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
    <div class="modal micromodal-slide" id="modal-date" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-date-title"
                id="modal__editor">
            </div>
        </div>
    </div>
    <div class="modal micromodal-slide" id="modal-preferences" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-preferences-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-preferences-title">
                        preferences
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-preferences-content">
                    <form method="post" action="index.php" enctype="multipart/form-data">
                        <div class="form">
                            <div class="input-group modal__input-group">
                                <label for="date">📅 a perfect date</label>
                                <select name="date" id="date" required>
                                    <option value="" disabled selected>select a date format</option>
                                    <option value="L">dd/mm/yyyy</option>
                                    <option value="M">mm/dd/yyyy</option>
                                    <option value="B">yyyy/mm/dd</option>
                                </select>
                            </div>
                            <div class="input-group modal__input-group">
                                <label for="clock">⏰ 24h or 12h</label>
                                <select name="clock" id="clock" required>
                                    <option value="" disabled selected>select a time format</option>
                                    <option value="0">11:59 p.m.</option>
                                    <option value="1">23:59</option>
                                </select>
                            </div>
                        </div>
                        <div class="input-group modal__input-group">
                            <button name="preferences" type="submit" class="send">👉</button>
                        </div>
                    </form>
                </main>
                <footer class="modal__footer">
                </footer>
            </div>
        </div>
    </div>
    <div class="modal micromodal-slide" id="modal-password" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-password-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-password-title">
                        password
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-password-content">
                    <form method="post" action="index.php" enctype="multipart/form-data">
                        <div class="form">
                            <div class="input-group modal__input-group">
                                <label for="old-password">🗝️ old password</label>
                                <input type="password" name="old-password" id="old-password" required />
                            </div>
                            <div class="input-group modal__input-group">
                                <label for="new-password">🔑 new password</label>
                                <input type="password" name="new-password" id="new-password" required />
                            </div>
                            <div class="input-group modal__input-group">
                                <label for="confirm-password">🔑 confirm new password</label>
                                <input type="password" name="confirm-password" id="confirm-password" required />
                            </div>
                        </div>
                        <div class="input-group modal__input-group">
                            <button name="password" type="submit" class="send">👉</button>
                        </div>
                    </form>
                </main>
                <footer class="modal__footer">
                </footer>
            </div>
        </div>
    </div>
</body>
<script src="./js/index.js"></script>
<script src="./js/micromodal.min.js"></script>
<script src="./js/timepicker.min.js"></script>
<script>
window.onload = MicroModal.init();
</script>

</html>