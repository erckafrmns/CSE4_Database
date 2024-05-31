<?php
session_start();
include('../connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

$total_departments = $conn->query("SELECT COUNT(*) AS count FROM department")->fetch_assoc()['count'];

function fetchDepartment($conn, $sort_criteria = '', $sort_order = '', $search_query = '') {
    $sql = "SELECT d.DepartmentID, d.DepartmentName, d.Location
            FROM department d";

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

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr id='row-{$row["DepartmentID"]}'>";
            echo "<td>" . $count++ . "</td>";
            echo "<td>" . $row["DepartmentID"] . "</td>";
            echo "<td>" . $row["DepartmentName"] . "</td>";
            echo "<td>" . $row["Location"] . "</td>";
            echo "<td class='operationBTN'>
                    <button class='update' data-id='{$row["DepartmentID"]}'><i class='fa-solid fa-pen-to-square fa-sm'></i>   Update</button>
                    <button class='delete' data-id='{$row["DepartmentID"]}'><i class='fa-solid fa-trash-can'></i>   Delete</button>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No results found</td></tr>";
    }
}

if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $sort_criteria = isset($_GET['sort_criteria']) ? $_GET['sort_criteria'] : '';
    $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : '';
    $search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
    fetchDepartment($conn, $sort_criteria, $sort_order, $search_query);
    exit;
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Report</title>
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
                <p>    <?php echo $total_departments; ?></p>  
            </div>
            <h1><i class="fa-solid fa-building-columns fa-sm"></i>  Department Report</h1>
            <button class="downloadReport">Download Report <i class="fa-solid fa-download"></i></button>
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
                        <th scope="col">No.</th>
                        <th scope="col">Department ID</th>
                        <th scope="col">Department Name</th>
                        <th scope="col">Location</th>
                        <th scope="col">Operations</th>
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
                var searchQuery = $('#searchQuery').val();

                $.ajax({
                    url: 'departmentReport.php',
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

                window.location.href = '../generatePDF/departmentPDF.php?sort_criteria=' + sortCriteria + '&sort_order=' + sortOrder + '&search_query=' + searchQuery;
            });


            $(document).on('click', '.update', function() {
                var departmentID = $(this).data('id');
                $.ajax({
                    url: 'getDepartmentDetails.php',
                    type: 'GET',
                    data: { DepartmentID: departmentID },
                    success: function(data) {
                        var department = JSON.parse(data);
                        (async () => {
                            const { value: formValues } = await Swal.fire({
                                title: 'Update Department',
                                html:
                                `<input class="swal2-input" id="DepartmentID" value="${department.DepartmentID}" placeholder="Department ID" readonly>` + '<br>' +
                                `<input class="swal2-input" id="DepartmentName" value="${department.DepartmentName}" placeholder="Department Name">` + '<br>' +
                                `<input class="swal2-input" id="Location" value="${department.Location}" placeholder="Location">`,
                                showDenyButton: true,
                                denyButtonText: `Cancel`,
                                confirmButtonColor: "#2C3E50",
                                confirmButtonText: "Update",
                                width: 600
                            }).then((result) => {
                                if (result.isDenied) {
                                    Swal.fire({
                                        icon: "error",
                                        title: "CANCELED",
                                        confirmButtonColor: "#2C3E50"
                                    });
                                } else if (result.isConfirmed) {
                                    var data = {
                                        DepartmentID: $('#DepartmentID').val(),
                                        DepartmentName: $('#DepartmentName').val(),
                                        Location: $('#Location').val()
                                    };
                                    $.ajax({
                                        url: 'updateDepartment.php',
                                        type: 'POST',
                                        data: data,
                                        success: function() {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'UPDATE SUCCESSFUL',
                                                confirmButtonColor: "#2C3E50",
                                                html:
                                                'Department ID: ' + data['DepartmentID'] + '<br>' +
                                                'Department Name: ' + data['DepartmentName'] + '<br>' +
                                                'Location: ' + data['Location']
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

            $(document).on('click', '.delete', function() {
                var departmentID = $(this).data('id');
                Swal.fire({
                    icon: "warning",
                    title: "DELETE " + departmentID,
                    text: "Deleting this department will also remove all associated records. Are you sure you want to proceed?",
                    showDenyButton: true,
                    denyButtonText: `Cancel`,
                    confirmButtonColor: "#2C3E50",
                    confirmButtonText: "Delete"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'deleteDepartment.php',
                            type: 'POST',
                            data: { DepartmentID: departmentID },
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