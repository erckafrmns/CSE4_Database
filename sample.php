<html>
  <head>
    <script src="https://code.jquery.com/jquery-1.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css"></script>
  </head>
  <body>
    <button class = "adduser">Add User</button>
    <script>
        $('.adduser').click(function(){
            (async () => {
                const { value: formValues } = await Swal.fire({
                    title: 'Update Record',
                    html:
                    '<input class="swal2-input" id="StudentID" value="SU24-123456" placeholder = "Student ID" readonly>' +
                    '<input class="swal2-input" id="FirstName" value="Ericka" placeholder = "First Name">' +
                    '<input class="swal2-input" id="LastName" value="Rasos" placeholder = "Last Name">' +
                    '<input class="swal2-input" id="MajorID" value="BSCS" placeholder = "Major ID">' +
                    '<input class="swal2-input" id="Email" value="ericka@email" placeholder = "Email">',
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
                }
              })
                if (formValues) {
                    var data = {
                      StudentID: $('#StudentID').val(),
                      FirstName: $('#FirstName').val(),
                      LastName: $('#LastName').val(),
                      MajorID: $('#MajorID').val(),
                      Email: $('#Email').val()
                    };

                    $.ajax({
                        url: 'update.php',
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
                            })
                        }
                    })
                }
            })()
        })
    </script>

    <?php
    $conn = mysqli_connect('localhost', 'root', '', 'data');

    if(isset($_POST['name'])){
      $name = $_POST['name'];
      $age = $_POST['age'];

      $query = "INSERT INTO tb_data VALUES('', '$name', '$age')";
      mysqli_query($conn, $query);
    }
    ?>
  </body>
</html>
