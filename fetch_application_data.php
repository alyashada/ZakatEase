<?php
session_start();
require_once 'dbconn.php';

if(isset($_POST['application_id'])) {
    $application_id = $_POST['application_id'];

    $query = "SELECT * FROM interview_details WHERE application_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $applicationData = $result->fetch_assoc();
        echo json_encode(array("success" => true, "data" => $applicationData));
    } else {
        echo json_encode(array("success" => false, "message" => "Interview details not found for this application."));
    }

    $stmt->close();
} else {
    echo json_encode(array("success" => false, "message" => "Application ID not provided."));
}

$conn->close();
?>