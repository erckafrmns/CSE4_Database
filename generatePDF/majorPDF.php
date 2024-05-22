<?php
require '../connection.php';
require_once('../tcpdf/tcpdf.php');

function fetchMajorData($conn, $selected_department = '', $sort_criteria = '', $sort_order = '') {
    $sql = "SELECT m.MajorID, m.MajorName, d.DepartmentID, d.DepartmentName
            FROM major m
            JOIN department d ON m.DepartmentID = d.DepartmentID";

    $where_clauses = [];
    if (!empty($selected_department)) {
        $where_clauses[] = "d.DepartmentID = '$selected_department'";
    }

    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    if (!empty($sort_criteria) && !empty($sort_order)) {
        $valid_criteria = ['MajorID', 'MajorName', 'DepartmentID', 'DepartmentName'];
        $valid_orders = ['ASC', 'DESC'];

        if (in_array($sort_criteria, $valid_criteria) && in_array($sort_order, $valid_orders)) {
            $sql .= " ORDER BY $sort_criteria $sort_order";
        } else {
            die("Invalid sorting criteria or order.");
        }
    }

    return $conn->query($sql);
}

$selected_department = isset($_GET['select_department']) ? $_GET['select_department'] : '';
$sort_criteria = isset($_GET['sort_criteria']) ? $_GET['sort_criteria'] : '';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : '';

$result = fetchMajorData($conn, $selected_department, $sort_criteria, $sort_order);

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);
$html = '<h2>Major Report</h2>';
$html .= '<table border="1" cellpadding="5">
            <thead>
                <tr style="background-color: #99a6b6;">
                    <th width="8%">No.</th>
                    <th width="16%">Major ID</th>
                    <th width="30%">Major Name</th>
                    <th width="16%">Dept ID</th>
                    <th width="30%">Dept Name</th>
                </tr>
            </thead>
            <tbody>';

$count = 1;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td width="8%">' . $count++ . '</td>
                    <td width="16%">' . $row['MajorID'] . '</td>
                    <td width="30%">' . $row['MajorName'] . '</td>
                    <td width="16%">' . $row['DepartmentID'] . '</td>
                    <td width="30%">' . $row['DepartmentName'] . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="5">No results found</td></tr>';
}

$html .= '</tbody></table>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('major_report.pdf', 'D');
?>
