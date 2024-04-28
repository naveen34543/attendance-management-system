<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="css/daytodayattendance.css">
    <title>EASC</title>
</head>

<body>
    <div id="lock">
        <h1>Rotate your device to landscape mode for a better viewing experience.</h1>
    </div>
    <div id="container">
        <h1>DAY TO DAY ATTENDANCE</h1>
        <form method="post" id="searchbar">
            <div>
                <button><a href=" Dashboard.php" class="buttonoff">Back</a></button>
                <label>Course :</label>
                <input type="text" name="course" id="course">
                <label>Year :</label>
                <input type="number" name="year" id="year">
                <input type="date" name="date" id="date">
                <input type="submit" name="submit" id="submit_button" value="Search">
            </div>
        </form>
        <br>
        <form id="formtwo" method="post">
            <table>
                <tr>
                    <th>S.no</th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Reg no</th>
                    <th>Course</th>
                    <th>Present</th>
                    <th>Absent</th>
                </tr>
                <?php
                    include('connect_1.php');

                    if (isset($_POST["submit"])) {
                        $course = $_POST["course"];
                        $year = $_POST["year"];
                        $date = $_POST["date"];

                        $sth = $con->prepare("SELECT * FROM `day_to_day_attendance` WHERE course = :course and year = :year and date = :date ORDER BY reg");

                        $sth->bindParam(':course', $course);
                        $sth->bindParam(':year', $year);
                        $sth->bindParam(':date', $date);

                        $sth->setFetchMode(PDO::FETCH_OBJ);
                        $sth->execute();

                        $serialnumber = 0;
                        $studentData = array(); 

                        while ($row = $sth->fetch()) {
                            $reg = $row->reg;

                            $sthRegister = $con->prepare("SELECT * FROM `student_register` WHERE sname = :sname and reg = :reg and course = :course");
                            $sthRegister->bindParam(':sname', $row->sname);
                            $sthRegister->bindParam(':reg', $reg);
                            $sthRegister->bindParam(':course', $course);

                            $sthRegister->execute();
                            $rowRegister = $sthRegister->fetch(PDO::FETCH_OBJ);

                            $sthPresent = $con->prepare("SELECT COUNT(*) as present_count FROM day_to_day_attendance WHERE reg = :reg AND date = :date AND attendance_status = 'present'");
                            $sthPresent->bindParam(':reg', $reg);
                            $sthPresent->bindParam(':date', $date);
                            $sthPresent->execute();
                            $resultPresent = $sthPresent->fetch(PDO::FETCH_ASSOC);
                            $presentCount = $resultPresent ? $resultPresent['present_count'] : 0;

                            $sthAbsent = $con->prepare("SELECT COUNT(*) as absent_count FROM day_to_day_attendance WHERE reg = :reg AND date = :date AND attendance_status = 'absent'");
                            $sthAbsent->bindParam(':reg', $reg);
                            $sthAbsent->bindParam(':date', $date);
                            $sthAbsent->execute();
                            $resultAbsent = $sthAbsent->fetch(PDO::FETCH_ASSOC);
                            $absentCount = $resultAbsent ? $resultAbsent['absent_count'] : 0;

                            $studentData[$reg] = array(
                                'date' => $row->date,
                                'sname' => $rowRegister->sname,
                                'reg' => $rowRegister->reg,
                                'course' => $rowRegister->course,
                                'present' => $presentCount,
                                'absent' => $absentCount
                            );
                        }

                        foreach ($studentData as $reg => $data) {
                            $serialnumber++;
                            ?>
                            <tr>
                                <td><?php echo $serialnumber; ?></td>
                                <td><?php echo $data['date']; ?></td>
                                <td><?php echo $data['sname']; ?></td>
                                <td><?php echo $data['reg']; ?></td>
                                <td><?php echo $data['course']; ?></td>
                                <td><?php echo $data['present']; ?></td>
                                <td><?php echo $data['absent']; ?></td>
                            </tr>
                        <?php
                        }

                        if ($serialnumber == 0) {
                            ?>
                            <tr id="nocolour">
                                <td>No records found.</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php
                        }
                    }
                ?>
            </table>
        </form>
    </div>
</body>

</html>
