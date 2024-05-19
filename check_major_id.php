<?php
require 'connection.php';

if (isset($_POST['majorID'])) {
    $majorID = $_POST['majorID'];
    $query = "SELECT * FROM major WHERE MajorID = '$majorID'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo 'exists';
    } else {
        echo 'not_exists';
    }
}
?>



<!-- <script>
        document.addEventListener('DOMContentLoaded', function() {

            function generateMajorID(majorName) {
                var words = majorName.trim().split(/\s+/);
                var wordsToRemove = ['of', 'in'];
                var initials = '';

                words.forEach(function(word) {
                    if (!wordsToRemove.includes(word.toLowerCase())) {
                        initials += word.charAt(0).toUpperCase();
                    }
                });

                return initials;
            }

            //Update MajorID field when MajorName changes
            function updateMajorID() {
                var majorName = document.getElementById('MajorName').value;
                var majorID = generateMajorID(majorName);
                document.getElementById('MajorID').value = majorID;
            }

            // Input event listener to MajorName field
            var majorNameInput = document.getElementById('MajorName');
            majorNameInput.addEventListener('input', updateMajorID);
        });
    </script> -->