<?php
require '../connection.php';

// Function to fetch student courses data
function fetchStudentCourses($conn) {
    $sql = "SELECT s.StudentID, s.FirstName, s.LastName, m.MajorName, GROUP_CONCAT(c.CourseName SEPARATOR ', ') AS Courses
            FROM student s
            INNER JOIN major m ON s.MajorID = m.MajorID
            INNER JOIN major_course_jnct mc ON m.MajorID = mc.MajorID
            INNER JOIN course c ON mc.CourseID = c.CourseID
            GROUP BY s.StudentID";

    $result = $conn->query($sql);

    $student_courses = array();
    while ($row = $result->fetch_assoc()) {
        $student_courses[] = array(
            'student_id' => $row['StudentID'],
            'first_name' => $row['FirstName'],
            'last_name' => $row['LastName'],
            'major_name' => $row['MajorName'],
            'courses' => $row['Courses']
        );
    }

    return $student_courses;
}

// Fetch student courses data
$student_courses = fetchStudentCourses($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Course Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .even-row {
            background-color: #99a6b6;
        }
    </style>
</head>
<body>
    <h2>Student Course Report</h2>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Major Name</th>
                <th>Courses</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($student_courses as $index => $student): ?>
                <tr class="<?php echo ($index % 2 == 0) ? 'even-row' : ''; ?>">
                    <td><?php echo $student['student_id']; ?></td>
                    <td><?php echo $student['first_name']; ?></td>
                    <td><?php echo $student['last_name']; ?></td>
                    <td><?php echo $student['major_name']; ?></td>
                    <td><?php echo $student['courses']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
