<?php
require '../connection.php';

// Function to fetch student data based on the sorting criteria
function fetchDepartment($conn, $sort_criteria = '', $sort_order = '') {
    $sql = "SELECT d.DepartmentID, d.DepartmentName, d.Location
            FROM department d";


    if (!empty($sort_criteria) && !empty($sort_order)) {
        $valid_criteria = ['DepartmentID', 'DepartmentName', 'Location'];
        $valid_orders = ['ASC', 'DESC'];

        if (in_array($sort_criteria, $valid_criteria) && in_array($sort_order, $valid_orders)) {
            $sql .= " ORDER BY $sort_criteria $sort_order";
        } else {
            die("Invalid sorting criteria or order.");
        }
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $count++ . "</td>";
            echo "<td>" . $row["DepartmentID"] . "</td>";
            echo "<td>" . $row["DepartmentName"] . "</td>";
            echo "<td>" . $row["Location"] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No results found</td></tr>";
    }
}

// Check if the request is an AJAX request and fetch the filtered and sorted data
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $sort_criteria = isset($_GET['sort_criteria']) ? $_GET['sort_criteria'] : '';
    $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : '';
    fetchDepartment($conn, $sort_criteria, $sort_order);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Report</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav>
        <h2><i class="fa-brands fa-wpforms fa-sm" style="color: #ffffff; font-style: italic;"></i> FORMS</h2>
        <div class="forms-items">
            <a href="../index.php"><i class="fa-solid fa-user fa-sm"></i> STUDENT</a>
            <a href="../forms/major.php"><i class="fa-solid fa-book fa-sm"></i> MAJOR</a>
            <a href="../forms/department.php"><i class="fa-solid fa-building-columns fa-sm"></i> DEPARTMENT</a>
            <a href="../forms/course.php"><i class="fa-solid fa-book-open-reader fa-sm"></i> COURSE</a>
        </div>
        <button onclick="location.href='studentReport.php'" class="tabs"><i class="fa-regular fa-file-lines"></i> Reports</button>
    </nav>

    <div class="wrapper">
        <div class="report-header">
            <ul>
                <li id="reportHead">Department Report     <i class="fa-solid fa-caret-down fa-sm"></i></li>
                <ul class="dropdown">
                    <li><a href="studentReport.php">Student Report</a></li>
                    <li><a href="majorReport.php">Major Report</a></li>
                    <li><a href="majorCourseReport.php">Major-Course Report</a></li>
                    <li><a href="courseReport.php">Course Report</a></li>
                    <li><a href="studentCoursesReport.php">Student-Courses Report</a></li>
                </ul>
            </ul>    
        </div>
        <div class="report-select">
            <div class="sort">
                <h5><i class="fa-solid fa-tornado fa-flip-horizontal fa-sm"></i>     Sort:</h5>
                <div class="select-container">
                    <select name="sort_criteria" id="sort_criteria">
                        <option value="">Sort Criteria</option>
                        <option value="DepartmentID">Department ID</option>
                        <option value="DepartmentName">Department Name</option>
                        <option value="Location">Location</option>
                    </select>
                    <select name="sort_order" id="sort_order">
                        <option value="">Sort Order</option>
                        <option value="ASC">Ascending</option>
                        <option value="DESC">Descending</option>
                    </select>
                </div>
            </div>
            <button class="departmentReport-download">Download PDF <i class="fa-solid fa-download"></i></button>
        </div>
        <div class="report-table">
            <table>
                <thead>
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Department ID</th>
                        <th scope="col">Department Name</th>
                        <th scope="col">Location</th>
                    </tr>
                </thead>
                <tbody id="report-table-body">
                    <?php fetchDepartment($conn); ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function fetchFilteredData() {
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();

                $.ajax({
                    url: 'departmentReport.php',
                    type: 'GET',
                    data: {
                        ajax: 1,
                        sort_criteria: sortCriteria,
                        sort_order: sortOrder
                    },
                    success: function(response) {
                        $('#report-table-body').html(response);
                    }
                });
            }

            $('#sort_criteria, #sort_order').change(function() {
                fetchFilteredData();
            });

            // Initial fetch
            fetchFilteredData();

            // Download PDF
            $('.departmentReport-download').click(function() {
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();

                window.location.href = '../generatePDF/departmentPDF.php?sort_criteria=' + sortCriteria + '&sort_order=' + sortOrder;
            });
        });
    </script>
    <script>
        document.getElementById('reportHead').addEventListener('click', function() {
            var dropdown = document.querySelector('ul .dropdown');
            if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        });
    </script>
</body>
</html>
