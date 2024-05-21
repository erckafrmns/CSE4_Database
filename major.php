<?php
require 'connection.php';

if (isset($_POST["submit"])){
    $MajorID = $_POST["MajorID"];
    $MajorName = $_POST["MajorName"];
    $DepartmentID = $_POST["DepartmentID"];

    $query = "INSERT INTO major VALUES ('$MajorID', '$MajorName', '$DepartmentID')";
    mysqli_query($conn, $query);
    echo "<script> alert(''$MajorName' Major was Successfully Added!'); </script>";
}  

// Fetch all available department from the database
$departmentQuery = "SELECT * FROM department";
$departmentResult = mysqli_query($conn, $departmentQuery);
$deptOptions = '';
while ($row = mysqli_fetch_assoc($departmentResult)) {
    $deptOptions .= "<option value='{$row['DepartmentID']}'>{$row['DepartmentID']}</option>";
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Major Display and Form</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var majorIDInput = document.querySelector('input[name="MajorID"]');
            var checkIcon = document.createElement('i');
            var crossIcon = document.createElement('i');
            checkIcon.classList.add('fa-regular', 'fa-circle-check', 'check-icon');
            crossIcon.classList.add('fa-regular', 'fa-circle-xmark', 'cross-icon');
            majorIDInput.parentNode.appendChild(checkIcon);
            majorIDInput.parentNode.appendChild(crossIcon);

            majorIDInput.addEventListener('input', function() {
                var majorID = majorIDInput.value;
                if (majorID) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'check_major_id.php', true);
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            var response = xhr.responseText.trim(); // Trim response text
                            console.log('Response Text:', response);
                            if (response === 'exists') { // Use strict comparison
                                console.log('Here in exist statement');
                                crossIcon.style.transform = 'translateX(3350%)';
                                checkIcon.style.transform = 'translateX(-3350%)';
                            } else if (response === 'not_exists') { // Use strict comparison
                                console.log('Here in not exist statement');
                                checkIcon.style.transform = 'translateX(3350%)';
                                crossIcon.style.transform = 'translateX(-3350%)';
                            } else {
                                console.log('Here in neither or else statement');
                                checkIcon.style.transform = 'translateX(-3350%)';
                                crossIcon.style.transform = 'translateX(-3350%)';
                            }
                        }
                    };
                    xhr.send('majorID=' + majorID);
                } else {
                    checkIcon.style.transform = 'translateX(-3350%)';
                    crossIcon.style.transform = 'translateX(-3350%)';
                }
            });
        });
    </script>

</head>
<body>

    <nav>
        <h2><i class="fa-brands fa-wpforms fa-sm" style="color: #ffffff; font-style: italic;"></i>                  FORMS</h2>
        <div class="forms-items">
            <a href="index.php"><i class="fa-solid fa-user fa-sm"></i>               STUDENT</a>
            <a href="major.php" class="active"><i class="fa-solid fa-book fa-sm"></i>               MAJOR</a>
            <a href="department.php"><i class="fa-solid fa-building-columns fa-sm"></i>               DEPARTMENT</a>
            <a href="course.php"><i class="fa-solid fa-book-open-reader fa-sm"></i>               COURSE</a>
        </div>
        <button onclick="location.href='reports.php'" class="tabs"><i class="fa-regular fa-file-lines"></i>            Reports</button>
    </nav>

    <div class="contentPanel">
        <h1><i class="fa-solid fa-book"></i>                         Major Display and Form</h1>

        <div class="form-header">
            <h3><i class="fa-solid fa-file-circle-plus" style="color: #F0F0EA;"></i>                ADD NEW MAJOR</h3>
        </div>
        <div class="form-container">
            <form action="" method="post" autocomplete="off">
                <label for="MajorID">Major ID :</label>
                <input type="text" id="MajorID" placeholder="Enter major ID ..." name="MajorID" >
                <label for="MajorName">Major Name :</label>
                <input type="text" id="MajorName" placeholder="Enter major name ..." name="MajorName" required>
                <label for="DepartmentID">Department ID :</label>
                <select id="DepartmentID" name="DepartmentID" required>
                    <option value="" disabled selected>Select Department ID ...</option>
                    <?php echo $deptOptions; ?>
                </select>
                <button type="submit" class="submitBTN" name="submit">SUBMIT      <i class="fa-solid fa-arrow-up-right-from-square fa-sm"></i></button>
            </form>
        </div>
    </div>
    
</body>
</html>
