<?php
require '../connection.php';
require_once('../tcpdf/tcpdf.php');

function fetchStudentData($conn, $selected_major = '', $selected_department = '', $sort_criteria = '', $sort_order = '', $search_query = '') {
    $sql = "SELECT s.StudentID, s.FirstName, s.LastName, m.MajorID, m.MajorName, d.DepartmentID, d.DepartmentName
            FROM student s
            JOIN major m ON s.MajorID = m.MajorID
            JOIN department d ON m.DepartmentID = d.DepartmentID";

    $where_clauses = [];
    if (!empty($selected_major)) {
        $where_clauses[] = "s.MajorID = '$selected_major'";
    }
    if (!empty($selected_department)) {
        $where_clauses[] = "d.DepartmentID = '$selected_department'";
    }
    if (!empty($search_query)) {
        $search_query = $conn->real_escape_string($search_query);
        $where_clauses[] = "(s.StudentID LIKE '%$search_query%' OR s.FirstName LIKE '%$search_query%' OR s.LastName LIKE '%$search_query%' OR m.MajorName LIKE '%$search_query%' OR m.MajorID LIKE '%$search_query%' OR d.DepartmentName LIKE '%$search_query%')";
    }

    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    if (!empty($sort_criteria) && !empty($sort_order)) {
        $valid_criteria = ['StudentID', 'FirstName', 'LastName', 'MajorName', 'DepartmentName'];
        $valid_orders = ['ASC', 'DESC'];

        if (in_array($sort_criteria, $valid_criteria) && in_array($sort_order, $valid_orders)) {
            $sql .= " ORDER BY $sort_criteria $sort_order";
        } else {
            die("Invalid sorting criteria or order.");
        }
    }

    return $conn->query($sql);
}

$selected_major = isset($_GET['select_major']) ? $_GET['select_major'] : '';
$selected_department = isset($_GET['select_department']) ? $_GET['select_department'] : '';
$sort_criteria = isset($_GET['sort_criteria']) ? $_GET['sort_criteria'] : '';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : '';
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

$result = fetchStudentData($conn, $selected_major, $selected_department, $sort_criteria, $sort_order, $search_query);

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

$html = '<h2>Student Report</h2>';
$html .= '<table border="1" cellpadding="5">
            <thead>
                <tr style="background-color: #99a6b6;">
                    <th width="5%">No.</th>
                    <th width="12%">Student ID</th>
                    <th width="15%">First Name</th>
                    <th width="15%">Last Name</th>
                    <th width="11%">Major ID</th>
                    <th width="15%">Major Name</th>
                    <th width="12%">Department ID</th>
                    <th width="15%">Department Name</th>
                </tr>
            </thead>
            <tbody>';

$count = 1;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td width="5%">' . $count++ . '</td>
                    <td width="12%">' . $row['StudentID'] . '</td>
                    <td width="15%">' . $row['FirstName'] . '</td>
                    <td width="15%">' . $row['LastName'] . '</td>
                    <td width="11%">' . $row['MajorID'] . '</td>
                    <td width="15%">' . $row['MajorName'] . '</td>
                    <td width="12%">' . $row['DepartmentID'] . '</td>
                    <td width="15%">' . $row['DepartmentName'] . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="8">No results found</td></tr>';
}

$html .= '</tbody></table>';
$pdf->writeHTML($html);

$pdf->Output('student_report.pdf', 'D');
?>
