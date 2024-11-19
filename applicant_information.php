<?php
session_start();

include 'dbconn.php';

function insertFormData($userId, $matricNo, $icNo, $gender, $religion, $semester, $program, $faculty, $campus, $currentCGPA, $nameOfAcademicAdvisor, $mailingAddress, $maritalStatus, $bankAccountNo, $bankName, $specialPrivileges, $presentTheOKUCard, $confirmationLetter) {
    global $conn;
    $sql = "INSERT INTO applicant_information (user_id, matric_no, ic_no, gender, religion, semester, program, faculty, campus, current_cgpa, name_of_academic_advisor, mailing_address, marital_status, bank_account_no, bank_name, special_privileges, present_the_oku_card, confirmation_letter) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'issssisssdssssssss', $userId, $matricNo, $icNo, $gender, $religion, $semester, $program, $faculty, $campus, $currentCGPA, $nameOfAcademicAdvisor, $mailingAddress, $maritalStatus, $bankAccountNo, $bankName, $specialPrivileges, $presentTheOKUCard, $confirmationLetter);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: zakatappstuuu.php?page=form-page-5&success=" . urlencode("Data inserted successfully!"));        exit();
    } else {
        header("Location: zakatappstuuu.php?page=form-page-5&error=" . urlencode(mysqli_error($conn)));        exit();
    }
}

function updateFormData($userId, $matricNo, $icNo, $gender, $religion, $semester, $program, $faculty, $campus, $currentCGPA, $nameOfAcademicAdvisor, $mailingAddress, $maritalStatus, $bankAccountNo, $bankName, $specialPrivileges, $presentTheOKUCard, $confirmationLetter) {
    global $conn;
    $existingApplicantData = getApplicantData($userId);

    if (empty($presentTheOKUCard)) {
        $presentTheOKUCard = $existingApplicantData['present_the_oku_card'];
    }
    if (empty($confirmationLetter)) {
        $confirmationLetter = $existingApplicantData['confirmation_letter'];
    }

    $sql = "UPDATE applicant_information SET matric_no=?, ic_no=?, gender=?, religion=?, semester=?, program=?, faculty=?, campus=?, current_cgpa=?, name_of_academic_advisor=?, mailing_address=?, marital_status=?, bank_account_no=?, bank_name=?, special_privileges=?, present_the_oku_card=?, confirmation_letter=? WHERE user_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssisssdssssssssi', $matricNo, $icNo, $gender, $religion, $semester, $program, $faculty, $campus, $currentCGPA, $nameOfAcademicAdvisor, $mailingAddress, $maritalStatus, $bankAccountNo, $bankName, $specialPrivileges, $presentTheOKUCard, $confirmationLetter, $userId);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: zakatappstuuu.php?page=form-page-5&success=" . urlencode("Data updated successfully!"));        exit();
    } else {
        header("Location: zakatappstuuu.php?page=form-page-5&error=" . urlencode(mysqli_error($conn)));        exit();
    }
}

function updateRegisteredUserData($userId, $email, $fullName, $mobileNumber, $addressLine1, $addressLine2, $postcode, $state) {
    global $conn;
    $sql = "UPDATE registered_user SET email=?, full_name=?, mobile_number=?, address_line_1=?, address_line_2=?, postcode=?, state=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sssssssi', $email, $fullName, $mobileNumber, $addressLine1, $addressLine2, $postcode, $state, $userId);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: zakatappstuuu.php?page=form-page-5&success=" . urlencode("User data updated successfully!"));        exit();
    } else {
        header("Location: zakatappstuuu.php?page=form-page-5&error=" . urlencode(mysqli_error($conn)));        exit();
    }
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    $userId = $_SESSION['user_id'];
    
    $userData = getUserData($userId);
}

function getUserData($userId) {
    global $conn;
    $sql = "SELECT * FROM registered_user WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        return false;
    }
}

function getApplicantData($userId) {
    global $conn;
    $sql = "SELECT * FROM applicant_information WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        return false;
    }
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    $userId = $_SESSION['user_id'];
    // Fetch data from applicant_information table
    $applicantData = getApplicantData($userId);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle file uploads
    $presentTheOkuCardPath = '';
    $confirmationLetterPath = '';

    if (!empty($_FILES['presentTheOKUCard']['name'])) {
        $presentTheOkuCardPath = 'uploadspreoku/' . uniqid() . '-' . $_FILES['presentTheOKUCard']['name'];
        move_uploaded_file($_FILES['presentTheOKUCard']['tmp_name'], $presentTheOkuCardPath);
    }

    if (!empty($_FILES['confirmationLetter']['name'])) {
        $confirmationLetterPath = 'uploadsconlet/' . uniqid() . '-' . $_FILES['confirmationLetter']['name'];
        move_uploaded_file($_FILES['confirmationLetter']['tmp_name'], $confirmationLetterPath);
    }

    if (!empty($_FILES['updatePresentTheOKUCard']['name'])) {
        $presentTheOkuCardPath = 'uploadspreoku/' . uniqid() . '-' . $_FILES['updatePresentTheOKUCard']['name'];
        move_uploaded_file($_FILES['updatePresentTheOKUCard']['tmp_name'], $presentTheOkuCardPath);
    }

    if (!empty($_FILES['updateConfirmationLetter']['name'])) {
        $confirmationLetterPath = 'uploadsconlet/' . uniqid() . '-' . $_FILES['updateConfirmationLetter']['name'];
        move_uploaded_file($_FILES['updateConfirmationLetter']['tmp_name'], $confirmationLetterPath);
    }

    if ($applicantData) {
        $matricNo = $_POST['matricNo'];
        $icNo = $_POST['icNo'];
        $gender = $_POST['gender'];
        $religion = $_POST['religion'];
        $semester = $_POST['semester'];
        $program = $_POST['program'];
        $faculty = $_POST['faculty'];
        $campus = $_POST['campus'];
        $currentCGPA = $_POST['currentCGPA'];
        $nameOfAcademicAdvisor = $_POST['nameOfAcademicAdvisor'];
        $mailingAddress = $_POST['mailingAddress'];
        $maritalStatus = $_POST['maritalStatus'];
        $bankAccountNo = $_POST['bankAccountNo'];
        $bankName = $_POST['bankName'];
        $specialPrivileges = $_POST['specialPrivileges'];

        updateFormData($userId, $matricNo, $icNo, $gender, $religion, $semester, 
        $program, $faculty, $campus, $currentCGPA, $nameOfAcademicAdvisor, $mailingAddress, 
        $maritalStatus, $bankAccountNo, $bankName, $specialPrivileges, $presentTheOkuCardPath, 
        $confirmationLetterPath);
    } else {
        $matricNo = $_POST['matricNo'];
        $icNo = $_POST['icNo'];
        $gender = $_POST['gender'];
        $religion = $_POST['religion'];
        $semester = $_POST['semester'];
        $program = $_POST['program'];
        $faculty = $_POST['faculty'];
        $campus = $_POST['campus'];
        $currentCGPA = $_POST['currentCGPA'];
        $nameOfAcademicAdvisor = $_POST['nameOfAcademicAdvisor'];
        $mailingAddress = $_POST['mailingAddress'];
        $maritalStatus = $_POST['maritalStatus'];
        $bankAccountNo = $_POST['bankAccountNo'];
        $bankName = $_POST['bankName'];
        $specialPrivileges = $_POST['specialPrivileges'];

        insertFormData($userId, $matricNo, $icNo, $gender, $religion, $semester, $program, 
        $faculty, $campus, $currentCGPA, $nameOfAcademicAdvisor, $mailingAddress,
         $maritalStatus, $bankAccountNo, $bankName, $specialPrivileges, $presentTheOkuCardPath, 
         $confirmationLetterPath);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
        $userId = $_SESSION['user_id'];
        if (isset($_POST['email']) && isset($_POST['fullName']) && isset($_POST['mobileNumber']) && 
        isset($_POST['addressLine1']) && isset($_POST['addressLine2']) && isset($_POST['postcode']) && isset($_POST['state'])) {
            $email = $_POST['email'];
            $fullName = $_POST['fullName'];
            $mobileNumber = $_POST['mobileNumber'];
            $addressLine1 = $_POST['addressLine1'];
            $addressLine2 = $_POST['addressLine2'];
            $postcode = $_POST['postcode'];
            $state = $_POST['state'];

            updateRegisteredUserData($userId, $email, $fullName, $mobileNumber, $addressLine1, $addressLine2, $postcode, $state);
        }
    }
}
?>