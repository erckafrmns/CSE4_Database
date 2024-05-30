<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sarang University - Guest</title>
    <link rel="stylesheet" href="css/guest.css">
    <link rel="stylesheet" href="css/guestNav.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>
    
    <nav>
        <h1><span class="sarang">SARANG </span><span class="univ">UNIVERSITY</span></h1>
        <ul>
            <li class="reportNav"><a href="guestReport/majorReport.php">REPORTS</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="guestReport/majorReport.php">Major</a></li>
                        <li><a href="guestReport/departmentReport.php">Department</a></li>
                        <li><a href="guestReport/courseReport.php">Course</a></li>
                        <li><a href="guestReport/majorCourseReport.php">Major-Course</a></li>
                    </ul>
                </div>
            </li>
            <li><button class="exitBTN" onclick="window.location.href='index.php';">EXIT</button></li>
        </ul>
    </nav>

    <div class="contentPanel">
        
        <div class="leftSide">
            <h4>WELCOME TO</h4>
            <h1>SARANG</h1>
            <h2>UNIVERSITY</h2>
            <p>'where all <span>dreams</span> come <span>true</span>'</p>
        </div>

        <div class="rightSide">
            <h1>List of Reports</h1>
            <ul>
                <li><a href="guestReport/majorReport.php"><i class="fa-solid fa-graduation-cap fa-sm"></i> Major</a></li>
                <li><a href="guestReport/departmentReport.php"><i class="fa-solid fa-building-columns fa-sm"></i> Department</a></li>
                <li><a href="guestReport/courseReport.php"><i class="fa-solid fa-book fa-sm"></i> Course</a></li>
                <li><a href="guestReport/majorCourseReport.php"><i class="fa-solid fa-book-open-reader fa-sm"></i> Major-Course</a></li>
            </ul>
        </div>
        
    </div>
    

</body>
</html>