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

            $output = '<table class="table" bordered="1">
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
                </tr>';

            while ($rowRegister = $sthRegister->fetch()) {
                $rowAttendance = $sthAttendance->fetch();

                $serialnumber++;

                $output .= '<tr>
                        <td>' . $serialnumber . '</td>
                        <td>' . $rowRegister->sname . '</td>
                        <td>' . $rowRegister->reg . '</td>
                        <td>' . $rowRegister->course . '</td>
                        <td>' . $rowRegister->year . '</td>
                        <td>';

                $reg = $rowRegister->reg;

                $sthSemester = $con->prepare("SELECT semester FROM day_to_day_attendance WHERE reg = :reg AND semester = :semester");
                $sthSemester->bindParam(':reg', $reg);
                $sthSemester->bindParam(':semester', $semester);
                $sthSemester->execute();

                $resultSemester = $sthSemester->fetch(PDO::FETCH_ASSOC);
                $studentSemester = $resultSemester ? $resultSemester['semester'] : '';
                $output .= $studentSemester . '</td>
                        <td>';

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
                $output .= $totalvalue . '/' . $totalvalues . '</td>
                        <td>';

                $reg = $rowRegister->reg;

                $sthCount = $con->prepare("SELECT semester, reg, COUNT(*) as present_count FROM day_to_day_attendance WHERE semester = :semester AND reg = :reg AND attendance_status = 'present' GROUP BY reg");
                $sthCount->bindParam(':semester', $semester);
                $sthCount->bindParam(':reg', $reg);
                $sthCount->execute();

                $result = $sthCount->fetch(PDO::FETCH_ASSOC);
                $presentCount = $result ? $result['present_count'] : 0;
                $presentCounts = $presentCount / 5;

                $output .= $presentCount . '/' . $presentCounts . '</td>
                        <td>';

                if ($totalvalues > 0) {
                    $percentage = $presentCounts / $totalvalues * 100;
                    $output .= number_format($percentage, 0) . '%';
                } else {
                    $output .= '0 %';
                }

                $output .= '</td>
                    </tr>';
            }

            $output .= '</table>';

            header('Content-Type: application/xls');
            header('Content-Disposition: attachment;filename=reports.xls');

            echo $output;
            exit;
        }
        ?>

