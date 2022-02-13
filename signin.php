<?php
include("connect_db.php");

session_start();
if (array_key_exists("h", $_SESSION)) {
    $q_check_session = "SELECT `hash` FROM `sessions` WHERE `hash`='". $mysqli->real_escape_string($_SESSION['h']) ."';";
    $result = $mysqli->query($q_check_session);
    $result = $result->fetch_array();
    if ($result) {
        header('Location: /regex/index.php');
    }
}



$status = 0;
$code_user_not_found = 1;
$code_pass_incorrect = 2;

$nm = "";
$pass = "";

if (array_key_exists("nm", $_POST)) {
    $nm = $_POST['nm'];
    $pass = $_POST["pass"];
	$q_check_user = "SELECT * FROM `coolhackers` WHERE name='". $mysqli->real_escape_string($nm) ."';";
	$result = $mysqli->query($q_check_user);
    $result = $result->fetch_array();
	if ($result) {
        if (password_verify($pass, $result['pass'])) {
            $t = time();
            $hash = password_hash($nm."-".$t, PASSWORD_DEFAULT);
            $_SESSION['nm'] = $_POST['nm'];
            $_SESSION['h'] = $hash;

            $q_auth = "INSERT INTO `sessions`(`hash`, `time`) VALUES ('". $mysqli->real_escape_string($hash)."','". $mysqli->real_escape_string($t) ."')";
            $result = $mysqli->query($q_auth);
            header('Location: /regex/index.php?n=0');
        } else {
            $status = $code_pass_incorrect;
        }
	} else {
        $status = $code_user_not_found;
    }
}
?>


<html>
<head>
	<title>RegEx Sign In</title>
    <link rel='stylesheet' href='css/main.css'>
    <link rel='stylesheet' href='css/sign.css'>
    <!-- <link rel='stylesheet' href='css/regex.css'> -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@300&display=swap" rel="stylesheet">

    <script>
        if (<?php echo $status; ?> == <?php echo $code_user_not_found; ?>) {
            alert("User not found!");
        } else if (<?php echo $status; ?> == <?php echo $code_pass_incorrect; ?>) {
            alert("Password is incorrect!");
        }
    </script>
</head>
<body>
    <div id="main">
        <div id='content'>
            <p id='header'>RegEx</p>
            <form action='' method='POST'>
                <input type='text' name='nm' placeholder='username...' class='input' value='<?php echo $nm; ?>' required><br>
                <input type='password' name='pass' placeholder='password...' class='input' value='<?php echo $pass; ?>' required><br>
                <input type='submit' value="Sign in!" class='submit'>
            </form>
            <a href="signup.php" class="redir">Don't have an acc? Sign up!</a>
        </div>
    </div>
</body>
</html>
