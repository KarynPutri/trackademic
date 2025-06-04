<?php
session_start(); // Start the session

// Include database configuration
include('config/db_config.php');

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get user input
    $login_identifier = sanitize_input($_POST['nim']); // Changed name to login_identifier
    $password = sanitize_input($_POST['password']);

    // Validate input
    if (empty($login_identifier) || empty($password)) {
        $error_message = "Please fill in all fields.";
    } else {

        // Connect to the database
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query the database using prepared statement to prevent SQL injection
        // Check if the login identifier is an email or a NIM
        $sql = "SELECT id, nim, password, is_admin, is_dosen FROM users WHERE email = ? OR nim = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $login_identifier, $login_identifier);
        $stmt->execute();
        $result = $stmt->get_result();


        if ($result->num_rows > 0) {
            // User found
            $row = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $row['password'])) {
                // Password is correct
                $_SESSION['user_id'] = $row['id']; // Store user ID in session
                $_SESSION['nim'] = $row['nim'];

                if ($row['is_admin'] == 1) {
                    header("Location: admin.php");
                    exit();
                } elseif ($row['is_dosen'] == 1) {
                    header("Location: manage.php");
                    exit();
                } else {
                    header("Location: core.php");
                    exit();
                }
            } else {
                // Password is incorrect
                $error_message = "Incorrect password.";
            }
        } else {
            // User not found
            $error_message = "User not found.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #0a0a0a;
            color: #e0e0e0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Darker animated background gradient with blue tones */
        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255, 140, 0, 0.15) 0%, transparent 60%),
                radial-gradient(circle at 80% 20%, rgba(255, 165, 0, 0.12) 0%, transparent 60%),
                radial-gradient(circle at 40% 80%, rgba(255, 69, 0, 0.08) 0%, transparent 60%),
                radial-gradient(circle at 60% 30%, rgba(255, 140, 0, 0.1) 0%, transparent 70%);
            animation: gradientPulse 15s ease-in-out infinite;
        }

        /* Slower, more subtle gradient animation */
        @keyframes gradientPulse {
            0% {
                background-position: 0% 0%;
                opacity: 0.6;
            }
            25% {
                background-position: 20% 10%;
                opacity: 0.8;
            }
            50% {
                background-position: 10% 20%;
                opacity: 0.7;
            }
            75% {
                background-position: -10% 10%;
                opacity: 0.8;
            }
            100% {
                background-position: 0% 0%;
                opacity: 0.6;
            }
        }

        /* Darker floating particles */
        .particle {
            position: absolute;
            background: rgba(255, 165, 0, 0.05);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        .particle:nth-child(1) {
            width: 3px;
            height: 3px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 4px;
            height: 4px;
            top: 60%;
            left: 80%;
            animation-delay: 3s;
        }

        .particle:nth-child(3) {
            width: 2px;
            height: 2px;
            top: 80%;
            left: 20%;
            animation-delay: 6s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) translateX(0px);
                opacity: 0.4;
            }
            50% {
                transform: translateY(-15px) translateX(8px);
                opacity: 0.7;
            }
        }

        .login-container {
            background: rgba(20, 20, 20, 0.8);
            backdrop-filter: blur(2px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 48px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.7);
            position: relative;
            z-index: 1;
        }

        .login-title {
            font-size: 32px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #e0e0e0 0%, rgba(255, 255, 255, 0.6) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-subtitle {
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 32px;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #e0e0e0;
            font-size: 16px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: rgba(255, 140, 0, 0.6);
            background: rgba(255, 255, 255, 0.06);
            box-shadow: 0 0 0 3px rgba(255, 140, 0, 0.1);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .error-message {
            background: rgba(139, 0, 0, 0.2);
            border: 1px solid rgba(139, 0, 0, 0.4);
            color: #ff6b6b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            text-align: center;
        }

        .btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #ff8c00 0%, #ff6347 100%);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 16px;
            position: relative;
            overflow: visible;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 140, 0, 0.3);
            background: linear-gradient(135deg, #ff6347 0%, #ff4500 100%);
        }

        .btn:hover .stars {
            display: block;
            filter: drop-shadow(0 0 10px #fffdef);
        }

        .stars {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            width: 200%;
            height: 200%;
        }

        .stars::before,
        .stars::after {
            content: '✦';
            position: absolute;
            color: #fffdef;
            font-size: 18px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: all 0.6s ease-out;
        }

        .btn:hover .stars::before {
            opacity: 1;
            transform: translate(-150%, -150%);
            animation: sparkle 1.5s ease-in-out infinite;
        }

        .btn:hover .stars::after {
            opacity: 1;
            transform: translate(50%, -150%);
            animation: sparkle 1.5s ease-in-out infinite;
            animation-delay: 0.3s;
        }

        .btn::before,
        .btn::after {
            content: '✦';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fffdef;
            font-size: 16px;
            opacity: 0;
            transition: all 0.6s ease-out;
            pointer-events: none;
            filter: drop-shadow(0 0 8px #fffdef);
        }

        .btn:hover::before {
            opacity: 1;
            transform: translate(-50%, -250%);
            animation: sparkle 1.8s ease-in-out infinite;
        }

        .btn:hover::after {
            opacity: 1;
            transform: translate(-50%, 150%);
            animation: sparkle 2s ease-in-out infinite;
            animation-delay: 0.9s;
        }

        @keyframes sparkle {
            0%, 100% {
                opacity: 0.7;
                transform: scale(0.8);
            }
            50% {
                opacity: 1;
                transform: scale(1.2);
            }
        }

        .btn .star-1,
        .btn .star-2 {
            content: '✦';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fffdef;
            font-size: 14px;
            opacity: 0;
            transition: all 0.7s ease-out;
            pointer-events: none;
            filter: drop-shadow(0 0 6px #fffdef);
        }

        .btn:hover .star-1 {
            opacity: 1;
            transform: translate(-200%, 100%);
            animation: sparkle 1.6s ease-in-out infinite;
            animation-delay: 1.2s;
        }

        .btn:hover .star-2 {
            opacity: 1;
            transform: translate(100%, 100%);
            animation: sparkle 1.4s ease-in-out infinite;
            animation-delay: 1.5s;
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.05);
        }

        .divider {
            text-align: center;
            margin: 24px 0;
            color: rgba(255, 255, 255, 0.3);
            font-size: 14px;
        }

        /* Responsive design */
        @media (max-width: 480px) {
            .login-container {
                padding: 32px 24px;
                margin: 20px;
            }
            
            .login-title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="background-animation"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>

    <div class="login-container">
        <h1 class="login-title">Welcome</h1>
        <p class="login-subtitle">Sign in to your account</p>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="nim" class="form-label">NIM/Email</label>
                <input type="text" id="nim" name="nim" class="form-input" placeholder="Enter your NIM or email" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn">
                Sign In
                <span class="stars"></span>
                <span class="star-1">✦</span>
                <span class="star-2">✦</span>
            </button>
        </form>

        <div class="divider">Don't have an account?</div>
        
        <button onclick="window.location.href='register.php'" class="btn btn-secondary">Create Account</button>
    </div>
</body>
</html>
