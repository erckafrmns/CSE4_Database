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
                <form action="#">
                    <h1>SIGN IN</h1>
                    <span>STUDENT ACCOUNT</span>
                    <input type="text" placeholder="Username" />
                    <input type="password" placeholder="Password" />
                    <a href="#">Forgot your password?</a>
                    <button>Sign In</button>
                    <p>or <br>continue with <a href="">guest account</a></p>
                </form>
            </div>
            <div class="form-container admin-container">
                <form action="#">
                    <h1>SIGN IN</h1>
                    <span>ADMIN ACCOUNT</span>
                    <input type="text" placeholder="Username" />
                    <input type="password" placeholder="Password" />
                    <a href="#">Forgot your password?</a>
                    <button>Sign In</button>
                </form>
            </div>
            <div class="overlay-container">
                <div class="overlay">
                    <div class="overlay-panel overlay-left">
                        <h1>WELCOME</h1>
                        <span>ADMIN</span>
                        <p>To manage the system and access administrative tools, please press the button below and enter your credentials.</p>
                        <button class="ghost" id="signIn">ADMIN</button>
                    </div>
                    <div class="overlay-panel overlay-right">
                        <h1>WELCOME</h1>
                        <span>STUDENT</span>
                        <p>To view your courses and edit your information, please press the button below and enter your credentials.</p>
                        <button class="ghost" id="signUp">STUDENT</button>
                        <p>or <br>continue with <a href="">guest account</a></p>
                    </div>
                </div>
            </div>
        </div>

    <script>
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('container');

        signUpButton.addEventListener('click', () => {
            container.classList.add("right-panel-active");
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
        });

    </script>

</body>
</html>