<?php
include('connect.php');

?>

<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link href="css/index.css" rel="stylesheet" >
    <title>EASC</title>
</head>
<body>
    <div id="lock">
        <h1>Rotate your device to landscape mode for a better viewing experience.</h1>
    </div>
    <div id="container">
       <div id="image" class="image"></div>
    <form name="form" id="box" method="POST">
        <h2>Login Panel</h2>
        <label>Username</label>
        <input type="text" name="user" id="user" required>
        <label>Staff ID</label>
        <input type="text" name="reg" id="reg" required>
        <label>DOB</label>
        <input type="date" name="pass" id="pass" required>
        <input type="submit" class="btn btn-success" value="Log In">
        </div>
<?php
session_start();

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $userInput = $_POST['user'];
    $regInput = $_POST['reg'];
    $passInput = $_POST['pass'];

    $sql = "SELECT * FROM `staff_register&index` WHERE user = '{$userInput}' AND reg = '{$regInput}' AND pass = '{$passInput}'";
    $result = $con->query($sql);

    if ($result->num_rows == 1) {
        header("Location: Dashboard.php");
        exit();
    } 
    else {
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<div class="alert">Invalid Username/Password!</div><br>';
    }

    $con->close();
}
?>
    </form>
</body>
</html>
