<?php
require '../connection.php';
require_once('../tcpdf/tcpdf.php');

function fetchDepartmentData($conn, $sort_criteria = '', $sort_order = '', $search_query = '') {
    $sql = "SELECT d.DepartmentID, d.DepartmentName, d.Location FROM department d";

    $where_clauses = [];
    if (!empty($search_query)) {
        $search_query = $conn->real_escape_string($search_query);
        $where_clauses[] = "(d.DepartmentID LIKE '%$search_query%' OR d.DepartmentName LIKE '%$search_query%' OR d.Location LIKE '%$search_query%')";
    }

    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    if (!empty($sort_criteria) && !empty($sort_order)) {
        $valid_criteria = ['DepartmentID', 'DepartmentName', 'Location'];
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
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

$result = fetchDepartmentData($conn, $sort_criteria, $sort_order, $search_query);

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);
$html = '<h2>Department Report</h2>';
$html .= '<table border="1" cellpadding="5">
            <thead>
                <tr style="background-color: #99a6b6;">
                    <th width="8%">No.</th>
                    <th width="15%">Department ID</th>
                    <th width="35%">Department Name</th>
                    <th width="42%">Location</th>
                </tr>
            </thead>
            <tbody>';

$count = 1;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td width="8%">' . $count++ . '</td>
                    <td width="15%">' . $row['DepartmentID'] . '</td>
                    <td width="35%">' . $row['DepartmentName'] . '</td>
                    <td width="42%">' . $row['Location'] . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="4">No results found</td></tr>';
}

$html .= '</tbody></table>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('department_report.pdf', 'D');
?>
