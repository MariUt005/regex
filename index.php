<?php
include("connect_db.php");

session_start();

// проверяем, что сессия существует
$q_check_session = "SELECT `hash` FROM `sessions` WHERE `hash`='". $mysqli->real_escape_string($_SESSION['h']) ."';";
$result = $mysqli->query($q_check_session);
$result = $result->fetch_array();
if (! $result) {
    header('Location: /regex/signin.php');
}

$q_get_user_id = "SELECT `id` FROM `coolhackers` WHERE `name`='". $mysqli->real_escape_string($_SESSION['nm']) ."';";
$user_id = $mysqli->query($q_get_user_id);
$user_id = $user_id->fetch_array();

$status = 0;

$task = 'Choose level!';
$text = '';
$regex = '';
$task_num = '';
$result = '';
// проверяем, не отправлена ли регулярка на проверку
if (array_key_exists('run', $_POST)) {
    $regex = $_POST['regex'];
    $task_num = $_POST['task_num'];

    $task_json = file_get_contents('tasks/'.$_POST['task_num'].'.json');
    $task_json = json_decode($task_json);
    $task = $task_json->{'task'};
    $text = $task_json->{'text'};

    try {
        $result = implode('', preg_grep($regex, str_split($text)));
        $q_get_answer = "SELECT `answer` FROM `tasks` WHERE `id`='". $mysqli->real_escape_string($task_num + 1) ."';";
        $answer = $mysqli->query($q_get_answer);
        $answer = $answer->fetch_array();
        if ($result == $answer[0]) {
            $q_add_user_task = "INSERT INTO `coolhackers_tasks` (`coolhackers_id`, `tasks_id`) VALUES ('". $mysqli->real_escape_string($user_id[0]) ."', '". $mysqli->real_escape_string($task_num+1) ."')";
            $t = $mysqli->query($q_add_user_task);
            $status = 1;
        }
    } catch (Throwable $e) {
        $result = 'Invalid regular expression!';
    }
}

$q_get_all_solved = "SELECT `tasks_id` FROM `coolhackers_tasks` WHERE `coolhackers_id`='". $mysqli->real_escape_string($user_id[0]) ."';";
$solved_tasks = $mysqli->query($q_get_all_solved);
$solved_tasks = $solved_tasks->fetch_all();
$temp = [];
for ($i = 0; $i < count($solved_tasks); ++$i) {
    $temp[$i] = $solved_tasks[$i][0] - 1;
}
$solved_tasks = json_encode($temp);

if (array_key_exists('n', $_GET)) {
    $q_get_task = "SELECT * FROM `tasks` WHERE `id`='". $mysqli->real_escape_string($_GET['n'] + 1) . "';";
    $task_obj = $mysqli->query($q_get_task);
    $task_obj = $task_obj->fetch_array();

    if ($task_obj) {
        $task_num = $_GET['n'];
        $task = $task_obj['task'];
        $text = $task_obj['text'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>RegEx</title>
    <script src="jquery.js"></script>
    <script>
        // скрыть выпадающее юзер-меню после загрузки страницы
        $(document).ready(
            function () {
                $('div[class=usermenu]').hide();
            }
        );
        // показать/скрыть выпыдающее юзер-меню после нажатия на никнейм
        $(document).ready(
            function () {
                $('span[class=nickname').click(
                    function () {
                        $('div[class=usermenu]').toggle();
                    }
                );
            }
        );

        // показать алерт, что функционал coming soon
        $(document).ready(
            function () {
                $('a').click(
                    function () {
                        if ($(this).attr('href') == '#') {
                            alert('Coming soon...');
                        }
                    }
                );
            }
        );

        $(document).ready(
            function () {
                var solved_tasks = <?php echo $solved_tasks; ?>;
                $('a.lvl').each(
                    function () {
                        if ($(this).text().slice(9) == <?php echo $task_num; ?>) {
                            if (solved_tasks.indexOf(Number($(this).text().slice(9))) != -1 ) {
                                $(this).css('color', '#42ff9e');
                            } else {
                                $(this).css('color', '#ff9494');
                            }
                            $(this).css('font-weight', 'bolder');
                        } else {
                            if (solved_tasks.indexOf(Number($(this).text().slice(9))) != -1 ) {
                                $(this).css('color', '#00db6a');
                            } else {
                                $(this).css('color', '#ff2e2e');
                            }
                            $(this).css('font-weight', 'normal');
                        }

                    }
                );
            }
        )

        if ('<?php echo $status; ?>' == 1) {
            alert('Solved!');
        }



    </script>

    <link rel='stylesheet' href='css/main.css'>
    <link rel='stylesheet' href='css/regex.css'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@300&display=swap" rel="stylesheet">
</head>
<body>
<div id="main">
    <div class="header">
        <div class="head">
            <span class="head">RegEx</span>
        </div>
        <div class="nickname">
            <span class="nickname"><?php echo $_SESSION['nm']; ?></span>
            <!--                    <div class="ico"></div>-->
        </div>
        <div class="usermenu">
            <a href="#" class="usermenu">> create lvl</a><br>
            <a href="#" class="usermenu">> settings</a><br>
            <a href="signout.php" class="usermenu">> sign out</a>
        </div>
    </div>
    <div class="container">
        <div class="levels">
            <p class="lvl">Levels</p>
            <a href='index.php?n=0' class="lvl">--> Lvl #0</a><br>
            <a href='index.php?n=1' class="lvl">--> Lvl #1</a><br>
            <a href='index.php?n=2' class="lvl">--> Lvl #2</a><br>
            <a href='index.php?n=3' class="lvl">--> Lvl #3</a><br>
            <a href='index.php?n=4' class="lvl">--> Lvl #4</a><br>
            <a href='#' class="lvl">--> Lvl #5</a><br>
            <a href='#' class="lvl">--> Lvl #6</a><br>
            <a href='#' class="lvl">--> Lvl #7</a><br>
            <a href='#' class="lvl">--> Lvl #8</a><br>
            <a href='#' class="lvl">--> Lvl #9</a><br>
        </div>
        <div class="data">
            <div class="task">
                <div class="task_text" id="task_text"><?php echo $task; ?></div>
                <div class="run">
                    <form action="" method="POST">
                        <input type="hidden" name="task_num" id="task_num" value="<?php echo $task_num; ?>">
                        <input type="text" name="regex" id="regex" placeholder="RegEx..." value="<?php echo $regex; ?>">
                        <input type="submit" value="> Run" id="run" name="run">
                    </form>
                </div>
            </div>
            <div class="container sub">
                <div class="ctext">
                    <div class="text" id="text_for_task"><?php echo $text; ?></div>
                </div>
                <div class="ctext">
                    <div class="text" id="regex_res"><?php echo $result; ?></div>
                </div>
            </div>
            <div class="task_menu"></div>
        </div>
    </div>
</div>
</body>
</html>