<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header Interviewer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
       
    header {
        background-color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 1000; 
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .logo img {
        height: 50px;
        max-width: 100%; 
    }

    nav ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        display: flex;
    }

    nav ul li {
        margin-right: 20px;
        box-sizing: border-box; 
    }

    nav ul li:last-child {
        margin-right: 0;
    }

    nav ul li a {
        text-decoration: none !important;
        color: black;
        font-weight: bold;
        transition: color 0.3s ease; 
    }

    nav ul li a:hover {
        color: #b6c784; 
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }

    .dropdown:hover .dropdown-menu {
        display: block;
    }

    .dropdown-menu a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        font-weight: bold;
    }

    .dropdown-menu a:hover {
        background-color: #f1f1f1;
    }

    .auth-buttons a {
        text-decoration: none;
        color: black;
        font-weight: bold;
        padding: 8px 16px;
        border: 2px solid black;
        border-radius: 5px;
        transition: background-color 0.3s ease, color 0.3s ease; 
    }

    .auth-buttons a:hover {
        background-color: black; 
        color: white; 
    }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="images\zakateaselogo.png" alt="Logo">
        </div>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="listappinterviewer.php">Assigned Application</a></li>
            </ul>
        </nav>
        <div class="auth-buttons dropdown">
           
            
            <a href="profile.php"><i class="fas fa-user"></i></a>
            <a href="logout.php">LogOut</a>
        </div>
    </header>
</body>
</html>
