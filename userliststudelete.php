<?php
// Include the database connection file
require_once 'dbconn.php';

// Check if the ID parameter is present in the URL
if (isset($_GET['id'])) {
    // Sanitize the ID input to prevent SQL injection
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Start a transaction
    mysqli_begin_transaction($conn);

    try {
        // Prepare the delete query for the dependent records
        $query1 = "DELETE FROM applicant_information WHERE user_id = '$id'";
        if (!mysqli_query($conn, $query1)) {
            throw new Exception('Error deleting from applicant_information: ' . mysqli_error($conn));
        }

        // Prepare the delete query for the main record
        $query2 = "DELETE FROM registered_user WHERE id = '$id'";
        if (!mysqli_query($conn, $query2)) {
            throw new Exception('Error deleting from registered_user: ' . mysqli_error($conn));
        }

        // Commit the transaction
        mysqli_commit($conn);

        // If deletion is successful, redirect back to the main page with success message
        $successMessage = "Information deleted successfully.";
        header("Location: userliststu.php?success=" . urlencode($successMessage));
        exit();
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        mysqli_rollback($conn);
        
        // Check if the error is related to foreign key constraint
        if (strpos($e->getMessage(), 'Cannot delete or update a parent row') !== false) {
            $errorMessage = 'Error: You need to delete related zakat application records first.';
        } else {
            $errorMessage = $e->getMessage();
        }
        
        // Redirect back to the main page with error message
        header('Location: userliststu.php?error=' . urlencode($errorMessage));
        exit();
    }
} else {
    // If the ID parameter is not present, redirect back to the main page with error message
    header('Location: userliststu.php?error=ID parameter not specified.');
    exit();
}

// Close the database connection
mysqli_close($conn);
?>
