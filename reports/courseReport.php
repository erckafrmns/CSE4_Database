<?php
session_start();
include('../connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

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

$total_majors = $conn->query("SELECT COUNT(*) AS count FROM major")->fetch_assoc()['count'];

function fetchCourse($conn, $selected_credits = '', $sort_criteria = '', $sort_order = '', $search_query = '') {
    $sql = "SELECT c.CourseID, c.CourseName, c.Credits FROM course c";

    $where_clauses = [];

    if (!empty($selected_credits)) {
        $sql .= " WHERE c.Credits = " . intval($selected_credits);
    }

    if (!empty($search_query)) {
        $search_query = $conn->real_escape_string($search_query);
        $where_clauses[] = "(c.CourseID LIKE '%$search_query%' OR c.CourseName LIKE '%$search_query%' OR c.Credits LIKE '%$search_query%')";
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

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr id='row-{$row["CourseID"]}'>";
            echo "<td>" . $count++ . "</td>";
            echo "<td>" . $row["CourseID"] . "</td>";
            echo "<td>" . $row["CourseName"] . "</td>";
            echo "<td>" . $row["Credits"] . "</td>";
            echo "<td class='operationBTN'>
                    <button class='update' data-id='{$row["CourseID"]}'><i class='fa-solid fa-pen-to-square fa-sm'></i>   Update</button>
                    <button class='delete' data-id='{$row["CourseID"]}'><i class='fa-solid fa-trash-can'></i>   Delete</button>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No results found</td></tr>";
    }
}

if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $selected_credits = isset($_GET['selected_credits']) ? $_GET['selected_credits'] : '';
    $sort_criteria = isset($_GET['sort_criteria']) ? $_GET['sort_criteria'] : '';
    $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : '';
    $search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
    fetchCourse($conn, $selected_credits, $sort_criteria, $sort_order, $search_query);
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Report</title>
    <link rel="stylesheet" href="../css/adminNav.css">
    <link rel="stylesheet" href="../css/reports.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
    <script src="../sweetalert/sweetalert2.min.js"></script>
    <script src="../sweetalert/sweetalert2.min.js/sweetalert2.all.min.js"></script>
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
            <div class="total">
                <i class="fa-solid fa-chart-simple"></i>
                <p>    <?php echo $total_majors; ?></p>  
            </div>
            <h1><i class="fa-solid fa-book-open-reader fa-sm"></i>  Course Report</h1>
            <button class="downloadReport">Download Report <i class="fa-solid fa-download"></i></button>
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

            <div class="search">
                <input type="text" class="searchTerm" id="searchQuery" placeholder="Search Here">
                <button type="submit" class="searchButton"><i class="fa fa-search"></i></button>
            </div>
            
        </div>


        <div class="report-table">
            <table>
                <thead>
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Course ID</th>
                        <th scope="col">Course Name</th>
                        <th scope="col">Credits</th>
                        <th scope="col">Operations</th>
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
                var selectedCredits = $('#select_credits').val();
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();
                var searchQuery = $('#searchQuery').val();

                $.ajax({
                    url: 'courseReport.php',
                    type: 'GET',
                    data: {
                        ajax: 1,
                        selected_credits: selectedCredits,
                        sort_criteria: sortCriteria,
                        sort_order: sortOrder,
                        search_query: searchQuery
                    },
                    success: function(response) {
                        $('#report-table-body').html(response);
                    }
                });
            }

            $('#select_credits, #sort_criteria, #sort_order').change(function() {
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
                var selectedCredits = $('#select_credits').val();
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();
                var searchQuery = $('#searchQuery').val();

                window.location.href = '../generatePDF/coursePDF.php?selected_credits=' + selectedCredits + '&sort_criteria=' + sortCriteria + '&sort_order=' + sortOrder + '&search_query=' + searchQuery;
            });


            $(document).on('click', '.update', function() {
                var courseID = $(this).data('id');
                $.ajax({
                    url: 'getCourseDetails.php',
                    type: 'GET',
                    data: { CourseID: courseID },
                    success: function(data) {
                        var course = JSON.parse(data);
                        (async () => {
                            const { value: formValues } = await Swal.fire({
                                title: 'Update Course',
                                html:
                                `<input class="swal2-input" id="CourseID" value="${course.CourseID}" placeholder="Course ID" readonly>` + '<br>' +
                                `<input class="swal2-input" id="CourseName" value="${course.CourseName}" placeholder="Course Name">` + '<br>' +
                                `<input class="swal2-input" id="Credits" value="${course.Credits}" placeholder="Credits">`,
                                showDenyButton: true,
                                denyButtonText: `Cancel`,
                                confirmButtonColor: "#2C3E50",
                                confirmButtonText: "Update",
                                width: 600
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    var data = {
                                        CourseID: $('#CourseID').val(),
                                        CourseName: $('#CourseName').val(),
                                        Credits: $('#Credits').val()
                                    };
                                    $.ajax({
                                        url: 'updateCourse.php',
                                        type: 'POST',
                                        data: data,
                                        success: function() {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Update Successful',
                                                confirmButtonColor: "#2C3E50",
                                                html:
                                                'Course ID: ' + data['CourseID'] + '<br>' +
                                                'Course Name: ' + data['CourseName'] + '<br>' +
                                                'Credits: ' + data['Credits']
                                            });
                                            fetchFilteredData();
                                        }
                                    });
                                }
                            });
                        })();
                    }
                });
            });

            // Delete course
            $(document).on('click', '.delete', function() {
                var courseID = $(this).data('id');
                Swal.fire({
                    icon: "warning",
                    title: "Delete Course " + courseID,
                    text: "Are you sure you want to delete this course?",
                    showDenyButton: true,
                    denyButtonText: `Cancel`,
                    confirmButtonColor: "#2C3E50",
                    confirmButtonText: "Delete"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'deleteCourse.php',
                            type: 'POST',
                            data: { CourseID: courseID },
                            success: function() {
                                Swal.fire({
                                    icon: "success",
                                    title: "Record Deleted Successfully!",
                                    confirmButtonColor: "#2C3E50"
                                });
                                fetchFilteredData();
                            }
                        });
                    } else if (result.isDenied) {
                        Swal.fire({
                            icon: "error",
                            title: "Cancelled",
                            confirmButtonColor: "#2C3E50"
                        });
                    }
                });
            });

            
        });
    </script>
</body>
</html>