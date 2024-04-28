<?php

$selectedHours = isset($_POST['hour']) ? $_POST['hour'] : '';
$semester = isset($_POST['semester']) ? $_POST['semester'] : '';

include('connect_1.php');

$counter = 0;

if (isset($_POST['submit']) && isset($_POST['attendance_status'])) {

    $course = isset($_POST['course']) ? $_POST['course'] : '';
    $date = date("Y-m-d");

    foreach ($_POST['attendance_status'] as $id => $attendance_status) {
        $sname = $_POST['sname'][$id];
        $reg = $_POST['reg'][$id];
        $course = $_POST['course'][$id];
        $year = $_POST['year'][$id];
        $selectedHours = $_POST['hour'][$id];
        $semester = $_POST['semester'][$id];

        $checkQuery = "SELECT * FROM `day_to_day_attendance` WHERE reg = :reg AND date = :date";
        $checkStmt = $con->prepare($checkQuery);
        $checkStmt->bindParam(':reg', $reg);
        $checkStmt->bindParam(':date', $date);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 4) {
            echo '<script>
                function myFunction() {
                    alert("Already five Hours Updated");
                    window.location.href = "Dashboard.php";
                }
                myFunction();
            </script>';
            exit();
        }

        $checksQuery = "SELECT * FROM `day_to_day_attendance` WHERE reg = :reg AND date = :date AND hour = :selectedHours";
        $checksStmt = $con->prepare($checksQuery);
        $checksStmt->bindParam(':reg', $reg);
        $checksStmt->bindParam(':date', $date);
        $checksStmt->bindParam(':selectedHours', $selectedHours); 
        $checksStmt->execute();

        if ($checksStmt->rowCount() > 0) {
            echo '<script>
                function myFunction() {
                    alert("Already this Hour Updated");
                    window.location.href = "Dashboard.php";
                }
                myFunction();
            </script>';
            exit();
        }

        $query = "INSERT INTO `day_to_day_attendance` (sname, reg, course, year, hour, semester, attendance_status, date) VALUES (:sname, :reg, :course, :year, :selectedHours, :semester, :attendance_status, :date)";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':sname', $sname);
        $stmt->bindParam(':reg', $reg);
        $stmt->bindParam(':course', $course);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':selectedHours', $selectedHours);
        $stmt->bindParam(':semester', $semester);
        $stmt->bindParam(':attendance_status', $attendance_status);
        $stmt->bindParam(':date', $date);

        if (!$stmt->execute()) {
            echo "Error: " . implode(" ", $stmt->errorInfo());
        }

        $counter++;
    }

    if ($counter > 0) {
        echo '<script>
            function myFunction() {
                alert("Successfully Attendance Updated");
                window.location.href = "Dashboard.php";
            }
            myFunction();
        </script>';
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="css/Takeattendance.css">
    <title>EASC</title>
</head>

<body>
<div id="lock">
        <h2>Rotate your device to landscape mode for a better viewing experience.</h2>
    </div>
    <div id="container">
    <h1>TAKE ATTENDANCE</h1>
    <form method="post">
        <div id="searchbar">
            <button><a href="Dashboard.php" class="buttonoff">Back</a></button>
            <label id="font_">Date : <?php echo date("Y-m-d"); ?></label>
            <label id="font_">Course :</label>
            <input type="text" name="course" id="course" required>
            <label id="font_">Year :</label>
            <input type="number" name="year" id="year" required>
            <label id="font_">Semester :</label>
            <select name="semester" <?php echo $semester; ?> required>
                <option value="">Select</option>
                <option value="I">I</option>
                <option value="II">II</option>
                <option value="III">III</option>
                <option value="IV">IV</option>
                <option value="V">V</option>
                <option value="VI">VI</option>
            </select>
            <label id="font_">Hour :</label>
            <select name="hour" <?php echo $selectedHours; ?> required>
                <option value="">Select</option>
                <option value="I">I</option>
                <option value="II">II</option>
                <option value="III">III</option>
                <option value="IV">IV</option>
                <option value="V">V</option>
            </select>
            <input type="submit" name="submit" id="submit_button" value="Search">
        </div>
    </form>
    <br>
    <form id="formtwo" method="POST">
        <table>
            <tr>
                <th>S.no</th>
                <th>Name</th>
                <th>Reg no</th>
                <th>Course</th>
                <th>Year</th>
                <th>Semester</th>
                <th>Hour</th>
                <th>Attendance Status</th>
            </tr>
            <?php
            if (!isset($_POST["course"]) || !isset($_POST["year"])) {
                echo "<tr id='nocolour'><td>Fill all the fields.</td></tr>";
            } else {
            $serialnumber = 0;
            if (isset($_POST["submit"])) {
                $course = $_POST["course"];
                $year = $_POST["year"];

                $sth = $con->prepare("SELECT * FROM `student_register` WHERE course = :course AND year = :year ORDER BY reg");

                $sth->bindParam(':course', $course);
                $sth->bindParam(':year', $year);

                $sth->setFetchMode(PDO::FETCH_OBJ);
                $sth->execute();

                while ($row = $sth->fetch()) {
                    $serialnumber++;
                    ?>
                    <tr>
                        <td><?php echo $serialnumber; ?></td>
                        <td><?php echo $row->sname; ?>
                            <input type="hidden" value="<?php echo $row->sname; ?>" name="sname[<?php echo $serialnumber; ?>]" id="sname_<?php echo $serialnumber; ?>">
                        </td>
                        <td><?php echo $row->reg; ?>
                            <input type="hidden" value="<?php echo $row->reg; ?>" name="reg[<?php echo $serialnumber; ?>]" id="reg_<?php echo $serialnumber; ?>">
                        </td>
                        <td><?php echo $row->course; ?>
                            <input type="hidden" value="<?php echo $row->course; ?>" name="course[<?php echo $serialnumber; ?>]" id="course_<?php echo $serialnumber; ?>">
                        </td>
                        <td><?php echo $row->year; ?>
                            <input type="hidden" value="<?php echo $row->year; ?>" name="year[<?php echo $serialnumber; ?>]" id="year_<?php echo $serialnumber; ?>">
                        </td>
                        <td>
                            <?php echo $semester ?>
                            <input type="hidden" value="<?php echo $semester; ?>" name="semester[<?php echo $serialnumber; ?>]" id="semester_<?php echo $serialnumber; ?>">
                        </td>
                        <td>
                            <?php echo $selectedHours ?>
                            <input type="hidden" value="<?php echo $selectedHours; ?>" name="hour[<?php echo $serialnumber; ?>]" id="hour_<?php echo $serialnumber; ?>">
                        </td>
                        <td>
                            <input type="radio" name="attendance_status[<?php echo $serialnumber; ?>]" id="attendance_status_present_<?php echo $serialnumber; ?>" value="present" required><img src="css/img/correct.png" alt="present">
                            <input type="radio" name="attendance_status[<?php echo $serialnumber; ?>]" id="attendance_status_absent_<?php echo $serialnumber; ?>" value="absent" required><img src="css/img/cross.png" alt="absent">
                        </td>
                    </tr>
                    <?php
                }
            }
            if ($serialnumber == 0) {
                echo "<tr id='nocolour'><td>No records found.</td></tr>";
            }
        }
            ?>
        </table>
        <br>
        <input type="submit" name="submit" id="submit_button" value="Submit">
    </form>
    </div>
</body>

</html>
