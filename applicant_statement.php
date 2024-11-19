<?php
session_start();

include_once "dbconn.php";

$applicantStatement = "";

function getApplicantStatementData($userId, $conn) {
    $applicantStatementData = array();

    $sql = "SELECT ai.user_id, as_table.* 
            FROM applicant_statement as_table
            INNER JOIN applicant_information ai ON as_table.application_id = ai.id
            WHERE ai.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $applicantStatementData = $result->fetch_assoc();
    }

    $stmt->close();

    return $applicantStatementData;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        $applicantStatement = isset($_POST['applicantStatement']) ? 1 : 0;

        $sql_check_record = "SELECT as_table.* 
                             FROM applicant_statement as_table
                             INNER JOIN applicant_information ai ON as_table.application_id = ai.id
                             WHERE ai.user_id = ?";
        $stmt_check_record = $conn->prepare($sql_check_record);
        $stmt_check_record->bind_param("i", $userId);
        $stmt_check_record->execute();
        $result_check_record = $stmt_check_record->get_result();

        if ($result_check_record->num_rows > 0) {
            $sql_update = "UPDATE applicant_statement as_table
                           INNER JOIN applicant_information ai ON as_table.application_id = ai.id
                           SET as_table.statement = ?
                           WHERE ai.user_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ii", $applicantStatement, $userId);

            if ($stmt_update->execute()) {
                header("Location: zakatappstuuu.php?page=form-page-12&success=" . urlencode("Applicant statement updated successfully") . "&redirect=1");
                exit();
            } else {
                header("Location: zakatappstuuu.php?page=form-page-12&error=" . urlencode("Error updating applicant statement: " . $conn->error) . "&redirect=1");
                exit();
            }

            $stmt_update->close();
        } else {
            $sql_insert = "INSERT INTO applicant_statement (application_id, statement) 
                           SELECT ai.id, ? 
                           FROM applicant_information ai 
                           WHERE ai.user_id = ?";
            $stmt_insert = $conn->prepare($sql_insert);

            $stmt_insert->bind_param("ii", $applicantStatement, $userId);

            if ($stmt_insert->execute()) {
                header("Location: zakatappstuuu.php?page=form-page-12&success=" . urlencode("Applicant statement inserted successfully") . "&redirect=1");
                exit();
            } else {
                header("Location: zakatappstuuu.php?page=form-page-12&error=" . urlencode("Error inserting applicant statement: " . $conn->error) . "&redirect=1");
                exit();
            }

            $stmt_insert->close();
        }

        $stmt_check_record->close();

        $_SESSION['application_submitted'] = true;
    } else {
        header("Location: login.php");
        exit();
    }
} else {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        $applicantStatementData = getApplicantStatementData($userId, $conn);

        if (!empty($applicantStatementData)) {
            $applicantStatement = $applicantStatementData['statement'];
        }
    } else {
        header("Location: login.php");
        exit();
    }
}
?>


