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

$user_id = $_SESSION['user_id'];
$nim = $_SESSION['nim'];

// Initialize variables
$title = "";
$submitted_by = "";
$created_at = "";
$message = "";

// Get report ID from URL
if (isset($_GET['id'])) {
    $report_id = sanitize_input($_GET['id']);

    // Fetch report data from the database
    $sql = "SELECT reports.title, reports.message, users.nama, reports.created_at, reports.is_anonymous, reports.is_accepted, reports.is_process, reports.is_completed
            FROM reports
            INNER JOIN users ON reports.nim = users.nim
            WHERE reports.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['title'];
        $message = $row['message'];
        $submitted_by = $row['is_anonymous'] ? "Anonymous" : $row['nama'];
        $created_at = $row['created_at'];
        if ($row['is_completed'] == 1) {
            $status = "Completed";
        } elseif ($row['is_process'] == 1) {
            $status = "In Process";
        } elseif ($row['is_accepted'] == 1) {
            $status = "Accepted";
        } else {
            $status = "Pending";
        }
    } else {
        // Report not found
        $title = "Report not found";
    }
} else {
    // Report ID not provided
    $title = "Report ID not provided";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Report</title>
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
        .floating-star:nth-child(7) { top: 30%; left: 40%; animation-delay: 5s; font-size: 14px; }
        .floating-star:nth-child(8) { top: 50%; left: 60%; animation-delay: 7s; font-size: 22px; }
        .floating-star:nth-child(9) { top: 15%; left: 30%; animation-delay: 2.5s; font-size: 20px; }
        .floating-star:nth-child(10) { top: 85%; left: 85%; animation-delay: 4.5s; font-size: 18px; }
        .floating-star:nth-child(11) { top: 25%; left: 70%; animation-delay: 1.5s; font-size: 16px; }
        .floating-star:nth-child(12) { top: 75%; left: 25%; animation-delay: 3.5s; font-size: 24px; }

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

        /* Enhanced Header/Navbar */
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

        .header-nav {
            display: flex;
            gap: 15px;
            align-items: center;
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
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                rgba(255, 255, 255, 0), 
                rgba(255, 255, 255, 0.1), 
                rgba(255, 255, 255, 0));
            transition: left 0.5s ease;
        }

        .nav-btn:hover::before {
            left: 100%;
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

        .nav-icon {
            font-size: 16px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            background: rgba(30, 30, 30, 0.6);
            border-radius: 30px;
            margin-right: 10px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .profile-avatar {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #1E90FF 0%, #4169E1 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            color: #fff;
        }

        .profile-name {
            font-size: 14px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Main content */
        .main-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
            position: relative;
            z-index: 10;
        }

        .report-container {
            background: rgba(30, 30, 30, 0.8);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 40px;
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.6s ease-out;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .report-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #1E90FF, #4169E1, #6495ED);
            opacity: 1;
        }

        .report-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .report-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #fff;
            background: linear-gradient(135deg, #e0e0e0 0%, rgba(255, 255, 255, 0.8) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .report-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .meta-icon {
            font-size: 18px;
            color: #4169E1;
        }

        .meta-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .meta-value {
            font-size: 14px;
            font-weight: 500;
            color: #fff;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-accepted {
            background: rgba(30, 144, 255, 0.2);
            color: #1E90FF;
            border: 1px solid rgba(30, 144, 255, 0.3);
        }

        .status-process {
            background: rgba(255, 152, 0, 0.2);
            color: #ff9800;
            border: 1px solid rgba(255, 152, 0, 0.3);
        }

        .status-completed {
            background: rgba(76, 175, 80, 0.2);
            color: #4caf50;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .report-content {
            margin-top: 30px;
        }

        .content-label {
            font-size: 16px;
            font-weight: 600;
            color: #4169E1;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .report-message {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 25px;
            font-size: 16px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .error-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.6);
        }

        .error-icon {
            font-size: 48px;
            margin-bottom: 20px;
            color: #ff6b6b;
        }

        .error-text {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .error-subtext {
            font-size: 16px;
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
                gap: 20px;
            }

            .header-nav {
                flex-wrap: wrap;
                justify-content: center;
            }

            .main-content {
                padding: 20px 15px;
            }

            .report-container {
                padding: 25px;
            }

            .report-title {
                font-size: 24px;
            }

            .report-meta {
                grid-template-columns: 1fr;
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
        <div class="floating-star">✦</div>
        <div class="floating-star">✧</div>
        <div class="floating-star">✦</div>
        <div class="floating-star">✧</div>
        <div class="floating-star">✦</div>
        <div class="floating-star">✧</div>
    </div>

    <header class="header">
        <div class="header-content">
            <h1 class="header-title">Report Details</h1>
            <nav class="header-nav">
                <div class="user-profile">
                    <div class="profile-avatar">
                        <?php echo substr($_SESSION['nim'], 0, 1); ?>
                    </div>
                    <div class="profile-name">
                        <?php echo $_SESSION['nim']; ?>
                    </div>
                </div>
                <a href="core.php" class="nav-btn primary">
                    <span class="nav-icon">🏠</span>
                    Dashboard
                </a>
                <a href="about.php" class="nav-btn">
                    <span class="nav-icon">ℹ️</span>
                    About
                </a>
                <a href="logout.php" class="nav-btn">
                    <span class="nav-icon">🚪</span>
                    Logout
                </a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <?php if ($title === "Report not found" || $title === "Report ID not provided"): ?>
            <div class="report-container">
                <div class="error-state">
                    <div class="error-icon">❌</div>
                    <div class="error-text"><?php echo $title; ?></div>
                    <div class="error-subtext">
                        <?php if ($title === "Report not found"): ?>
                            The report you're looking for doesn't exist or has been removed.
                        <?php else: ?>
                            Please provide a valid report ID to view the report.
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="report-container">
                <div class="report-header">
                    <h1 class="report-title"><?php echo htmlspecialchars($title); ?></h1>
                    
                    <div class="report-meta">
                        <div class="meta-item">
                            <div class="meta-icon">👤</div>
                            <div>
                                <div class="meta-label">Submitted By</div>
                                <div class="meta-value"><?php echo htmlspecialchars($submitted_by); ?></div>
                            </div>
                        </div>
                        
                        <div class="meta-item">
                            <div class="meta-icon">📅</div>
                            <div>
                                <div class="meta-label">Date Submitted</div>
                                <div class="meta-value"><?php echo date('F j, Y \a\t g:i A', strtotime($created_at)); ?></div>
                            </div>
                        </div>
                        
                        <div class="meta-item">
                            <div class="meta-icon">📊</div>
                            <div>
                                <div class="meta-label">Status</div>
                                <div class="meta-value">
                                    <?php
                                    $statusClass = 'pending';
                                    if ($status === 'Completed') $statusClass = 'completed';
                                    elseif ($status === 'In Process') $statusClass = 'process';
                                    elseif ($status === 'Accepted') $statusClass = 'accepted';
                                    ?>
                                    <span class="status-badge status-<?php echo $statusClass; ?>">
                                        <?php if ($status === 'Completed'): ?>🟢<?php endif; ?>
                                        <?php if ($status === 'In Process'): ?>🟡<?php endif; ?>
                                        <?php if ($status === 'Accepted'): ?>🔵<?php endif; ?>
                                        <?php if ($status === 'Pending'): ?>⚪<?php endif; ?>
                                        <?php echo $status; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="report-content">
                    <div class="content-label">
                        <span>📝</span>
                        Report Content
                    </div>
                    <div class="report-message">
                        <?php echo nl2br(htmlspecialchars($message)); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
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
    </script>
</body>
</html>
