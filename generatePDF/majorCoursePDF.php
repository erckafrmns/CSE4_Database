<?php
require '../connection.php';
require_once('../tcpdf/tcpdf.php');

function fetchMajorCoursesData($conn, $search_query = '', $sort_criteria = '', $sort_order = '') {
    $sql = "SELECT m.MajorID, m.MajorName, c.CourseID, c.CourseName
            FROM major m
            INNER JOIN major_course_jnct mc ON m.MajorID = mc.MajorID
            INNER JOIN course c ON mc.CourseID = c.CourseID";

    if (!empty($search_query)) {
        $sql .= " WHERE m.MajorID LIKE '%$search_query%' OR m.MajorName LIKE '%$search_query%'";
    }

    if (!empty($sort_criteria) && !empty($sort_order)) {
        $valid_criteria = ['MajorID', 'MajorName'];
        $valid_orders = ['ASC', 'DESC'];

        if (in_array($sort_criteria, $valid_criteria) && in_array($sort_order, $valid_orders)) {
            $sql .= " ORDER BY $sort_criteria $sort_order, c.CourseID";
        } else {
            die("Invalid sorting criteria or order.");
        }
    }

    return $conn->query($sql);
}

$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
$sort_criteria = isset($_GET['sort_criteria']) ? $_GET['sort_criteria'] : '';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : '';

$result = fetchMajorCoursesData($conn, $search_query, $sort_criteria, $sort_order);

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

$html = '<h2>Major-Course Report</h2>';
$prev_major_id = null;
$first_row = true;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $major_id = $row['MajorID'];
        $major_name = $row['MajorName'];
        $course_id = $row['CourseID'];
        $course_name = $row['CourseName'];

        if ($major_id !== $prev_major_id) {
            if (!$first_row) {
                $html .= '</tbody></table>';
            }
            $html .= "<h3>$major_id | $major_name</h3>";
            $html .= '<table border="1" cellpadding="5"><thead><tr><th>Course ID</th><th>Course Name</th></tr></thead><tbody>';
            $prev_major_id = $major_id;
            $first_row = false;
        }

        $html .= "<tr><td>$course_id</td><td>$course_name</td></tr>";
    }
    $html .= '</tbody></table>';
} else {
    $html .= '<p>No results found</p>';
}

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('major_course_report.pdf', 'D');
?>
