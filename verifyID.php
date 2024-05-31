<?php
include('connection.php');

$response = ['id' => ''];

if (isset($_POST['id']) && isset($_POST['type'])) {
    $id = $_POST['id'];
    $type = $_POST['type'];

    // Debug: Output the received ID and type
    error_log("Received ID: $id");
    error_log("Received Type: $type");

    if ($type === 'student') {
        $stmt = $conn->prepare("SELECT * FROM student WHERE StudentID = ?");
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE AdminID = ?");
    }

    // Debug: Check if the statement was prepared correctly
    if (!$stmt) {
        error_log("Statement preparation failed: " . $conn->error);
        echo json_encode($response);
        exit;
    }

    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Debug: Output the number of rows found
    error_log("Number of rows found: " . $result->num_rows);

    if ($result->num_rows > 0) {
        $response['id'] = $id;
    }

    $stmt->close();
}

echo json_encode($response);
?>
