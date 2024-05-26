<?php
require '../connection.php';

// Function to fetch unique credits
function fetchUniqueCredits($conn) {
    $sql = "SELECT DISTINCT Credits FROM course ORDER BY Credits ASC";
    $result = $conn->query($sql);

    $creditOptions = '';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $creditOptions .= '<option value="' . $row["Credits"] . '">' . $row["Credits"] . '</option>';
        }
    }
    return $creditOptions;
}

$creditOptions = fetchUniqueCredits($conn);

// Function to fetch course data based on the sorting criteria and selected credits
function fetchCourse($conn, $sort_criteria = '', $sort_order = '', $selected_credits = '') {
    $sql = "SELECT c.CourseID, c.CourseName, c.Credits FROM course c";
    
    if (!empty($selected_credits)) {
        $sql .= " WHERE c.Credits = " . intval($selected_credits);
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

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $count++ . "</td>";
            echo "<td>" . $row["CourseID"] . "</td>";
            echo "<td>" . $row["CourseName"] . "</td>";
            echo "<td>" . $row["Credits"] . "</td>";
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
    $selected_credits = isset($_GET['selected_credits']) ? $_GET['selected_credits'] : '';
    fetchCourse($conn, $sort_criteria, $sort_order, $selected_credits);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Report</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav>
        <h2><i class="fa-brands fa-wpforms fa-sm" style="color: #ffffff; font-style: italic;"></i> FORMS</h2>
        <div class="forms-items">
            <a href="../forms/student.php"><i class="fa-solid fa-user fa-sm"></i> STUDENT</a>
            <a href="../forms/major.php"><i class="fa-solid fa-book fa-sm"></i> MAJOR</a>
            <a href="../forms/department.php"><i class="fa-solid fa-building-columns fa-sm"></i> DEPARTMENT</a>
            <a href="../forms/course.php"><i class="fa-solid fa-book-open-reader fa-sm"></i> COURSE</a>
        </div>
        <button onclick="location.href='studentReport.php'" class="tabs"><i class="fa-regular fa-file-lines"></i> Reports</button>
    </nav>

    <div class="wrapper">
        <div class="report-header">
            <ul>
                <li id="reportHead">Course Report     <i class="fa-solid fa-caret-down fa-sm"></i></li>
                <ul class="dropdown">
                    <li><a href="studentReport.php">Student Report</a></li>
                    <li><a href="majorReport.php">Major Report</a></li>
                    <li><a href="departmentReport.php">Department Report</a></li>
                    <li><a href="majorCourseReport.php">Major-Course Report</a></li>
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
                        <option value="CourseID">Course ID</option>
                        <option value="CourseName">Course Name</option>
                        <option value="Credits">Credits</option>
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
                    <select name="select_credits" id="select_credits">
                        <option value="">All Credits</option>
                        <?php echo $creditOptions; ?>
                    </select>
                </div>
            </div>
            <button class="courseReport-download">Download PDF <i class="fa-solid fa-download"></i></button>
        </div>
        <div class="report-table">
            <table>
                <thead>
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Course ID</th>
                        <th scope="col">Course Name</th>
                        <th scope="col">Credits</th>
                    </tr>
                </thead>
                <tbody id="report-table-body">
                    <?php fetchCourse($conn); ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function fetchFilteredData() {
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();
                var selectedCredits = $('#select_credits').val();

                $.ajax({
                    url: 'courseReport.php',
                    type: 'GET',
                    data: {
                        ajax: 1,
                        sort_criteria: sortCriteria,
                        sort_order: sortOrder,
                        selected_credits: selectedCredits
                    },
                    success: function(response) {
                        $('#report-table-body').html(response);
                    }
                });
            }

            $('#sort_criteria, #sort_order, #select_credits').change(function() {
                fetchFilteredData();
            });

            // Initial fetch
            fetchFilteredData();

            // Download PDF
            $('.courseReport-download').click(function() {
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();
                var selectedCredits = $('#select_credits').val();

                window.location.href = '../generatePDF/coursePDF.php?sort_criteria=' + sortCriteria + '&sort_order=' + sortOrder + '&selected_credits=' + selectedCredits;
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
