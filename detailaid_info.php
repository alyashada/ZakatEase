<?php
session_start();
include_once "dbconn.php";

$zakatReceived = $zakatYear = $zakatSemester = $zakatAmount = $zakatPurpose = $zakatDocuments = "";

$maxFileSize = 30 * 1024 * 1024; // 30 MB

function getDetailsOfAidData($userId, $conn) {
    $detailsOfAidData = array();

    $sql = "SELECT da.* FROM details_of_aid da
            INNER JOIN applicant_information ai ON da.application_id = ai.id
            WHERE ai.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $detailsOfAidData = $result->fetch_assoc();
    }

    $stmt->close();

    return $detailsOfAidData;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        $zakatReceived = $_POST['zakatReceived'] ?? '';
        $zakatYear = !empty($_POST['zakatYear']) ? intval($_POST['zakatYear']) : 0;
        $zakatSemester = !empty($_POST['zakatSemester']) ? intval($_POST['zakatSemester']) : 0;
        $zakatAmount = $_POST['zakatAmount'] ?? '';
        $zakatPurpose = $_POST['zakatPurpose'] ?? '';

        $fileName = $_FILES["zakatDocuments"]["name"];
        $fileTmpName = $_FILES["zakatDocuments"]["tmp_name"];
        $fileError = $_FILES["zakatDocuments"]["error"];
        $fileSize = $_FILES["zakatDocuments"]["size"];

        if ($fileError === UPLOAD_ERR_OK) {
            if ($fileSize > $maxFileSize) {
                header("Location: zakatappstuuu.php?page=form-page-11&error=" . urlencode("File size exceeds limit."));
                exit();
            }

            $uploadDir = "uploadzakatdoc/";
            $destination = $uploadDir . basename($fileName);

            if (move_uploaded_file($fileTmpName, $destination)) {
                $sql_check_record = "SELECT da.* FROM details_of_aid da
                                     INNER JOIN applicant_information ai ON da.application_id = ai.id
                                     WHERE ai.user_id = ?";
                $stmt_check_record = $conn->prepare($sql_check_record);
                $stmt_check_record->bind_param("i", $userId);
                $stmt_check_record->execute();
                $result_check_record = $stmt_check_record->get_result();

                if ($result_check_record->num_rows > 0) {
                    $sql_update = "UPDATE details_of_aid da
                                   INNER JOIN applicant_information ai ON da.application_id = ai.id
                                   SET da.zakat_received = ?, da.zakat_year = ?, da.zakat_semester = ?, 
                                       da.zakat_amount = ?, da.zakat_purpose = ?, da.zakat_documents = ?
                                   WHERE ai.user_id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("ssssssi", $zakatReceived, $zakatYear, $zakatSemester, $zakatAmount, $zakatPurpose, $destination, $userId);
                } else {
                    $sql_insert = "INSERT INTO details_of_aid (application_id, zakat_received, zakat_year, zakat_semester, zakat_amount, zakat_purpose, zakat_documents)
                                   SELECT ai.id, ?, ?, ?, ?, ?, ?
                                   FROM applicant_information ai
                                   WHERE ai.user_id = ?";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param("ssssssi", $zakatReceived, $zakatYear, $zakatSemester, $zakatAmount, $zakatPurpose, $destination, $userId);
                }

                $stmt = isset($stmt_update) ? $stmt_update : $stmt_insert;
                if ($stmt->execute()) {
                    header("Location: zakatappstuuu.php?page=form-page-11&success=" . urlencode("Record updated/inserted successfully."));
                    exit();
                } else {
                    header("Location: zakatappstuuu.php?page=form-page-11&error=" . urlencode("Error updating/inserting record: " . $conn->error));
                    exit();
                }

                $stmt->close();
            } else {
                header("Location: zakatappstuuu.php?page=form-page-11&error=" . urlencode("Error uploading file."));
                exit();
            }
        } else {
            header("Location: zakatappstuuu.php?page=form-page-11&error=" . urlencode("File upload error."));
            exit();
        }

        $stmt_check_record->close();
    } else {
        header("Location: login.php");
        exit();
    }
} else {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        $detailsOfAidData = getDetailsOfAidData($userId, $conn);

        if (!empty($detailsOfAidData)) {
            $zakatReceived = $detailsOfAidData['zakat_received'];
            $zakatYear = $detailsOfAidData['zakat_year'];
            $zakatSemester = $detailsOfAidData['zakat_semester'];
            $zakatAmount = $detailsOfAidData['zakat_amount'];
            $zakatPurpose = $detailsOfAidData['zakat_purpose'];
            $zakatDocuments = $detailsOfAidData['zakat_documents'];
        }
    } else {
        header("Location: login.php");
        exit();
    }
}
?>
