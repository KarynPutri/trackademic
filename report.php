<?php
session_start();

// Include database configuration
include('config/db_config.php');

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit();
}

// Connect to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$nim = $_SESSION['nim']; // Get NIM from session

// Handle report submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = sanitize_input($_POST['title']);
    $message = sanitize_input($_POST['message']);

    // Validate input
    if (empty($title) || empty($message)) {
        $error_message = "Please fill in all fields.";
    } else {
        $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0; // Check if anonymous checkbox is checked
        
        // Insert data into the database
        $sql = "INSERT INTO reports (nim, title, message, is_anonymous) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nim, $title, $message, $is_anonymous);

        if ($stmt->execute()) {
            $success_message = "Report submitted successfully.";
            header("Location: core.php"); // Redirect to core after submission
            exit();
        } else {
            $error_message = "Failed to submit report.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Report</title>
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
            position: relative;
            overflow-x: hidden;
        }

        /* Dynamic background animation with blue tones */
        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: 
                radial-gradient(circle at 15% 40%, rgba(30, 144, 255, 0.12) 0%, transparent 50%),
                radial-gradient(circle at 85% 60%, rgba(70, 130, 180, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 50% 20%, rgba(100, 149, 237, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 30% 80%, rgba(65, 105, 225, 0.1) 0%, transparent 50%);
            animation: backgroundFlow 20s ease-in-out infinite;
        }

        @keyframes backgroundFlow {
            0%, 100% {
                background-position: 0% 0%, 100% 100%, 50% 0%, 0% 100%;
                opacity: 0.6;
            }
            33% {
                background-position: 30% 20%, 70% 80%, 80% 30%, 20% 70%;
                opacity: 0.8;
            }
            66% {
                background-position: 60% 40%, 40% 60%, 20% 60%, 80% 40%;
                opacity: 0.7;
            }
        }

        /* Enhanced floating stars */
        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .floating-star {
            position: absolute;
            color: rgba(135, 206, 250, 0.15);
            font-size: 18px;
            animation: floatUpDown 8s ease-in-out infinite;
        }

        .floating-star:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; font-size: 22px; }
        .floating-star:nth-child(2) { top: 20%; left: 80%; animation-delay: 2s; font-size: 16px; }
        .floating-star:nth-child(3) { top: 60%; left: 15%; animation-delay: 4s; font-size: 24px; }
        .floating-star:nth-child(4) { top: 80%; left: 70%; animation-delay: 6s; font-size: 20px; }
        .floating-star:nth-child(5) { top: 40%; left: 90%; animation-delay: 1s; font-size: 18px; }
        .floating-star:nth-child(6) { top: 70%; left: 5%; animation-delay: 3s; font-size: 26px; }

        @keyframes floatUpDown {
            0%, 100% {
                transform: translateY(0px) translateX(0px);
                opacity: 0.3;
            }
            50% {
                transform: translateY(-20px) translateX(10px);
                opacity: 0.7;
            }
        }

        /* Enhanced Header */
        .header {
            background: rgba(15, 15, 15, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-size: 28px;
            font-weight: 600;
            background: linear-gradient(135deg, #e0e0e0 0%, rgba(255, 255, 255, 0.6) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            padding-left: 15px;
        }

        .header-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 5px;
            height: 70%;
            background: linear-gradient(to bottom, #1E90FF, #4169E1);
            border-radius: 3px;
        }

        .nav-btn {
            padding: 10px 20px;
            background: rgba(30, 30, 30, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            color: #e0e0e0;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-left: 10px;
        }

        .nav-btn:hover {
            background: rgba(40, 40, 40, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border-color: rgba(30, 144, 255, 0.3);
        }

        .nav-btn.primary {
            background: linear-gradient(135deg, #1E90FF 0%, #4169E1 100%);
            border: none;
            box-shadow: 0 4px 10px rgba(30, 144, 255, 0.3);
        }

        .nav-btn.primary:hover {
            background: linear-gradient(135deg, #4169E1 0%, #6495ED 100%);
            box-shadow: 0 6px 15px rgba(30, 144, 255, 0.4);
        }

        /* Main content */
        .main-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
            position: relative;
            z-index: 10;
        }

        .form-container {
            background: rgba(30, 30, 30, 0.8);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: slideInUp 0.6s ease-out;
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #1E90FF, #4169E1, #6495ED);
            opacity: 1;
            border-radius: 16px 16px 0 0;
        }

        .form-title {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #fff;
            text-align: center;
            background: linear-gradient(135deg, #e0e0e0 0%, rgba(255, 255, 255, 0.8) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-subtitle {
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 30px;
            font-size: 16px;
        }

        /* Messages */
        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }

        .success-message {
            background: rgba(76, 175, 80, 0.2);
            border: 1px solid rgba(76, 175, 80, 0.4);
            color: #4caf50;
        }

        .error-message {
            background: rgba(139, 0, 0, 0.2);
            border: 1px solid rgba(139, 0, 0, 0.4);
            color: #ff6b6b;
        }

        /* Form styles */
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

        .form-input, .form-textarea {
            width: 100%;
            padding: 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #e0e0e0;
            font-size: 16px;
            transition: all 0.3s ease;
            outline: none;
            font-family: inherit;
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-input:focus, .form-textarea:focus {
            border-color: rgba(30, 144, 255, 0.6);
            background: rgba(255, 255, 255, 0.06);
            box-shadow: 0 0 0 3px rgba(30, 144, 255, 0.1);
        }

        .form-input::placeholder, .form-textarea::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #4169E1;
        }

        .checkbox-group label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            cursor: pointer;
        }

        /* Button styles */
        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            margin-bottom: 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1E90FF 0%, #4169E1 100%);
            color: #fff;
            box-shadow: 0 4px 10px rgba(30, 144, 255, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4169E1 0%, #6495ED 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(30, 144, 255, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e0e0e0;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.05);
        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .main-content {
                padding: 20px 15px;
            }

            .form-container {
                padding: 25px;
            }

            .form-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="background-animation"></div>
    
    <div class="floating-elements">
        <div class="floating-star">✦</div>
        <div class="floating-star">✧</div>
        <div class="floating-star">✦</div>
        <div class="floating-star">✧</div>
        <div class="floating-star">✦</div>
        <div class="floating-star">✧</div>
    </div>

    <header class="header">
        <div class="header-content">
            <h1 class="header-title">Submit Report</h1>
            <nav>
                <a href="core.php" class="nav-btn primary">🏠 Dashboard</a>
                <a href="about.php" class="nav-btn">ℹ️ About</a>
                <a href="logout.php" class="nav-btn">🚪 Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="form-container">
            <h2 class="form-title">Submit New Report</h2>
            <p class="form-subtitle">Share your concerns or feedback with us</p>

            <?php if (isset($error_message)): ?>
                <div class="message error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if (isset($success_message)): ?>
                <div class="message success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="title" class="form-label">Report Title</label>
                    <input type="text" id="title" name="title" class="form-input" placeholder="Enter a descriptive title for your report" required>
                </div>

                <div class="form-group">
                    <label for="message" class="form-label">Report Details</label>
                    <textarea id="message" name="message" class="form-textarea" placeholder="Describe your report in detail. Include any relevant information that would help us understand and address your concern." required></textarea>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="is_anonymous" name="is_anonymous" value="1">
                    <label for="is_anonymous">Submit this report anonymously</label>
                </div>

                <button type="submit" class="btn btn-primary">Submit Report</button>
                <a href="core.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </main>

    <script>
        // Add hover effects to navigation buttons
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.05)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Auto-resize textarea
        const textarea = document.getElementById('message');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.max(120, this.scrollHeight) + 'px';
        });
    </script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
