<html>
  <head>
    <script src="https://code.jquery.com/jquery-1.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css"></script>
  </head>
  <body>
    <button class = "adduser">Add User</button>
    <script type="text/javascript">
      $('.adduser').click(function(){
        (async () => {
          const { value: formValues } = await Swal.fire({
            title: 'Add New User',
            html:
              '<input class="swal2-input" id="name" placeholder = "Name">' +
              '<input class="swal2-input" id="age" placeholder = "Age">',
            showCancelButton: true,
          })

          if (formValues) {
            var data = {
              name: $('#name').val(),
              age: $('#age').val()
            };

            $.ajax({
              url: 'index.php',
              type: 'post',
              data: data,
              success:function(){
                Swal.fire({
                  icon: 'success',
                  title: 'Inserted Successfully',
                  html:
                  'Name : ' + data['name'] + '<br>' +
                  'Age : ' + data['age']
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
