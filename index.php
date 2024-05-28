<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sarang University</title>
    <link rel="stylesheet" href="css/index.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>
    
    <nav>
        <h1><span class="sarang">SARANG </span><span class="univ">UNIVERSITY</span></h1>
    </nav>

    <div class="contentPanel">

        <div class="container" id="container">
            <div class="form-container student-container">
                <form action="login.php" method="POST">
                    <h1>SIGN IN</h1>
                    <span class="acc">STUDENT ACCOUNT</span>
                    <input type="text" name="StudentID" placeholder="Student ID" />
                    <input type="password" name="Password" placeholder="Password" />
                    <?php if(isset($_GET['error']) && $_GET['error'] == 'student_login'): ?>
                        <p class="error-message">*Invalid Student ID or Password*</p>
                    <?php endif; ?>
                    <a href="#">Forgot your password?</a>
                    <button type="submit" name="student_login">Sign In</button>
                    <p class="contGuest">or <br>continue with <a href="guestAccount.php">guest account</a></p>
                </form>
            </div>
            <div class="form-container admin-container">
                <form action="login.php" method="POST">
                    <h1>SIGN IN</h1>
                    <span class="acc">ADMIN ACCOUNT</span>
                    <input type="text" name="AdminID" placeholder="Admin ID" />
                    <input type="password" name="Password" placeholder="Password" />
                    <?php if(isset($_GET['error']) && $_GET['error'] == 'admin_login'): ?>
                        <p class="error-message">*Invalid Admin ID or Password*</p>
                    <?php endif; ?>
                    <a href="#">Forgot your password?</a>
                    <button type="submit" name="admin_login">Sign In</button>
                </form>
            </div>
            <div class="overlay-container">
                <div class="overlay">
                    <div class="overlay-panel overlay-left">
                        <h1>WELCOME</h1>
                        <span>ADMIN</span>
                        <p>To manage the system and access administrative tools, please press the button below and enter your credentials.</p>
                        <button class="ghost" id="admin">ADMIN</button>
                    </div>
                    <div class="overlay-panel overlay-right">
                        <h1>WELCOME</h1>
                        <span>STUDENT</span>
                        <p>To view your courses and edit your information, please press the button below and enter your credentials.</p>
                        <button class="ghost" id="student">STUDENT</button>
                        <p class="overlayGuest">or <br>continue with <a href="guestAccount.php">guest account</a></p>
                    </div>
                </div>
            </div>
        </div>

    <script>
        const studentButton = document.getElementById('student');
        const adminButton = document.getElementById('admin');
        const container = document.getElementById('container');

        studentButton.addEventListener('click', () => {
            container.classList.add("right-panel-active");
        });

        adminButton.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
        });

        <?php if(isset($_GET['error']) && $_GET['error'] == 'student_login'): ?>
            container.classList.add("right-panel-active");
        <?php endif; ?>

    </script>

</body>
</html>