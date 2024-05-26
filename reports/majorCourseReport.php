<?php
require '../connection.php';

// Function to fetch major courses data
function fetchMajorCourses($conn, $sort_criteria = '', $sort_order = '') {
    $sql = "SELECT m.MajorID, m.MajorName, c.CourseID, c.CourseName
            FROM major m
            INNER JOIN major_course_jnct mc ON m.MajorID = mc.MajorID
            INNER JOIN course c ON mc.CourseID = c.CourseID";

    // For search query
    $search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
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

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $major_courses = array();
        $prev_major_id = null;
        while ($row = $result->fetch_assoc()) {
            $major_id = $row['MajorID'];
            $major_name = $row['MajorName'];
            $course_id = $row['CourseID'];
            $course_name = $row['CourseName'];

            // Check if major ID has changed
            if ($major_id !== $prev_major_id) {
                $major_courses[$major_id] = array(
                    'major_id' => $major_id,
                    'major_name' => $major_name,
                    'courses' => array()
                );
            }

            // Add course to the major's courses
            $major_courses[$major_id]['courses'][] = array(
                'course_id' => $course_id,
                'course_name' => $course_name
            );

            // Update previous major ID
            $prev_major_id = $major_id;
        }

        $index = 0;
        foreach ($major_courses as $major_data) {
            $row_class = $index % 2 == 0 ? "odd-row" : "even-row";
            foreach ($major_data['courses'] as $course_index => $course) {
                echo "<tr class='$row_class'>";
                if ($course_index === 0) {
                    echo "<td rowspan='".count($major_data['courses'])."'>{$major_data['major_id']}</td>";
                    echo "<td rowspan='".count($major_data['courses'])."'>{$major_data['major_name']}</td>";
                }
                echo "<td>{$course['course_id']}</td>";
                echo "<td>{$course['course_name']}</td>";
                echo "</tr>";
            }
            $index++;
        }
    } else {
        echo "<tr><td colspan='4'>No results found</td></tr>";
    }
}

// Check if the request is an AJAX request and fetch the filtered and sorted data
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $sort_criteria = isset($_GET['sort_criteria']) ? $_GET['sort_criteria'] : '';
    $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : '';
    fetchMajorCourses($conn, $sort_criteria, $sort_order);
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Major-Course Report</title>
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
                <li id="reportHead">Major-Course Report     <i class="fa-solid fa-caret-down fa-sm"></i></li>
                <ul class="dropdown">
                    <li><a href="studentReport.php">Student Report</a></li>
                    <li><a href="majorReport.php">Major Report</a></li>
                    <li><a href="departmentReport.php">Department Report</a></li>
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
                        <option value="MajorID">Major ID</option>
                        <option value="MajorName">Major Name</option>
                    </select>
                    <select name="sort_order" id="sort_order">
                        <option value="">Sort Order</option>
                        <option value="ASC">Ascending</option>
                        <option value="DESC">Descending</option>
                    </select>
                </div>
            </div>
            <div class="filter">
                <h5>Search      <i class="fa-solid fa-magnifying-glass"></i></h5>
                <div class="select-container">
                    <input type="text" id="search_input" placeholder="MajorID or major name...">
                    <button id="search_button"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <button class="majorCourseReport-download">Download PDF <i class="fa-solid fa-download"></i></button>
        </div>
        <div class="majorCourseReport-table">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Major ID</th>
                        <th scope="col">Major Name</th>
                        <th scope="col">Course ID</th>
                        <th scope="col">Course Name</th>
                    </tr>
                </thead>
                <tbody id="majorCourseReport-table-body">
                    <?php fetchMajorCourses($conn); ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function fetchFilteredData() {
                var searchQuery = $('#search_input').val();
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();

                $.ajax({
                    url: 'majorCourseReport.php',
                    type: 'GET',
                    data: {
                        ajax: 1,
                        search_query: searchQuery,
                        sort_criteria: sortCriteria,
                        sort_order: sortOrder
                    },
                    success: function(response) {
                        $('#majorCourseReport-table-body').html(response);
                    }
                });
            }

            $('#search_input, #sort_criteria, #sort_order').change(function() {
                fetchFilteredData();
            });

            // Function to handle search input keypress
            $('#search_input').keypress(function(e) {
                if (e.which == 13) { // Check if Enter key is pressed
                    fetchFilteredData();
                }
            });

            $('#search_button').click(function() {
                fetchFilteredData();
            });

            // Initial fetch
            fetchFilteredData();

            // Download PDF
            $('.majorCourseReport-download').click(function() {
                var searchQuery = $('#search_input').val();
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();

                window.location.href = '../generatePDF/majorCoursePDF.php?search_input=' + searchQuery + '&sort_criteria=' + sortCriteria + '&sort_order=' + sortOrder;
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
