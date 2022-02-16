<?php
include("connect_db.php");

session_start();

// проверяем, что сессия существует
$q_check_session = "SELECT * FROM `sessions` WHERE `hash`='". $mysqli->real_escape_string($_SESSION['h']) ."';";
$result = $mysqli->query($q_check_session);
$result = $result->fetch_array();
if (! $result) {
    header('Location: /regex/signin.php');
}


if ( ! password_verify($_SESSION['nm']."-".$result['time'], $result['hash'])) {
    unset($_SESSION['nm']);
    unset($_SESSION['h']);
    header('Location: /regex/signin.php');
}

$q_get_user_id = "SELECT `id` FROM `coolhackers` WHERE `name`='". $mysqli->real_escape_string($_SESSION['nm']) ."';";
$user_id = $mysqli->query($q_get_user_id);
$user_id = $user_id->fetch_array();

$status = 0;
$message = '';

$new_nm = '';
$new_nm_pass = '';
$old_pass = '';
$new_pass = '';
$new_pass_repeat = '';

if (array_key_exists('change_username', $_POST)) {
    $new_nm = $_POST['nm'];
    $new_nm_pass = $_POST['pass'];
    $q_check_username = "SELECT EXISTS(SELECT * FROM `coolhackers` WHERE name='". $mysqli->real_escape_string($_POST['nm']) ."');";
    $result = $mysqli->query($q_check_username);
    if ($result->fetch_array()[0]) {
        $status = 1;
        $message = 'This username already used!';
    } else {
        $q_get_pass_hash = "SELECT `pass` FROM `coolhackers` WHERE id='". $user_id[0] ."';";
        $pass_hash = $mysqli->query($q_get_pass_hash);
        $pass_hash = $pass_hash->fetch_array();
        if (password_verify($new_nm_pass, $pass_hash[0])) {
            $q_update_data = "UPDATE `coolhackers` SET `name`='". $mysqli->real_escape_string($new_nm) ."' WHERE id='". $user_id[0] ."';";
            $result = $mysqli->query($q_update_data);

            $t = time();
            $hash = password_hash($_POST['nm']."-".$t, PASSWORD_DEFAULT);
            $_SESSION['nm'] = $_POST['nm'];
            $_SESSION['h'] = $hash;

            $q_auth = "INSERT INTO `sessions` (`hash`, `time`) VALUES ('".$mysqli->real_escape_string($hash)."', '". $mysqli->real_escape_string($t) ."');";
            $result = $mysqli->query($q_auth);

            $status = 1;
            $message = 'Username changed!';
        } else {
            $status = 1;
            $message = 'Password is incorrect!';
        }
    }
}
if (array_key_exists('change_password', $_POST)) {
    $old_pass = $_POST['pass'];
    $new_pass = $_POST['pass_new'];
    $new_pass_repeat = $_POST['pass_new_repeat'];
    $q_get_pass_hash = "SELECT `pass` FROM `coolhackers` WHERE id='". $user_id[0] ."';";
    $pass_hash = $mysqli->query($q_get_pass_hash);
    $pass_hash = $pass_hash->fetch_array();
    if (password_verify($old_pass, $pass_hash[0])) {
        if ($new_pass == $new_pass_repeat) {
            $q_update_data = "UPDATE `coolhackers` SET `pass`='". password_hash($new_pass, PASSWORD_DEFAULT) ."' WHERE id='". $user_id[0] ."';";
            $result = $mysqli->query($q_update_data);

            $t = time();
            $hash = password_hash($_SESSION['nm']."-".$t, PASSWORD_DEFAULT);
            $_SESSION['h'] = $hash;

            $q_auth = "INSERT INTO `sessions` (`hash`, `time`) VALUES ('".$mysqli->real_escape_string($hash)."', '". $mysqli->real_escape_string($t) ."');";
            $result = $mysqli->query($q_auth);

            $status = 1;
            $message = 'Password changed!';
        } else {
            $status = 1;
            $message = 'Passwords not same!';
        }
    } else {
        $status = 1;
        $message = 'Password is incorrect!';
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
                $('div.usermenu').hide();
            }
        );
        // показать/скрыть выпыдающее юзер-меню после нажатия на никнейм
        $(document).ready(
            function () {
                $('span.nickname').click(
                    function () {
                        $('div.usermenu').toggle();
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

        // поставить значения по умолчанию после загрузки страницы
        $(document).ready(
            function () {
                $('div.set_container').hide();
                $('div.user_set').show();
                $('a.sidemenu').each(
                    function () {
                        if ($(this).text() == '--> User') {
                            $(this).css('color', '#42ff9e');
                            $(this).css('font-weight', 'bolder');
                        } else {
                            $(this).css('color', '#00db6a');
                            $(this).css('font-weight', 'normal');
                        }
                    }
                )
            }
        )

        // менять цвет выбранной позиции меню при клике
        $(document).ready(
            function () {
                $('a.sidemenu').click(
                    function () {
                        if ($(this).attr('href') != '#') {
                            var clicked = $(this);

                            $('a.sidemenu').each(
                                function () {
                                    if ($(this).text() == clicked.text()) {
                                        $(this).css('color', '#42ff9e');
                                        $(this).css('font-weight', 'bolder');
                                    } else {
                                        $(this).css('color', '#00db6a');
                                        $(this).css('font-weight', 'normal');
                                    }
                                }
                            );

                            $('div.set_container').hide();
                            switch (clicked.attr('id')) {
                                case 'user':
                                    $('div.user_set').show();
                                    break;
                                case 'regex':
                                    $('div.regex_set').show();
                                    break;
                                default:
                                    alert('This page doesn\'t exists!');
                            }
                        }
                    }
                );
            }
        );

        if (<?php echo $status; ?> == 1) {
            alert('<?php echo $message; ?>');
        }
    </script>

    <link rel='stylesheet' href='css/main.css'>
    <link rel='stylesheet' href='css/regex.css'>
    <link rel='stylesheet' href='css/settings.css'>

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
            <a href="index.php?n=0" class="usermenu">> levels</a><br>
            <a href="#" class="usermenu">> create lvl</a><br>
            <a href="signout.php" class="usermenu">> sign out</a>
        </div>
    </div>
    <div class="container">
        <div class="sidebar">
            <p class="sidemenu">Settings</p>
            <a class="sidemenu" id="user">--> User</a><br>
            <a href='#' class="sidemenu" id="regex">--> RegEx</a><br>
        </div>
        <div class="data">
            <div class="set_container user_set">
                <div class="set_block">
                    <p class="">Change username:</p>
                    <form action="" method="POST">
                        <input type="text" name="nm" placeholder="new username..." value="<?php echo $new_nm; ?>" required><br>
                        <input type="password" name="pass" placeholder="password..." value="<?php echo $new_nm_pass; ?>" required><br>
                        <input type="submit" name="change_username" value="Submit!">
                    </form>
                </div>
                <div class="set_block">
                    <p class="">Change password:</p>
                    <form action="" method="POST">
                        <input type="password" name="pass" placeholder="old password..." value="<?php echo $old_pass; ?>" required><br>
                        <input type="password" name="pass_new" placeholder="new password..." value="<?php echo $new_pass; ?>" required><br>
                        <input type="password" name="pass_new_repeat" placeholder="repeat new password..." value="<?php echo $new_pass_repeat; ?>" required><br>
                        <input type="submit" name="change_password" value="Submit!">
                    </form>
                </div>
            </div>
            <div class="set_container regex_set">

            </div>
        </div>
    </div>
</div>
</body>
</html>