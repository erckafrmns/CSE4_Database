<?php
require '../connection.php';
require_once('../tcpdf/tcpdf.php');

// Function to fetch student courses data based on sorting criteria and search query
function fetchStudentCourses($conn, $sort_criteria = '', $sort_order = '', $search_query = '') {
    $sql = "SELECT s.StudentID, s.FirstName, s.LastName, m.MajorName, GROUP_CONCAT(c.CourseName SEPARATOR '<br>') AS Courses
            FROM student s
            INNER JOIN major m ON s.MajorID = m.MajorID
            INNER JOIN major_course_jnct mc ON m.MajorID = mc.MajorID
            INNER JOIN course c ON mc.CourseID = c.CourseID
            GROUP BY s.StudentID";

    if (!empty($search_query)) {
        $sql .= " HAVING s.StudentID LIKE '%$search_query%' OR s.LastName LIKE '%$search_query%' OR m.MajorName LIKE '%$search_query%'";
    }

    if (!empty($sort_criteria) && !empty($sort_order)) {
        $valid_criteria = ['StudentID', 'FirstName', 'LastName', 'MajorName'];
        $valid_orders = ['ASC', 'DESC'];

        if (in_array($sort_criteria, $valid_criteria) && in_array($sort_order, $valid_orders)) {
            $sql .= " ORDER BY $sort_criteria $sort_order";
        } else {
            die("Invalid sorting criteria or order.");
        }
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
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
    } else {
        return array();
    }
}

$search_query = isset($_GET['search_input']) ? $_GET['search_input'] : '';
$sort_criteria = isset($_GET['sort_criteria']) ? $_GET['sort_criteria'] : '';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : '';

$student_courses = fetchStudentCourses($conn, $sort_criteria, $sort_order, $search_query);

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);
$html = '<h2>Student-Courses Report</h2>';
$html .= '<table border="1" cellpadding="5">
            <thead>
                <tr style="background-color: #99a6b6;">
                    <th width="12%">Student ID</th>
                    <th width="18%">First Name</th>
                    <th width="20%">Last Name</th>
                    <th width="20%">Major Name</th>
                    <th width="30%">Courses</th>
                </tr>
            </thead>
            <tbody>';

foreach ($student_courses as $student) {
    $html .= '<tr>
                <td width="12%">' . $student['student_id'] . '</td>
                <td width="18%">' . $student['first_name'] . '</td>
                <td width="20%">' . $student['last_name'] . '</td>
                <td width="20%">' . $student['major_name'] . '</td>
                <td width="30%">' . $student['courses'] . '</td>
              </tr>';
}

if (empty($student_courses)) {
    $html .= '<tr><td colspan="5">No results found</td></tr>';
}

$html .= '</tbody></table>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('student_courses_report.pdf', 'D');
?>
