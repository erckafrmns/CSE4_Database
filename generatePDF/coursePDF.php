<?php
require '../connection.php';
require_once('../tcpdf/tcpdf.php');

function fetchCourseData($conn,  $selected_credits = '', $sort_criteria = '', $sort_order = '', $search_query = '') {
    $sql = "SELECT c.CourseID, c.CourseName, c.Credits FROM course c";
    
    $where_clauses = [];
    if (!empty($selected_credits)) {
        $where_clauses[] = "c.Credits = " . intval($selected_credits);
    }
    if (!empty($search_query)) {
        $search_query = $conn->real_escape_string($search_query);
        $where_clauses[] = "(c.CourseID LIKE '%$search_query%' OR c.CourseName LIKE '%$search_query%')";
    }

    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    if (!empty($sort_criteria) && !empty($sort_order)) {
        $valid_criteria = ['CourseID', 'CourseName', 'Credits'];
        $valid_orders = ['ASC', 'DESC'];

        if (in_array($sort_criteria, $valid_criteria) && in_array($sort_order, $valid_orders)) {
            $sql .= " ORDER BY $sort_criteria $sort_order";
        } else {
            die("Invalid sorting criteria or order.");
        }
    }

    return $conn->query($sql);
}

$sort_criteria = isset($_GET['sort_criteria']) ? $_GET['sort_criteria'] : '';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : '';
$selected_credits = isset($_GET['selected_credits']) ? $_GET['selected_credits'] : '';
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

$result = fetchCourseData($conn, $selected_credits, $sort_criteria, $sort_order, $search_query);

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 11);
$html = '<h2>Course Report</h2>';
$html .= '<table border="1" cellpadding="5">
            <thead>
                <tr style="background-color: #99a6b6;">
                    <th width="10%">No.</th>
                    <th width="25%">Course ID</th>
                    <th width="50%">Course Name</th>
                    <th width="15%">Credits</th>
                </tr>
            </thead>
            <tbody>';

$count = 1;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td width="10%">' . $count++ . '</td>
                    <td width="25%">' . $row['CourseID'] . '</td>
                    <td width="50%">' . $row['CourseName'] . '</td>
                    <td width="15%">' . $row['Credits'] . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="4">No results found</td></tr>';
}

$html .= '</tbody></table>';
$pdf->writeHTML($html);
$pdf->Output('course_report.pdf', 'D');
?>
