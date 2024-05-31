<?php
session_start();
include('../connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}



// Fetch all available majors and departments from the database
$majorOptions = getOptions($conn, "major", "MajorID", "MajorName");
$total_students = $conn->query("SELECT COUNT(*) AS count FROM student")->fetch_assoc()['count'];

// Fetch options function
function getOptions($conn, $table, $idColumn, $nameColumn) {
    $query = "SELECT * FROM $table";
    $result = mysqli_query($conn, $query);
    $options = '';
    while ($row = mysqli_fetch_assoc($result)) {
        $options .= "<option value='{$row[$idColumn]}'>{$row[$nameColumn]}</option>";
    }
    return $options;
}

// Function to fetch student data based on the selected major, department, and sorting criteria
function fetchStudents($conn, $selected_major = '', $sort_criteria = '', $sort_order = '', $search_query = '') {
    $sql = "SELECT s.StudentID, s.FirstName, s.LastName, s.Email, m.MajorID, m.MajorName
            FROM student s
            JOIN major m ON s.MajorID = m.MajorID";

    $where_clauses = [];

    if (!empty($selected_major)) {
        $where_clauses[] = "s.MajorID = '$selected_major'";
    }

    if (!empty($search_query)) {
        $search_query = $conn->real_escape_string($search_query);
        $where_clauses[] = "(s.StudentID LIKE '%$search_query%' OR s.FirstName LIKE '%$search_query%' OR s.LastName LIKE '%$search_query%' OR m.MajorName LIKE '%$search_query%' OR m.MajorID LIKE '%$search_query%' OR s.Email LIKE '%$search_query%')";
    }

    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    if (!empty($sort_criteria) && !empty($sort_order)) {
        $valid_criteria = ['StudentID', 'FirstName', 'LastName', 'MajorName', 'Email'];
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
            echo "<tr id='row-{$row["StudentID"]}'>";
            echo "<td>" . $count++ . "</td>";
            echo "<td>" . $row["StudentID"] . "</td>";
            echo "<td>" . $row["FirstName"] . "</td>";
            echo "<td>" . $row["LastName"] . "</td>";
            echo "<td>" . $row["MajorID"] . "</td>";
            echo "<td>" . $row["MajorName"] . "</td>";
            echo "<td>" . $row["Email"] . "</td>";
            echo "<td class='operationBTN'>
                    <button class='update' data-id='{$row["StudentID"]}'><i class='fa-solid fa-pen-to-square fa-sm'></i>   Update</button>
                    <button class='delete' data-id='{$row["StudentID"]}'><i class='fa-solid fa-trash-can'></i>   Delete</button>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No results found</td></tr>";
    }
}

if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $selected_major = isset($_GET['select_major']) ? $_GET['select_major'] : '';
    $sort_criteria = isset($_GET['sort_criteria']) ? $_GET['sort_criteria'] : '';
    $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : '';
    $search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
    fetchStudents($conn, $selected_major, $sort_criteria, $sort_order, $search_query);
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Report</title>
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
                <i class="fa-solid fa-users"></i>   
                <p>    <?php echo $total_students; ?></p>  
            </div>
            <h1><i class="fa-solid fa-user fa-sm"></i>  Student Report</h1>
            <button class="downloadReport">Download Report <i class="fa-solid fa-download"></i></button>
        </div>

        <div class="report-select">
            <div class="sort">
                <h5><i class="fa-solid fa-tornado fa-flip-horizontal fa-sm"></i>     Sort:</h5>
                <div class="select-container">
                    <select name="sort_criteria" id="sort_criteria">
                        <option value="">Sort Criteria</option>
                        <option value="StudentID">Student ID</option>
                        <option value="FirstName">First Name</option>
                        <option value="LastName">Last Name</option>
                        <option value="MajorName">Major Name</option>
                        <option value="Email">Email</option>
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
                    <select name="select_major" id="select_major">
                        <option value="">All Major</option>
                        <?php echo $majorOptions; ?>
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
                        <th scope="col">Student ID</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Major ID</th>
                        <th scope="col">Major Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Operations</th>
                    </tr>
                </thead>
                <tbody id="report-table-body">
                    <?php fetchStudents($conn); ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function fetchFilteredData() {
                var selectedMajor = $('#select_major').val();
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();
                var searchQuery = $('#searchQuery').val();

                $.ajax({
                    url: 'studentReport.php',
                    type: 'GET',
                    data: {
                        ajax: 1,
                        select_major: selectedMajor,
                        sort_criteria: sortCriteria,
                        sort_order: sortOrder,
                        search_query: searchQuery
                    },
                    success: function(response) {
                        $('#report-table-body').html(response);
                    }
                });
            }

            $('#select_major, #sort_criteria, #sort_order').change(function() {
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
                var selectedMajor = $('#select_major').val();
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();
                var searchQuery = $('#searchQuery').val();

                window.location.href = '../generatePDF/studentPDF.php?select_major=' + selectedMajor + '&sort_criteria=' + sortCriteria + '&sort_order=' + sortOrder + '&search_query=' + searchQuery;
            });


            $(document).on('click', '.update', function() {
                var studentID = $(this).data('id');
                // Fetch student details and open update modal
                $.ajax({
                    url: 'getStudentDetails.php',
                    type: 'GET',
                    data: { StudentID: studentID },
                    success: function(data) {
                        // Populate the form with fetched data and show Swal modal
                        var student = JSON.parse(data);
                        (async () => {
                            const { value: formValues } = await Swal.fire({
                                title: 'Update Record',
                                html:
                                `<input class="swal2-input" id="StudentID" value="${student.StudentID}" placeholder="Student ID" readonly>` + '<br>' +
                                `<input class="swal2-input" id="FirstName" value="${student.FirstName}" placeholder="First Name">` + '<br>' +
                                `<input class="swal2-input" id="LastName" value="${student.LastName}" placeholder="Last Name">` + '<br>' +
                                `<input class="swal2-input" id="MajorID" value="${student.MajorID}" placeholder="Major ID">` + '<br>' +
                                `<input class="swal2-input" id="Email" value="${student.Email}" placeholder="Email">`,
                                showDenyButton: true,
                                denyButtonText: `Cancel`,
                                confirmButtonColor: "#2C3E50",
                                confirmButtonText: "Update"
                            }).then((result) => {
                                if (result.isDenied) {
                                    Swal.fire({
                                        icon: "error",
                                        title: "CANCELED",
                                        confirmButtonColor: "#2C3E50"
                                    });
                                } else if (result.isConfirmed) {
                                    var data = {
                                        StudentID: $('#StudentID').val(),
                                        FirstName: $('#FirstName').val(),
                                        LastName: $('#LastName').val(),
                                        MajorID: $('#MajorID').val(),
                                        Email: $('#Email').val()
                                    };
                                    console.log(data);

                                    $.ajax({
                                        url: 'updateStudent.php',
                                        type: 'post',
                                        data: data,
                                        success:function(){
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'UPDATE SUCCESSFUL',
                                                confirmButtonColor: "#2C3E50",
                                                html:
                                                'StudentID : ' + data['StudentID'] + '<br>' +
                                                'FirstName : ' + data['FirstName'] + '<br>' +
                                                'LastName : ' + data['LastName'] + '<br>' +
                                                'MajorID : ' + data['MajorID'] + '<br>' +
                                                'Email : ' + data['Email'] 
                                            });
                                            fetchFilteredData();
                                        }
                                    });
                                    
                                }
                            })

                            
                        })();
                    }
                });
            });

            $(document).on('click', '.delete', function() {
                var studentID = $(this).data('id');
                Swal.fire({
                    icon: "warning",
                    title: "Are you sure?",
                    text: "Delete student with StudentID: " + studentID,
                    showDenyButton: true,
                    denyButtonText: `Cancel`,
                    confirmButtonColor: "#2C3E50",
                    confirmButtonText: "Delete"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'deleteStudent.php',
                            type: 'POST',
                            data: { StudentID: studentID },
                            success: function() {
                                Swal.fire({
                                    icon: "success",
                                    title: "SUCCESS",
                                    text: "Record Deleted Successfully!",
                                    confirmButtonColor: "#2C3E50"
                                });
                                fetchFilteredData();
                            }
                        });
                    } else if (result.isDenied) {
                        Swal.fire({
                            icon: "error",
                            title: "CANCELLED",
                            confirmButtonColor: "#2C3E50"
                        });
                    }
                });
            });


        });
    </script>

</body>
</html>