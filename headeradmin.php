<style>
    header {
            background-color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Optional: add a shadow for visual separation */
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
    }

    nav ul li:last-child {
        margin-right: 0;
    }

    nav ul li a {
            text-decoration: none !important; 
            color: black;
            font-weight: bold;
        }

    nav ul li a:hover {
        color: #b6c784 !important;
        transition: color 0.3s ease;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
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

    .auth-buttons {
            position: relative;
        }

    .auth-buttons a {
        text-decoration: none;
        color: black;
        font-weight: bold;
        padding: 8px 16px;
        border: 2px solid black;
        border-radius: 5px;
        margin-right: 10px;
    }
    .auth-buttons a:hover {
            background-color: black;
            color: white;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

    @media (max-width: 991.98px) {
        body {
            padding-top: 60px;
        }
    }
</style>

<header>
    <div class="logo">
        <img src="images\zakateaselogo.png" alt="Logo">
    </div>
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li class="dropdown">
            <a href="#">User List</a>
                <div class="dropdown-menu">
                    <a href="userliststu.php">Student</a>
                    <a href="userlistint.php">Interviewer</a>
                </div>
                <li class="dropdown">
            <a href="#">Application</a>
                <div class="dropdown-menu">
                    <a href="listapplication.php">Student</a>
                </div>
            </li>
            <li><a href="update_dates.php">Open/Closed Application</a></li>
        </ul>
    </nav>
    <div class="auth-buttons">
        <a href="profile.php"><i class="fas fa-user"></i></a>
        <a href="logout.php">LogOut</a>
    </div>
</header>
