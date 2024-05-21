<?php
require 'connection.php';

//Join the tables
$sql = "SELECT s.StudentID, s.FirstName, s.LastName, s.MajorID, m.MajorName, d.DepartmentID, d.DepartmentName
FROM student s
JOIN major m ON s.MajorID = m.MajorID
JOIN department d ON m.DepartmentID = d.DepartmentID";

// Fetch all available majors from the database
$majorQuery = "SELECT * FROM major";
$majorResult = mysqli_query($conn, $majorQuery);
$majorOptions = '';
while ($row = mysqli_fetch_assoc($majorResult)) {
    $majorOptions .= "<option value='{$row['MajorID']}'>{$row['MajorName']}</option>";
}

// Fetch all available department from the database
$departmentQuery = "SELECT * FROM department";
$departmentResult = mysqli_query($conn, $departmentQuery);
$deptOptions = '';
while ($row = mysqli_fetch_assoc($departmentResult)) {
    $deptOptions .= "<option value='{$row['DepartmentID']}'>{$row['DepartmentName']}</option>";
}

// Initialize sorting and filtering variables
$sort_criteria = isset($_GET['sort_criteria']) ? $_GET['sort_criteria'] : '';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : '';


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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>
    
    <<nav>
        <h2><i class="fa-brands fa-wpforms fa-sm" style="color: #ffffff; font-style: italic;"></i>                  FORMS</h2>
        <div class="forms-items">
            <a href="index.php"><i class="fa-solid fa-user fa-sm"></i>               STUDENT</a>
            <a href="major.php"><i class="fa-solid fa-book fa-sm"></i>               MAJOR</a>
            <a href="department.php"><i class="fa-solid fa-building-columns fa-sm"></i>               DEPARTMENT</a>
            <a href="course.php"><i class="fa-solid fa-book-open-reader fa-sm"></i>               COURSE</a>
        </div>
        <button onclick="location.href='reports.php'" class="tabs"><i class="fa-regular fa-file-lines"></i>            Reports</button>
    </nav>

    <div class="wrapper">
        <div class="report-header">
            <h1>Student Report</h1>
        </div>
        <div class="report-select">
            <form method="GET" action="reports.php">
                <select name="sort_criteria" id="sort_criteria">
                    <option value="">Select Sort Criteria</option>
                    <option value="StudentID">Student ID</option>
                    <option value="FirstName">First Name</option>
                    <option value="LastName">Last Name</option>
                    <option value="MajorName">Major Name</option>
                    <option value="DepartmentName">Department Name</option>
                </select>
                <select name="sort_order" id="sort_order">
                    <option value="">Select Sort Order</option>
                    <option value="ASC">Ascending</option>
                    <option value="DESC">Descending</option>
                </select>
                <button type="submit">Sort</button>
            </form>
            <select name="select-major" id="select-major">
                <option value="">Select Major</option>
                <?php echo $majorOptions; ?>
            </select>
            <select name="select-department" id="select-department">
                <option value="">Select Department</option>
                <?php echo $deptOptions; ?>
            </select>
            <div class="btn">
                <button>Download PDF</button>
            </div>
        </div>
        <div class="student-table">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Student ID</th>
                        <th scope="col">FIrst name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Major ID</th>
                        <th scope="col">Major Name</th>
                        <th scope="col">Department ID</th>
                        <th scope="col">Department Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
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
                        echo "<tr><td colspan='7'>No results found</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            function fetchFilteredData() {
                var selectedMajor = $('#select-major').val();

                $.ajax({
                    url: 'fetch_students_by_major.php',
                    type: 'GET',
                    data: {
                        select_major: selectedMajor
                    },
                    success: function(response) {
                        $('#student-table-body').html(response);
                    }
                });
            }

            $('#select-major').change(function() {
                fetchFilteredData();
            });

            // Initial fetch
            fetchFilteredData();
        });
    </script>
</body>
</html>