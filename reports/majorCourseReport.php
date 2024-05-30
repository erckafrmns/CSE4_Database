<?php
session_start();
include('../connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

$total_majors = $conn->query("SELECT COUNT(*) AS count FROM major")->fetch_assoc()['count'];
$total_courses = $conn->query("SELECT COUNT(*) AS count FROM course")->fetch_assoc()['count'];

function fetchMajorCourses($conn, $sort_criteria = '', $sort_order = '', $search_query = '') {
    $sql = "SELECT m.MajorID, m.MajorName, c.CourseID, c.CourseName
            FROM major m
            INNER JOIN major_course_jnct mc ON m.MajorID = mc.MajorID
            INNER JOIN course c ON mc.CourseID = c.CourseID";

    $where_clauses = [];

    if (!empty($search_query)) {
        $search_query = $conn->real_escape_string($search_query);
        $where_clauses[] = "(m.MajorID LIKE '%$search_query%' OR m.MajorName LIKE '%$search_query%')";
    }

    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    if (!empty($sort_criteria) && !empty($sort_order)) {
        $valid_criteria = ['MajorID', 'MajorName'];
        $valid_orders = ['ASC', 'DESC'];

        if (in_array($sort_criteria, $valid_criteria) && in_array($sort_order, $valid_orders)) {
            $sql .= " ORDER BY $sort_criteria $sort_order";
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

if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $sort_criteria = isset($_GET['sort_criteria']) ? $_GET['sort_criteria'] : '';
    $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : '';
    $search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
    fetchMajorCourses($conn, $sort_criteria, $sort_order, $search_query);
    exit;
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Major-Course Report</title>
    <link rel="stylesheet" href="../css/adminNav.css">
    <link rel="stylesheet" href="../css/reports.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>

    <nav>
        <h1><span class="sarang">SARANG </span><span class="univ">UNIVERSITY</span></h1>
        <ul>
            <li class="dashboard"><a href="../adminAccount.php">Dashboard</a></li>
            <li class="menu-dropdown"><a href="../forms/student.php">Forms</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="../forms/student.php">Student Form</a></li>
                        <li><a href="../forms/major.php">Major Form</a></li>
                        <li><a href="../forms/department.php">Department Form</a></li>
                        <li><a href="../forms/course.php">Course Form</a></li>
                    </ul>
                </div>
            </li>
            <li class="menu-dropdown"><a href="studentReport.php">Reports</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="studentReport.php">Student</a></li>
                        <li><a href="majorReport.php">Major</a></li>
                        <li><a href="departmentReport.php">Department</a></li>
                        <li><a href="courseReport.php">Course</a></li>
                        <li><a href="majorCourseReport.php">Major-Course</a></li>
                        <li><a href="studentCoursesReport.php">Student-Course</a></li>
                    </ul>
                </div>
            </li>
            <li class="menu-dropdown"><a href="../account/editInfoAdmin.php">Account</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="../account/editInfoAdmin.php">Edit Information</a></li>
                        <li><a href="../account/changePassAdmin.php">Change Password</a></li>
                    </ul>
                </div>
            </li>
            <li><button class="SignOutBTN" onclick="window.location.href='../logout.php';">Sign Out</button></li>
        </ul>
    </nav>

    <div class="sidebar">
        <h2><i class="fa-solid fa-rectangle-list"></i> Reports</h2>
        <div class="forms-items">
            <a href="studentReport.php"><i class="fa-solid fa-user"></i>  STUDENT</a>
            <a href="majorReport.php"><i class="fa-solid fa-graduation-cap"></i>  MAJOR</a>
            <a href="departmentReport.php"><i class="fa-solid fa-building-columns"></i>  DEPARTMENT</a>
            <a href="courseReport.php"><i class="fa-solid fa-book-open-reader"></i>  COURSE</a>
            <a href="majorCourseReport.php"><i class="fa-solid fa-book"></i>  MAJOR - COURSE</a>
            <a href="studentCoursesReport.php"><i class="fa-solid fa-user-graduate"></i>  STUDENT - COURSE</a>
        </div>
    </div>

    <div class="contentPanel">
        
        <div class="header">
            <div class="total"></div>
            <h1><i class="fa-solid fa-book fa-sm"></i>  Major-Course Report</h1>
            <button class="downloadReport">Download Report <i class="fa-solid fa-download"></i></button>
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
                        <option value="ASC">Ascending</option>
                        <option value="DESC">Descending</option>
                    </select>
                </div>
            </div>

            <div class="search">
                <input type="text" class="searchTerm" id="searchQuery" placeholder="Search Here">
                <button type="submit" class="searchButton"><i class="fa fa-search"></i></button>
            </div>
            
        </div>


        <div class="report-table">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Major ID</th>
                        <th scope="col">Major Name</th>
                        <th scope="col">Course ID</th>
                        <th scope="col">Course Name</th>
                    </tr>
                </thead>
                <tbody id="report-table-body">
                    <?php fetchMajorCourses($conn); ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function fetchFilteredData() {
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();
                var searchQuery = $('#searchQuery').val();

                $.ajax({
                    url: 'majorCourseReport.php',
                    type: 'GET',
                    data: {
                        ajax: 1,
                        sort_criteria: sortCriteria,
                        sort_order: sortOrder,
                        search_query: searchQuery
                    },
                    success: function(response) {
                        $('#report-table-body').html(response);
                    }
                });
            }

            $('#sort_criteria, #sort_order').change(function() {
                fetchFilteredData();
            });

            $('.searchButton').click(function() {
                fetchFilteredData();
            });

            $('#searchQuery').on('keyup', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    fetchFilteredData();
                }
            });

            // Initial fetch
            fetchFilteredData();

            // Download PDF
            $('.downloadReport').click(function() {
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();
                var searchQuery = $('#searchQuery').val();

                window.location.href = '../generatePDF/majorCoursePDF.php?sort_criteria=' + sortCriteria + '&sort_order=' + sortOrder + '&searchQuery=' + searchQuery;
            });

            // Delete student
            window.deleteStudent = function(studentID) {
            if (confirm('Are you sure you want to delete this student?')) {
                $.ajax({
                    url: 'majorReport.php',
                    type: 'POST',
                    data: { delete_student_id: studentID },
                    success: function(response) {
                        alert(response);
                        fetchFilteredData();
                    }
                });
            }
        };
            // Update student
            window.updateStudent = function(studentID) {
                console.log('Update student:', studentID); // Debug log
                // Get student data from the row
                var row = $('#row-' + studentID);
                var firstName = row.find('td').eq(2).text();
                var lastName = row.find('td').eq(3).text();
                var majorID = row.find('td').eq(4).text();

                // Fill the update form with existing data
                $('#updateStudentID').val(studentID);
                $('#updateFirstName').val(firstName);
                $('#updateLastName').val(lastName);
                $('#updateMajor').val(majorID);

                // Show the update modal
                $('#updateModal').show();
            };

            $('#updateForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'studentReport.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        alert(response);
                        closeUpdateModal();
                        fetchFilteredData();
                    }
                });
            });

            window.closeUpdateModal = function() {
                $('#updateModal').hide();
            };
        });
    </script>
</body>
</html>