<style>
    header {
        background-color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 1000; Ensure it stays above other content
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
<header>
    <div class="logo">
        <img src="images/zakateaselogo.png" alt="Logo">
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="calc.php">Calculator</a></li>
            <li><a href="index.php#faq">FAQs</a></li>
        </ul>
    </nav>
    <div class="auth-buttons">
        <a href="login.php">Login</a>
        <a href="signup.php">Sign Up</a>
    </div>
</header>