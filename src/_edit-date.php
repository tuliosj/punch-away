<?php

include_once('./php/database.php');
include_once('./php/controllers/workDaysController.php');

session_start();

function cast($x) {return (int)$x;}

function options($i, $time, $_24hclock) {
    $time = explode(':',$time);
    $time = array_map('cast',$time);

    if($_24hclock) {
        $ampm_select = '';
    } else {
        $ampm_select = 
        '<select '.($attribute=="start" ? 'onchange=\'fill_next(0)\'' : ($attribute=="lunch_start"?'onchange=\'fill_next(1)\'':'onchange=\'fill_next(2)\'')).' name="'.$attribute.'_ampm" id="'.$attribute.'_ampm" required>
            <option value="0"'.($time[0]<12?' selected':'').'>a.m.</option>
            <option value="1"'.($time[0]>11?' selected':'').'>p.m.</option>
        </select>';
        $time[0] = $time[0]%12;
        if($time[0]==0) {
            $time[0]=12;
        }
    }

    $hours_options = "";
    if($_24hclock) {
        for ($i=0; $i < 24; $i++) { 
            $hours_options .= "<option value='".sprintf("%d",$i)."' ". ($time[0]==$i?'selected>':'>').sprintf("%02d",$i)."</option>";
        }
    } else {
        for ($i=1; $i < 13; $i++) { 
            $hours_options .= "<option value='".sprintf("%d",$i)."' ". ($time[0]==$i?'selected>':'>').sprintf("%02d",$i)."</option>";
        }
    }

    $minutes_options = "";
    for ($i=0; $i < 60; $i++) { 
        $minutes_options .= "<option value='".sprintf("%d",$i)."' ". ($time[1]==$i?'selected>':'>').sprintf("%02d",$i)."</option>";
    }

    $seconds_options = "";
    for ($i=0; $i < 60; $i++) { 
        $seconds_options .= "<option value='".sprintf("%d",$i)."' " . ($time[2]==$i?'selected>':'>').sprintf("%02d",$i)."</option>";
    }

    return [$hours_options, $minutes_options, $seconds_options, $ampm_select];
}

if(isset($_SESSION)) {
    $date = $_REQUEST["q"];
    $work_days = new WorkDay($_SESSION['id']);
    $work_days->set_date($date);
    $todays_work = $work_days->view();
    $_24hclock = $work_days->get_24hclock();

    $start_options = options('start', $todays_work->start, $_24hclock);
    $lunch_start_options = options('lunch_start', $todays_work->lunch_start, $_24hclock);
    $lunch_end_options = options('lunch_end', $todays_work->lunch_end, $_24hclock);
    $end_options = options('end', $todays_work->end, $_24hclock);

    $modal = 
    '<header class="modal__header">
        <h2 class="modal__title" id="modal-date-title">
            punch clock | '.$work_days->format_date($date).'
        </h2>
        <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
    </header>
    <main class="modal__content" id="modal-date-content">
        <form name="date_edit" method="post" action="index.php" enctype="multipart/form-data">
            <div class="form">
                <input type="hidden" name="_24hclock" id="_24hclock" value="'.$_24hclock.'">
                <input type="hidden" name="edited-date" id="edited-date" value="'.$date.'">
                <div class="input-group modal__input-group">
                    <label for="start_hours">👨‍💻 punched in</label>
                    <div>
                        <select onchange=\'fill_next(0)\' name="start_hours" id="start_hours" required>
                            '.$start_options[0].'
                        </select>
                        :
                        <select onchange=\'fill_next(0)\' name="start_minutes" id="start_minutes" required>
                            '.$start_options[1].'
                        </select>
                        :
                        <select onchange=\'fill_next(0)\' name="start_seconds" id="start_seconds" required>
                            '.$start_options[2].'
                        </select>
                        '.$start_options[3].'
                        <a href="#" id="start_now" onclick="fill_now(\'start\')">now</a>
                    </div>
                </div>
                <div class="input-group modal__input-group">
                    <label for="lunch_start">🍽️ went out</label>
                    <div>
                        <select onchange=\'fill_next(1)\' name="lunch_start_hours" id="lunch_start_hours" required>
                            '.$lunch_start_options[0].'
                        </select>
                        :
                        <select onchange=\'fill_next(1)\' name="lunch_start_minutes" id="lunch_start_minutes" required>
                            '.$lunch_start_options[1].'
                        </select>
                        :
                        <select onchange=\'fill_next(1)\' name="lunch_start_seconds" id="lunch_start_seconds" required>
                            '.$lunch_start_options[2].'
                        </select>
                        '.$lunch_start_options[3].'
                        <a href="#" id="lunch_start_now" onclick="fill_now(\'lunch_start\')">now</a>
                    </div>
                </div>
                <div class="input-group modal__input-group">
                    <label for="lunch_end">🎯 got back</label>
                    <div>
                        <select onchange=\'fill_next(2)\' name="lunch_end_hours" id="lunch_end_hours" required>
                            '.$lunch_end_options[0].'
                        </select>
                        :
                        <select onchange=\'fill_next(2)\' name="lunch_end_minutes" id="lunch_end_minutes" required>
                            '.$lunch_end_options[1].'
                        </select>
                        :
                        <select onchange=\'fill_next(2)\' name="lunch_end_seconds" id="lunch_end_seconds" required>
                            '.$lunch_end_options[2].'
                        </select>
                        '.$lunch_end_options[3].'
                        <a href="#" id="lunch_end_now" onclick="fill_now(\'lunch_end\')">now</a>
                    </div>
                </div>
                <div class="input-group modal__input-group">
                    <label for="end">💪 punched out</label>
                    <div>
                        <select name="end_hours" id="end_hours" required>
                            '.$end_options[0].'
                        </select>
                        :
                        <select name="end_minutes" id="end_minutes" required>
                            '.$end_options[1].'
                        </select>
                        :
                        <select name="end_seconds" id="end_seconds" required>
                            '.$end_options[2].'
                        </select>
                        '.$end_options[3].'
                        <a href="#" id="end_now" onclick="fill_now(\'end\')">now</a>
                    </div>
                </div>
            </div>
            <div class="input-group modal__input-group">
                <button name="date_change" type="submit" class="send">👉</button>
            </div>
        </form>
    </main>';
    echo $modal;
} else {
    echo 'error, please log out and try again later.';
}

?>