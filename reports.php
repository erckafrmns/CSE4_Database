<?php
require 'connection.php';

// Fetch all available majors from the database
$majorQuery = "SELECT * FROM major";
$majorResult = mysqli_query($conn, $majorQuery);
$majorOptions = '';
while ($row = mysqli_fetch_assoc($majorResult)) {
    $majorOptions .= "<option value='{$row['MajorID']}'>{$row['MajorName']}</option>";
}

// Fetch all available departments from the database
$departmentQuery = "SELECT * FROM department";
$departmentResult = mysqli_query($conn, $departmentQuery);
$deptOptions = '';
while ($row = mysqli_fetch_assoc($departmentResult)) {
    $deptOptions .= "<option value='{$row['DepartmentID']}'>{$row['DepartmentName']}</option>";
}

// Function to fetch student data based on the selected major, department, and sorting criteria
function fetchStudents($conn, $selected_major = '', $selected_department = '', $sort_criteria = '', $sort_order = '') {
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

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["StudentID"] . "</td>";
            echo "<td>" . $row["FirstName"] . "</td>";
            echo "<td>" . $row["LastName"] . "</td>";
            echo "<td>" . $row["MajorID"] . "</td>";
            echo "<td>" . $row["MajorName"] . "</td>";
            echo "<td>" . $row["DepartmentID"] . "</td>";
            echo "<td>" . $row["DepartmentName"] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No results found</td></tr>";
    }
}

// Check if the request is an AJAX request and fetch the filtered and sorted data
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $selected_major = isset($_GET['select_major']) ? $_GET['select_major'] : '';
    $selected_department = isset($_GET['select_department']) ? $_GET['select_department'] : '';
    $sort_criteria = isset($_GET['sort_criteria']) ? $_GET['sort_criteria'] : '';
    $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : '';
    fetchStudents($conn, $selected_major, $selected_department, $sort_criteria, $sort_order);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav>
        <h2><i class="fa-brands fa-wpforms fa-sm" style="color: #ffffff; font-style: italic;"></i> FORMS</h2>
        <div class="forms-items">
            <a href="index.php"><i class="fa-solid fa-user fa-sm"></i> STUDENT</a>
            <a href="major.php"><i class="fa-solid fa-book fa-sm"></i> MAJOR</a>
            <a href="department.php"><i class="fa-solid fa-building-columns fa-sm"></i> DEPARTMENT</a>
            <a href="course.php"><i class="fa-solid fa-book-open-reader fa-sm"></i> COURSE</a>
        </div>
        <button onclick="location.href='reports.php'" class="tabs"><i class="fa-regular fa-file-lines"></i> Reports</button>
    </nav>

    <div class="wrapper">
        <div class="report-header">
            <h1>Student Report</h1>
        </div>
        <div class="report-select">
            <div class="sort">
                <h5><i class="fa-solid fa-tornado fa-sm"></i>     Sort:</h5>
                <div class="select-container">
                    <select name="sort_criteria" id="sort_criteria">
                        <option value="">Sort Criteria</option>
                        <option value="StudentID">Student ID</option>
                        <option value="FirstName">First Name</option>
                        <option value="LastName">Last Name</option>
                        <option value="MajorName">Major Name</option>
                        <option value="DepartmentName">Department Name</option>
                    </select>
                    <select name="sort_order" id="sort_order">
                        <option value="">Sort Order</option>
                        <option value="ASC">Ascending</option>
                        <option value="DESC">Descending</option>
                    </select>
                </div>
            </div>
            <div class="filter">
                <h5><i class="fa-solid fa-filter fa-sm"></i>     Filter:</h5>
                <div class="select-container">
                    <select name="select-major" id="select-major">
                        <option value="">All Major</option>
                        <?php echo $majorOptions; ?>
                    </select>
                    <select name="select-department" id="select-department">
                        <option value="">All Department</option>
                        <?php echo $deptOptions; ?>
                    </select>
                </div>
            </div>
            <button class="report-download">Download PDF <i class="fa-solid fa-download"></i></button>
        </div>
        <div class="student-table">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Student ID</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Major ID</th>
                        <th scope="col">Major Name</th>
                        <th scope="col">Department ID</th>
                        <th scope="col">Department Name</th>
                    </tr>
                </thead>
                <tbody id="student-table-body">
                    <?php fetchStudents($conn); ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function fetchFilteredData() {
                var selectedMajor = $('#select-major').val();
                var selectedDepartment = $('#select-department').val();
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();

                $.ajax({
                    url: 'reports.php',
                    type: 'GET',
                    data: {
                        ajax: 1,
                        select_major: selectedMajor,
                        select_department: selectedDepartment,
                        sort_criteria: sortCriteria,
                        sort_order: sortOrder
                    },
                    success: function(response) {
                        $('#student-table-body').html(response);
                    }
                });
            }

            $('#select-major, #select-department, #sort_criteria, #sort_order').change(function() {
                fetchFilteredData();
            });

            // Initial fetch
            fetchFilteredData();
        });
    </script>
</body>
</html>
