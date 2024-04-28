<?php
session_start();

$user = $_POST['user'];
$reg = $_POST['reg'];
$pass = $_POST['pass'];
$mobile = $_POST['mobile'];
$email = $_POST['email'];

if (!empty($user) && !empty($reg) && !empty($pass) && !empty($mobile) && !empty($email)) {
   
    include('connect.php');

    if (mysqli_connect_error()) {
        die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    } else {
        
        $checkQuery = "SELECT * FROM `staff_register&index` WHERE reg = '$reg'";
        $result = mysqli_query($con, $checkQuery);

        if (mysqli_num_rows($result) > 0) {
            echo '<script>
                function myFunction() {
                    alert("Staff ID already exists. Please use a different one.");
                    window.location.href = "staffnewregister.php";
                }
                myFunction();
            </script>';
            exit();
        }

        
        $query = "INSERT INTO `staff_register&index` (user, reg, pass, mobile, email) VALUES ('$user', '$reg', '$pass', '$mobile', '$email')";

        if (mysqli_query($con, $query)) {
            echo '<script>
                function myFunction() {
                    alert("Successfully Updated");
                    window.location.href = "index.php";
                }
                myFunction();
            </script>';    
            exit();
        } else {
            echo "Error: " . mysqli_error($con);
        }

        $con->close();
    }
}
?>
