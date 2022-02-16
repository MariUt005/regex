<?php
include("connect_db.php");

session_start();
if (array_key_exists("h", $_SESSION)) {
    $q_check_session = "SELECT `hash` FROM `sessions` WHERE `hash`='". $mysqli->real_escape_string($_SESSION['h']) ."';";
    $result = $mysqli->query($q_check_session);
    $result = $result->fetch_array();
    if ($result) {
        header('Location: index.php?n=0');
    }
}

$status = 0;
$code_username_used = 1;
$code_pass_not_same = 2;
$code_sign_up_success = 3;

$nm = "";
$pass = "";
$pass_2 = "";

if (array_key_exists("nm", $_POST)) {
    $nm = $_POST["nm"];
    $pass = $_POST["pass"];
    $pass_2 = $_POST["pass_2"];

	$q_check_username = "SELECT EXISTS(SELECT * FROM `coolhackers` WHERE name='". $mysqli->real_escape_string($_POST['nm']) ."');";
	$result = $mysqli->query($q_check_username);
	if ($result->fetch_array()[0]) {
        $status = $code_username_used;
	} else if ($_POST["pass"] != $_POST["pass_2"]){
        $status = $code_pass_not_same;
    } else {
        $q_insert_data = "INSERT INTO `coolhackers` (`name`, `pass`) VALUES ('". $mysqli->real_escape_string($nm) ."', '".password_hash($pass, PASSWORD_DEFAULT)."');";
        $result = $mysqli->query($q_insert_data);

        session_start();
        $t = time();
        $hash = password_hash($_POST['nm']."-".$t, PASSWORD_DEFAULT);
        $_SESSION['nm'] = $_POST['nm'];
        $_SESSION['h'] = $hash;

        $q_auth = "INSERT INTO `sessions` (`hash`, `time`) VALUES ('".$mysqli->real_escape_string($hash)."', '". $mysqli->real_escape_string($t) ."');";
        $result = $mysqli->query($q_auth);

        $status = $code_sign_up_success;
    }
}
?>


<html>
<head>
	<title>RegEx Sign Up</title>

    <link rel='stylesheet' href='css/main.css'>
    <link rel='stylesheet' href='css/sign.css'>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@300&display=swap" rel="stylesheet">

    <script>
        if (<?php echo $status; ?> == <?php echo $code_username_used; ?>) {
            alert("This username already used!");
        } else if (<?php echo $status; ?> == <?php echo $code_pass_not_same; ?>) {
            alert("Passwords not same!");
        } else if (<?php echo $status; ?> == <?php echo $code_sign_up_success; ?>) {
            alert("Success!");
            location.href ='index.php?n=0';
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
                <input type='password' name='pass_2' placeholder='repeat password...' class='input' value='<?php echo $pass_2; ?>' required><br>
                <input type='submit' value="Sign up!" class='submit'>
            </form>
            <a href="signin.php" class="redir">Already have an acc? Sign in!</a>
        </div>
    </div>
</body>
</html>
