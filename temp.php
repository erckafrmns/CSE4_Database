<?php
require 'connection.php';

// Function to fetch major courses data
function fetchMajorCourses($conn) {
    $sql = "SELECT m.MajorID, m.MajorName, c.CourseID, c.CourseName
            FROM major m
            INNER JOIN major_course_jnct mc ON m.MajorID = mc.MajorID
            INNER JOIN course c ON mc.CourseID = c.CourseID
            ORDER BY m.MajorID, c.CourseID";

    $result = $conn->query($sql);

    $major_courses = array();
    $prev_major_id = null;
    while ($row = $result->fetch_assoc()) {
        $major_id = $row['MajorID'];
        $major_name = $row['MajorName'];
        $course_id = $row['CourseID'];
        $course_name = $row['CourseName'];

        // Check if major ID has changed
        if ($major_id !== $prev_major_id) {
            $major_courses[$major_id] = array(
                'major_id' => $major_id,
                'major_name' => $major_name,
                'courses' => array()
            );
        }

        // Add course to the major's courses
        $major_courses[$major_id]['courses'][] = array(
            'course_id' => $course_id,
            'course_name' => $course_name
        );

        // Update previous major ID
        $prev_major_id = $major_id;
    }

    return $major_courses;
}

// Fetch major courses data
$major_courses = fetchMajorCourses($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Major Course Report</title>
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
    <h2>Major Course Report</h2>
    <table>
        <thead>
            <tr>
                <th>Major ID</th>
                <th>Major Name</th>
                <th>Course ID</th>
                <th>Course Name</th>
            </tr>
        </thead>
        <tbody>
            <?php $row_class = ''; ?>
            <?php foreach ($major_courses as $major_id => $major_data): ?>
                <?php foreach ($major_data['courses'] as $index => $course): ?>
                    <tr class="<?php echo $row_class; ?>">
                        <?php if ($index === 0): ?>
                            <td rowspan="<?php echo count($major_data['courses']); ?>"><?php echo $major_data['major_id']; ?></td>
                            <td rowspan="<?php echo count($major_data['courses']); ?>"><?php echo $major_data['major_name']; ?></td>
                        <?php endif; ?>
                        <td><?php echo $course['course_id']; ?></td>
                        <td><?php echo $course['course_name']; ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php $row_class = ($row_class === '') ? 'even-row' : ''; // Toggle row class ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
