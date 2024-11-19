<?php
session_start();

include_once "dbconn.php";

$accommodationType = $accommodationFee = $paymentRate = $vehicle = $vehicleType = $vehicleModel = '';

$accommodationType = isset($_POST['accommodationType']) ? $_POST['accommodationType'] : '';
$accommodationFee = isset($_POST['accommodationFee']) ? $_POST['accommodationFee'] : '';
$paymentRate = isset($_POST['paymentRate']) ? $_POST['paymentRate'] : '';
$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : '';
$vehicleType = ''; 
$vehicleModel = ''; 

if ($vehicle == 'yes') {
    $vehicleType = isset($_POST['vehicleType']) ? $_POST['vehicleType'] : '';
    $vehicleModel = isset($_POST['vehicleModel']) ? $_POST['vehicleModel'] : '';
}

function getAccommodationInfoData($userId, $conn) {
    $accommodationInfoData = array();

    $sql = "SELECT ai.* FROM accommodation_information ai
            INNER JOIN applicant_information apinfo ON ai.application_id = apinfo.id
            WHERE apinfo.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $accommodationInfoData = $result->fetch_assoc();
    }

    $stmt->close();

    return $accommodationInfoData;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        $accommodationType = $_POST['accommodationType'];
        $accommodationFee = $_POST['accommodationFee'];
        $paymentRate = $_POST['paymentRate'];
        $vehicle = $_POST['vehicle'];
        $vehicleType = ''; 
        $vehicleModel = ''; 

        if ($vehicle == 'yes') {
            $vehicleType = isset($_POST['vehicleType']) ? $_POST['vehicleType'] : '';
            $vehicleModel = isset($_POST['vehicleModel']) ? $_POST['vehicleModel'] : '';
        }

        $sql_check_record = "SELECT ai.* FROM accommodation_information ai
                             INNER JOIN applicant_information apinfo ON ai.application_id = apinfo.id
                             WHERE apinfo.user_id = ?";
        $stmt_check_record = $conn->prepare($sql_check_record);
        $stmt_check_record->bind_param("i", $userId);
        $stmt_check_record->execute();
        $result_check_record = $stmt_check_record->get_result();

        if ($result_check_record->num_rows > 0) {
            $sql_update = "UPDATE accommodation_information ai
                           INNER JOIN applicant_information apinfo ON ai.application_id = apinfo.id
                           SET ai.accommodation_type = ?, ai.accommodation_fee = ?, ai.payment_rate = ?, 
                               ai.vehicle = ?, ai.vehicle_type = ?, ai.vehicle_model = ?
                           WHERE apinfo.user_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ssssssi", $accommodationType, $accommodationFee, $paymentRate, $vehicle, $vehicleType, $vehicleModel, $userId);

            if ($stmt_update->execute()) {
                echo "Record updated successfully";
                header("Location: zakatappstuuu.php?page=form-page-10&success=" . urlencode("Record updated successfully."));
                exit();
            } else {
                header("Location: zakatappstuuu.php?page=form-page-10&error=" . urlencode("Error updating record: " . $conn->error));
                exit();
            }

            $stmt_update->close();
        } else {
            $sql_insert = "INSERT INTO accommodation_information (application_id, accommodation_type, accommodation_fee, payment_rate, vehicle, vehicle_type, vehicle_model)
                           SELECT apinfo.id, ?, ?, ?, ?, ?, ?
                           FROM applicant_information apinfo
                           WHERE apinfo.user_id = ?";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssssssi", $accommodationType, $accommodationFee, $paymentRate, $vehicle, $vehicleType, $vehicleModel, $userId);

            if ($stmt_insert->execute()) {
                header("Location: zakatappstuuu.php?page=form-page-10&success=" . urlencode("Record added successfully."));
                exit();            } else {
                    header("Location: zakatappstuuu.php?page=form-page-10&error=" . urlencode("Error inserting code: " . $conn->error));
                    exit();
            }

            $stmt_insert->close();
        }

        $stmt_check_record->close();
    } else {
        header("Location: login.php");
        exit();
    }
} else {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        $accommodationInfoData = getAccommodationInfoData($userId, $conn);

        if (!empty($accommodationInfoData)) {
            $accommodationType = isset($accommodationInfoData['accommodation_type']) ? $accommodationInfoData['accommodation_type'] : '';
            $accommodationFee = isset($accommodationInfoData['accommodation_fee']) ? $accommodationInfoData['accommodation_fee'] : '';
            $paymentRate = isset($accommodationInfoData['payment_rate']) ? $accommodationInfoData['payment_rate'] : '';
            $vehicle = isset($accommodationInfoData['vehicle']) ? $accommodationInfoData['vehicle'] : '';
            $vehicleType = isset($accommodationInfoData['vehicle_type']) ? $accommodationInfoData['vehicle_type'] : '';
            $vehicleModel = isset($accommodationInfoData['vehicle_model']) ? $accommodationInfoData['vehicle_model'] : '';
        }
    } else {
        header("Location: login.php");
        exit();
    }
}
?>