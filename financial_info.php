<?php
session_start();

include_once "dbconn.php";

$sponsorshipLoan = $reason = $source = $sponsorshipAmount = $sponsorshipBalance = $tuitionFee = $feeStatus = "";

function getFinancialInfoData($userId, $conn) {
    $financialInfoData = array();

    $sql = "SELECT fi.* FROM financial_information fi
            INNER JOIN applicant_information ai ON fi.application_id = ai.id
            WHERE ai.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $financialInfoData = $result->fetch_assoc();
    }

    $stmt->close();

    return $financialInfoData;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        $sponsorshipLoan = $_POST['sponsorshipLoan'];
        $reason = $_POST['reason'];
        $source = $_POST['source'];
        $sponsorshipAmount = $_POST['sponsorshipAmount'];
        $sponsorshipBalance = $_POST['sponsorshipBalance'];
        $tuitionFee = $_POST['tuitionFee'];
        $feeStatus = $_POST['feeStatus'];

        $sql_check_record = "SELECT fi.* FROM financial_information fi
                             INNER JOIN applicant_information ai ON fi.application_id = ai.id
                             WHERE ai.user_id = ?";
        $stmt_check_record = $conn->prepare($sql_check_record);
        $stmt_check_record->bind_param("i", $userId);
        $stmt_check_record->execute();
        $result_check_record = $stmt_check_record->get_result();

        if ($result_check_record->num_rows > 0) {
            $sql_update = "UPDATE financial_information fi
                           INNER JOIN applicant_information ai ON fi.application_id = ai.id
                           SET fi.sponsorship_loan = ?, fi.reason = ?, fi.source = ?, 
                               fi.sponsorship_amount = ?, fi.sponsorship_balance = ?, fi.tuition_fee = ?, 
                               fi.fee_status = ?
                           WHERE ai.user_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("sssssssi", $sponsorshipLoan, $reason, $source, $sponsorshipAmount, $sponsorshipBalance, $tuitionFee, $feeStatus, $userId);

            if ($stmt_update->execute()) {
                header("Location: zakatappstuuu.php?page=form-page-9&success=" . urlencode("Financial information updated successfully."));
                exit();
            } else {
                header("Location: zakatappstuuu.php?page=form-page-9&error=" . urlencode("Error updating record: " . $conn->error));
                exit();
            }

            $stmt_update->close();
        } else {
            $sql_insert = "INSERT INTO financial_information (application_id, sponsorship_loan, reason, source, sponsorship_amount, sponsorship_balance, tuition_fee, fee_status)
                           SELECT ai.id, ?, ?, ?, ?, ?, ?, ?
                           FROM applicant_information ai
                           WHERE ai.user_id = ?";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sssssssi", $sponsorshipLoan, $reason, $source, $sponsorshipAmount, $sponsorshipBalance, $tuitionFee, $feeStatus, $userId);

            if ($stmt_insert->execute()) {
                header("Location: zakatappstuuu.php?page=form-page-9&success=" . urlencode("Financial information added successfully."));
                exit();
            } else {
                echo "Error inserting record: " . $conn->error;
                header("Location: zakatappstuuu.php?page=form-page-9&error=" . urlencode("Error inserting record:: " . $conn->error));
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

        $financialInfoData = getFinancialInfoData($userId, $conn);

        if (!empty($financialInfoData)) {
            $sponsorshipLoan = $financialInfoData['sponsorship_loan'];
            $reason = $financialInfoData['reason'];
            $source = $financialInfoData['source'];
            $sponsorshipAmount = $financialInfoData['sponsorship_amount'];
            $sponsorshipBalance = $financialInfoData['sponsorship_balance'];
            $tuitionFee = $financialInfoData['tuition_fee'];
            $feeStatus = $financialInfoData['fee_status'];
        }
    } else {
        header("Location: login.php");
        exit();
    }
}
?>