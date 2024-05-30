<?php
session_start();
include('../connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch all available majors and departments from the database
$deptOptions = getOptions($conn, "department", "DepartmentID", "DepartmentName");
$total_majors = $conn->query("SELECT COUNT(*) AS count FROM major")->fetch_assoc()['count'];

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
function fetchMajor($conn, $selected_department = '', $sort_criteria = '', $sort_order = '', $search_query = '') {
    $sql = "SELECT m.MajorID, m.MajorName, d.DepartmentID, d.DepartmentName
            FROM major m
            JOIN department d ON m.DepartmentID = d.DepartmentID";

    $where_clauses = [];

    if (!empty($selected_department)) {
        $where_clauses[] = "d.DepartmentID = '$selected_department'";
    }

    if (!empty($search_query)) {
        $search_query = $conn->real_escape_string($search_query);
        $where_clauses[] = "(m.MajorID LIKE '%$search_query%' OR m.MajorName LIKE '%$search_query%' OR d.DepartmentID LIKE '%$search_query%' OR d.DepartmentName LIKE '%$search_query%')";
    }

    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    if (!empty($sort_criteria) && !empty($sort_order)) {
        $valid_criteria = ['MajorID', 'MajorName', 'DepartmentID', 'DepartmentName'];
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
            echo "<tr id='row-{$row["MajorID"]}'>";
            echo "<td>" . $count++ . "</td>";
            echo "<td>" . $row["MajorID"] . "</td>";
            echo "<td>" . $row["MajorName"] . "</td>";
            echo "<td>" . $row["DepartmentID"] . "</td>";
            echo "<td>" . $row["DepartmentName"] . "</td>";
            echo "<td class='operationBTN'>
                    <button class='update' onclick='updateStudent({$row["MajorID"]})'><i class='fa-solid fa-pen-to-square fa-sm'></i>   Update</button>
                    <button class='delete' onclick='deleteStudent({$row["MajorID"]})'><i class='fa-solid fa-trash-can'></i>   Delete</button>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No results found</td></tr>";
    }
}

if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $selected_department = isset($_GET['select_department']) ? $_GET['select_department'] : '';
    $sort_criteria = isset($_GET['sort_criteria']) ? $_GET['sort_criteria'] : '';
    $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : '';
    $search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
    fetchMajor($conn, $selected_department, $sort_criteria, $sort_order, $search_query);
    exit;
}

// Handle Delete Request
if (isset($_POST['delete_student_id'])) {
    $student_id = $_POST['delete_student_id'];
    $delete_sql = "DELETE FROM student WHERE StudentID = '$student_id'";
    if ($conn->query($delete_sql)) {
        echo "Student deleted successfully.";
    } else {
        echo "Error deleting student.";
    }
    exit;
}

// Handle Update Request
if (isset($_POST['update_student_id'])) {
    $student_id = $_POST['update_student_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $major_id = $_POST['major_id'];

    $update_sql = "UPDATE student SET FirstName='$first_name', LastName='$last_name', MajorID='$major_id' WHERE StudentID='$student_id'";
    if ($conn->query($update_sql)) {
        echo "Student updated successfully.";
    } else {
        echo "Error updating student.";
    }
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Major Report</title>
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
            <a href="studentCourseReport.php"><i class="fa-solid fa-user-graduate"></i>  STUDENT - COURSE</a>
        </div>
    </div>

    <div class="contentPanel">
        
        <div class="header">
            <div class="total">
                <i class="fa-solid fa-users"></i>   
                <p>    <?php echo $total_majors; ?></p>  
            </div>
            <h1><i class="fa-solid fa-graduation-cap fa-sm"></i>  Major Report</h1>
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
                        <option value="DepartmentID">Department ID</option>
                        <option value="DepartmentName">Department Name</option>
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
                    <select name="select_department" id="select_department">
                        <option value="">All Department</option>
                        <?php echo $deptOptions; ?>
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
                        <th scope="col">Major ID</th>
                        <th scope="col">Major Name</th>
                        <th scope="col">Department ID</th>
                        <th scope="col">Department Name</th>
                        <th scope="col">Operations</th>
                    </tr>
                </thead>
                <tbody id="report-table-body">
                    <?php fetchMajor($conn); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Update Student Modal -->
    <div id="updateModal" style="display:none;">
        <h2>Update Student</h2>
        <form id="updateForm">
            <input type="hidden" id="updateStudentID" name="update_student_id">
            <label for="updateFirstName">First Name:</label>
            <input type="text" id="updateFirstName" name="first_name" required><br>
            <label for="updateLastName">Last Name:</label>
            <input type="text" id="updateLastName" name="last_name" required><br>
            <label for="updateMajor">Major:</label>
            <select id="updateMajor" name="major_id" required>
                <?php echo $majorOptions; ?>
            </select><br>
            <button type="submit">Update</button>
            <button type="button" onclick="closeUpdateModal()">Cancel</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            function fetchFilteredData() {
                var selectedDepartment = $('#select_department').val();
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();
                var searchQuery = $('#searchQuery').val();

                $.ajax({
                    url: 'majorReport.php',
                    type: 'GET',
                    data: {
                        ajax: 1,
                        select_department: selectedDepartment,
                        sort_criteria: sortCriteria,
                        sort_order: sortOrder,
                        search_query: searchQuery
                    },
                    success: function(response) {
                        $('#report-table-body').html(response);
                    }
                });
            }

            $('#select_department, #sort_criteria, #sort_order').change(function() {
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
                var selectedDepartment = $('#select_department').val();
                var sortCriteria = $('#sort_criteria').val();
                var sortOrder = $('#sort_order').val();
                var searchQuery = $('#searchQuery').val();

                window.location.href = '../generatePDF/studentPDF.php?select_department=' + selectedDepartment + '&sort_criteria=' + sortCriteria + '&sort_order=' + sortOrder + '&search_query=' + searchQuery;
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