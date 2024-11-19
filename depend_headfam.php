<?php
session_start();
include 'dbconn.php';

function getDependHeadFamData($applicantId, $conn) {
    $dependHeadFamData = array();

    $sql = "SELECT * FROM depend_head_fam WHERE applicant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $applicantId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dependHeadFamData[] = $row;
        }
    }

    $stmt->close();

    return $dependHeadFamData;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        $sql_select_applicant_id = "SELECT id FROM applicant_information WHERE user_id = ?";
        $stmt_select_applicant_id = $conn->prepare($sql_select_applicant_id);
        $stmt_select_applicant_id->bind_param("i", $userId);
        $stmt_select_applicant_id->execute();
        $result_select_applicant_id = $stmt_select_applicant_id->get_result();

        if ($row_applicant_id = $result_select_applicant_id->fetch_assoc()) {
            $applicantId = $row_applicant_id['id'];

            $cityOfResidence = $_POST['cityOfResidence'];
            $headOfFamily = $_POST['headOfFamily'];
            $workingStatus = $_POST['workingStatus'];
            $workingAdults = $_POST['workingAdults'];
            $unemployedAdults = $_POST['unemployedAdults'];
            $iptStudents = $_POST['iptStudents'];
            $schoolChildren = $_POST['schoolChildren'];
            $children5YearsAndUnder = $_POST['children5YearsAndUnder'];

            $sql_check_record = "SELECT * FROM depend_head_fam WHERE applicant_id = ?";
            $stmt_check_record = $conn->prepare($sql_check_record);
            $stmt_check_record->bind_param("i", $applicantId);
            $stmt_check_record->execute();
            $result_check_record = $stmt_check_record->get_result();

            if ($result_check_record->num_rows > 0) {
                $sql_update = "UPDATE depend_head_fam SET city_of_residence = ?, head_of_family = ?, working_status = ?, working_adults = ?, unemployed_adults = ?, ipt_students = ?, school_children = ?, children_5_years_and_under = ? WHERE applicant_id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ssssssssi", $cityOfResidence, $headOfFamily, $workingStatus, $workingAdults, $unemployedAdults, $iptStudents, $schoolChildren, $children5YearsAndUnder, $applicantId);

                if ($stmt_update->execute()) {
                    header("Location: zakatappstuuu.php?page=form-page-7&success=" . urlencode("Record updated successfully"));
                    exit();
                } else {
                    header("Location: zakatappstuuu.php?page=form-page-7&error=" . urlencode("Error updating record: " . $conn->error));
                    exit();
                }

                $stmt_update->close();
            } else {
                $sql_insert = "INSERT INTO depend_head_fam (city_of_residence, head_of_family, working_status, working_adults, unemployed_adults, ipt_students, school_children, children_5_years_and_under, applicant_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("ssssssssi", $cityOfResidence, $headOfFamily, $workingStatus, $workingAdults, $unemployedAdults, $iptStudents, $schoolChildren, $children5YearsAndUnder, $applicantId);

                if ($stmt_insert->execute()) {
                    
                    header("Location: zakatappstuuu.php?page=form-page-7&success=" . urlencode("Record inserted successfully"));
                    exit();
                } else {
                    
                    header("Location: zakatappstuuu.php?page=form-page-7&error=" . urlencode("Error inserting record: " . $conn->error));
                    exit();
                }

                
                $stmt_insert->close();
            }

            
            $stmt_check_record->close();

            
            $dependHeadFamData = getDependHeadFamData($applicantId, $conn);

        } 
        
        $stmt_select_applicant_id->close();
    } else {
       
        header("Location: login.php");
        exit();
    }
} else {
    
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        $sql_select_applicant_id = "SELECT id FROM applicant_information WHERE user_id = ?";
        $stmt_select_applicant_id = $conn->prepare($sql_select_applicant_id);
        $stmt_select_applicant_id->bind_param("i", $userId);
        $stmt_select_applicant_id->execute();
        $result_select_applicant_id = $stmt_select_applicant_id->get_result();

        if ($row_applicant_id = $result_select_applicant_id->fetch_assoc()) {
            $applicantId = $row_applicant_id['id'];

           
            $dependHeadFamData = getDependHeadFamData($applicantId, $conn);
        } 
        
        $stmt_select_applicant_id->close();
    } else {
        header("Location: login.php");
        exit();
    }
}
?>