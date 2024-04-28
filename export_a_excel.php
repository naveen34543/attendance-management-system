<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="css/export_a_excel.css">
    <title>EASC</title>
</head>
<body>
<div id="lock">
        <h1>Rotate your device to landscape mode for a better viewing experience.</h1>
    </div>
    <div id="container">
    <form name="form" action="export.php" method="POST">
    <table>
        <h1>Student Attendance Report</h1>
        <tr>
            <td>
            <label>Course :</label>
        </td>
        <td>
        <input type="text" name="course" id="course">
        </td>
        </tr>
        <tr>
            <td>
        <label>Year :</label>
        </td>
        <td>
        <input type="number" name="year" id="year">
        </td>
        </tr>
        <tr>
            <td>
        <label>Semester :</label>
        </td>
        <td>
        <select name="semester" required>
                    <option value="">Select</option>
                    <option value="I">I</option>
                    <option value="II">II</option>
                    <option value="III">III</option>
                    <option value="IV">IV</option>
                    <option value="V">V</option>
                    <option value="VI">VI</option>
                </select>
        </td>
        </tr>
        </table>
        <br>
        <input type="submit" name="submit" id="submit button" value="Download">
        <br>
        <button><a href="Dashboard.php" class="buttonoff">Back</a></button>
        <br>
    </form>
    </div>
</body>
</html>