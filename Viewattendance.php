<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="css/Viewattendance.css">
    <title>EASC</title>
</head>

<body>
<div id="lock">
        <h1>Rotate your device to landscape mode for a better viewing experience.</h1>
    </div>
    <div id="container">
    <h1>VIEW ATTENDANCE</h1>
    <form method="post">
        <div id="searchbar">
            <button><a href="Dashboard.php" class="buttonoff">Back</a></button>
            <label>Course :</label>
            <input type="text" name="course" id="course">
            <label>Year :</label>
            <input type="number" name="year" id="year">
            <label>Semester :</label>
            <select name="semester">
                <option value="">Select</option>
                <option value="I">I</option>
                <option value="II">II</option>
                <option value="III">III</option>
                <option value="IV">IV</option>
                <option value="V">V</option>
                <option value="VI">VI</option>
            </select>
            <input type="submit" name="submit" id="submit button" value="Search">
            <input type="text" placeholder="Enter a student Reg.no" name="reg" id="reg">
            <select name="semester_reg">
                <option value="">Semester</option>
                <option value="I">I</option>
                <option value="II">II</option>
                <option value="III">III</option>
                <option value="IV">IV</option>
                <option value="V">V</option>
                <option value="VI">VI</option>
            </select>
            <input type="submit" name="search" id="search" value="Search">
        </div>
    </form>
    <br>
    <form id="formtwo" method="post">
        <table>
            <tr>
                <th>S.no</th>
                <th>Name</th>
                <th>Reg no</th>
                <th>Course</th>
                <th>Year</th>
                <th>Semester</th>
                <th>Total no.of Hrs/Day</th>
                <th>Student no.of present Hrs/Days</th>
                <th>Student Percentage</th>
            </tr>
            <?php

                include('connect_1.php');

            if (isset($_POST["submit"])) {
                $course = $_POST["course"];
                $year = $_POST["year"];
                $semester = $_POST["semester"];

                $sthRegister = $con->prepare("SELECT * FROM `student_register` WHERE course = :course AND year = :year ORDER BY reg");
                $sthAttendance = $con->prepare("SELECT * FROM `day_to_day_attendance` WHERE semester = :semester ORDER BY reg");

                $sthRegister->bindParam(':course', $course);
                $sthRegister->bindParam(':year', $year);

                $sthAttendance->bindParam(':semester', $semester);

                $sthRegister->setFetchMode(PDO::FETCH_OBJ);
                $sthAttendance->setFetchMode(PDO::FETCH_OBJ);

                $sthRegister->execute();
                $sthAttendance->execute();

                $serialnumber = 0;
                while ($rowRegister = $sthRegister->fetch()) {
                    $rowAttendance = $sthAttendance->fetch(); 

                    $serialnumber++;
                    ?>
                    <tr>
                        <td><?php echo $serialnumber; ?></td>
                        <td><?php echo $rowRegister->sname; ?></td>
                        <td><?php echo $rowRegister->reg; ?></td>
                        <td><?php echo $rowRegister->course; ?></td>
                        <td><?php echo $rowRegister->year; ?></td>
                        <td>
                        <?php
                        $reg = $rowRegister->reg;

                        $sthSemester = $con->prepare("SELECT semester FROM day_to_day_attendance WHERE reg = :reg AND semester = :semester");
                        $sthSemester->bindParam(':reg', $reg);
                        $sthSemester->bindParam(':semester', $semester);
                        $sthSemester->execute();

                        $resultSemester = $sthSemester->fetch(PDO::FETCH_ASSOC);
                        $studentSemester = $resultSemester ? $resultSemester['semester'] : '';
                        echo $studentSemester;

                        ?>
                    </td>
                    <td>
                    <?php
                            $reg = $rowRegister->reg;

                            $sthPresent = $con->prepare("SELECT COUNT(*) as present_count FROM day_to_day_attendance WHERE semester = :semester AND reg = :reg AND attendance_status = 'present'");
                            $sthPresent->bindParam(':semester', $semester);
                            $sthPresent->bindParam(':reg', $reg);
                            $sthPresent->execute();                            
                            $resultPresent = $sthPresent->fetch(PDO::FETCH_ASSOC);
                            $presentCount = $resultPresent ? $resultPresent['present_count'] : 0;

                            $sthAbsent = $con->prepare("SELECT COUNT(*) as absent_count FROM day_to_day_attendance WHERE semester = :semester AND reg = :reg AND attendance_status = 'absent'");
                            $sthAbsent->bindParam(':semester', $semester);
                            $sthAbsent->bindParam(':reg', $reg);
                            $sthAbsent->execute();
                            $resultAbsent = $sthAbsent->fetch(PDO::FETCH_ASSOC);
                            $absentCount = $resultAbsent ? $resultAbsent['absent_count'] : 0;

                            $totalvalue = $presentCount + $absentCount;
                            $totalvalues = $totalvalue / 5;
                            echo $totalvalue . "/" . $totalvalues;
                            ?>
                        </td>
                        <td>
                            <?php
                            $reg = $rowRegister->reg;
                            $sthCount = $con->prepare("SELECT semester, reg, COUNT(*) as present_count FROM day_to_day_attendance WHERE semester = :semester AND reg = :reg AND attendance_status = 'present' GROUP BY reg");
                            $sthCount->bindParam(':semester', $semester);
                            $sthCount->bindParam(':reg', $reg);
                            $sthCount->execute();

                            $result = $sthCount->fetch(PDO::FETCH_ASSOC);
                            $presentCount = $result ? $result['present_count'] : 0;
                            $presentCounts = $presentCount / 5;

                            echo $presentCount . "/" . $presentCounts;
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($totalvalues > 0) { 
                                $percentage = $presentCounts / $totalvalues * 100;
                                echo number_format($percentage, 0) . "%";
                                
                            } else {
                                echo "0 %";
                            }
                            ?>
                        </td>
                    </tr>
                <?php
                }

                if ($serialnumber == 0) {
                    ?>
                    <tr id="nocolour">
                        <td>No records found.</td>
                    </tr>
                <?php
                }
            }

            if (isset($_POST["search"])) {
                $reg = isset($_POST["reg"]) ? $_POST["reg"] : '';
                $semester = isset($_POST["semester_reg"]) ? $_POST["semester_reg"] : '';

                $sthReg = $con->prepare("SELECT * FROM `student_register` WHERE reg = :reg");
                $sthSem = $con->prepare("SELECT * FROM `day_to_day_attendance` WHERE semester = :semester ");

                $sthReg->bindParam(':reg', $reg);
                $sthSem->bindParam(':semester', $semester);

                $sthReg->setFetchMode(PDO::FETCH_OBJ);
                $sthSem->setFetchMode(PDO::FETCH_OBJ);

                $sthReg->execute();
                $sthSem->execute();

                $serialnumber = 0;
                while ($rowRegister = $sthReg->fetch()) {
                    $rowAttendance = $sthSem->fetch(); 

                    $serialnumber++;
                    ?>
                    <tr>
                        <td><?php echo $serialnumber; ?></td>
                        <td><?php echo $rowRegister->sname; ?></td>
                        <td><?php echo $rowRegister->reg; ?></td>
                        <td><?php echo $rowRegister->course; ?></td>
                        <td><?php echo $rowRegister->year; ?></td>
                        <td>
                        <?php
                        $reg = $rowRegister->reg;

                        $sthSemester = $con->prepare("SELECT semester FROM day_to_day_attendance WHERE reg = :reg AND semester = :semester");
                        $sthSemester->bindParam(':reg', $reg);
                        $sthSemester->bindParam(':semester', $semester);
                        $sthSemester->execute();

                        $resultSemester = $sthSemester->fetch(PDO::FETCH_ASSOC);
                        $studentSemester = $resultSemester ? $resultSemester['semester'] : '';
                        echo $studentSemester;
                        ?>
                    </td>
                    <td>
                            <?php
                            $reg = $rowRegister->reg;

                            $sthPresent = $con->prepare("SELECT COUNT(*) as present_count FROM day_to_day_attendance WHERE semester = :semester AND reg = :reg AND attendance_status = 'present'");
                            $sthPresent->bindParam(':semester', $semester);
                            $sthPresent->bindParam(':reg', $reg);
                            $sthPresent->execute();                            
                            $resultPresent = $sthPresent->fetch(PDO::FETCH_ASSOC);
                            $presentCount = $resultPresent ? $resultPresent['present_count'] : 0;

                            $sthAbsent = $con->prepare("SELECT COUNT(*) as absent_count FROM day_to_day_attendance WHERE semester = :semester AND reg = :reg AND attendance_status = 'absent'");
                            $sthAbsent->bindParam(':semester', $semester);
                            $sthAbsent->bindParam(':reg', $reg);
                            $sthAbsent->execute();
                            $resultAbsent = $sthAbsent->fetch(PDO::FETCH_ASSOC);
                            $absentCount = $resultAbsent ? $resultAbsent['absent_count'] : 0;

                            $totalvalue = $presentCount + $absentCount;
                            $totalvalues = $totalvalue / 5;
                            echo $totalvalue . "/" . $totalvalues;
                            ?>
                        </td>
                        <td>
                            <?php
                            $reg = $rowRegister->reg;
                            $sthCount = $con->prepare("SELECT semester, reg, COUNT(*) as present_count FROM day_to_day_attendance WHERE semester = :semester AND reg = :reg AND attendance_status = 'present' GROUP BY reg");
                            $sthCount->bindParam(':semester', $semester);
                            $sthCount->bindParam(':reg', $reg);
                            $sthCount->execute();

                            $result = $sthCount->fetch(PDO::FETCH_ASSOC);
                            $presentCount = $result ? $result['present_count'] : 0;
                            $presentCounts = $presentCount / 5;

                            echo $presentCount . "/" . $presentCounts;
                            ?>
                        </td>
                        <td>
                        <?php
                            if ($totalvalues > 0) { 
                                $percentage = $presentCounts / $totalvalues * 100;
                                echo number_format($percentage, 0) . "%";
                                
                            } else {
                                echo "0 %";
                            }
                            ?>
                        </td>
                    </tr>
                <?php
                }

                if ($serialnumber == 0) {
                    ?>
                    <tr id="nocolour">
                        <td>No records found.</td>
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