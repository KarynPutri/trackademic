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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
                radial-gradient(circle at 15% 40%, rgba(255, 140, 0, 0.12) 0%, transparent 50%),
                radial-gradient(circle at 85% 60%, rgba(255, 165, 0, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 50% 20%, rgba(255, 69, 0, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 30% 80%, rgba(255, 140, 0, 0.1) 0%, transparent 50%);
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
            color: rgba(255, 165, 0, 0.15);
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
            background: linear-gradient(to bottom, #ff8c00, #ff6347);
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
            border-color: rgba(255, 140, 0, 0.3);
        }

        .nav-btn.primary {
            background: linear-gradient(135deg, #ff8c00 0%, #ff6347 100%);
            border: none;
            box-shadow: 0 4px 10px rgba(255, 140, 0, 0.3);
        }

        .nav-btn.primary:hover {
            background: linear-gradient(135deg, #ff6347 0%, #ff4500 100%);
            box-shadow: 0 6px 15px rgba(255, 140, 0, 0.4);
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
            background: linear-gradient(135deg, #ff8c00 0%, #ff6347 100%);
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            position: relative;
            z-index: 10;
        }

        .section {
            margin-bottom: 50px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-icon {
            font-size: 20px;
            color: #ff6347;
        }

        /* Report cards */
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            opacity: 0;
            animation: cascadeIn 0.8s ease-out 0.5s forwards;
        }

        .report-card {
            background: rgba(30, 30, 30, 0.9);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.6s ease-out both;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }

        .report-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 140, 0, 0.02) 0%, rgba(255, 69, 0, 0.01) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .report-card:hover::after {
            opacity: 1;
        }

        .report-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
            border-color: rgba(255, 140, 0, 0.4);
        }

        .report-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #fff;
        }

        .report-title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .report-title a:hover {
            color: #4169E1;
        }

        .report-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
        }

        .report-author {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .report-date {
            font-size: 12px;
        }

        .status-progression {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .status-item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-accepted {
            background: rgba(255, 140, 0, 0.2);
            color: #ff8c00;
            border: 1px solid rgba(255, 140, 0, 0.3);
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

        .status-current {
            animation: statusGlow 2s ease-in-out infinite;
            box-shadow: 0 0 15px rgba(255, 140, 0, 0.4);
            transform: scale(1.05);
        }

        .status-completed.status-current {
            animation: statusGlowGreen 2s ease-in-out infinite;
            box-shadow: 0 0 15px rgba(76, 175, 80, 0.4);
        }

        .status-strikethrough {
            text-decoration: line-through;
            opacity: 0.5;
            filter: grayscale(0.7);
        }

        @keyframes statusGlow {
            0%, 100% {
                box-shadow: 0 0 15px rgba(255, 140, 0, 0.4);
            }
            50% {
                box-shadow: 0 0 25px rgba(255, 140, 0, 0.6);
            }
        }

        @keyframes statusGlowGreen {
            0%, 100% {
                box-shadow: 0 0 15px rgba(76, 175, 80, 0.4);
            }
            50% {
                box-shadow: 0 0 25px rgba(76, 175, 80, 0.6);
            }
        }

        .status-arrow {
            color: rgba(255, 255, 255, 0.3);
            font-size: 10px;
            margin: 0 4px;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.5);
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .empty-text {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .empty-subtext {
            font-size: 14px;
        }

        /* Animations */
        @keyframes cascadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

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

            .reports-grid {
                grid-template-columns: 1fr;
            }

            .main-content {
                padding: 20px 15px;
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
            <h1 class="header-title">Student Dashboard</h1>
            <nav class="header-nav">
                <div class="user-profile">
                    <div class="profile-avatar">
                        <?php echo substr($_SESSION['nim'], 0, 1); ?>
                    </div>
                    <div class="profile-name">
                        <?php echo $_SESSION['nim']; ?>
                    </div>
                </div>
                <a href="report.php" class="nav-btn primary">
                    <span class="nav-icon">📝</span>
                    Submit Report
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
        <section class="section">
            <h2 class="section-title">
                <span class="section-icon">🌟</span>
                All Reports
            </h2>
            <div class="reports-grid" id="allReportsGrid">
                <?php
                // Fetch all reports from the database, ordered by submission time (newest first)
                $sql = "SELECT reports.id, reports.title, users.nama, reports.created_at, reports.is_anonymous, reports.is_accepted, reports.is_process, reports.is_completed
                        FROM reports
                        INNER JOIN users ON reports.nim = users.nim
                        ORDER BY reports.created_at DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status = 'Pending';
                        if ($row['is_completed'] == 1) {
                            $status = 'Completed';
                        } elseif ($row['is_process'] == 1) {
                            $status = 'In Process';
                        } elseif ($row['is_accepted'] == 1) {
                            $status = 'Accepted';
                        }

                        echo '<div class="report-card">';
                        echo '<h3 class="report-title"><a href="view.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a></h3>';
                        echo '<div class="report-meta">';
                        echo '<div class="report-author">';
                        echo '<span>👤</span>';
                        echo '<span>' . ($row['is_anonymous'] ? "Anonymous" : htmlspecialchars($row['nama'])) . '</span>';
                        echo '</div>';
                        echo '<div class="report-date">📅 ' . date('M j, Y', strtotime($row['created_at'])) . '</div>';
                        echo '</div>';

                        // Generate status progression
                        echo '<div class="status-progression">';

                        if ($status === 'Pending') {
                            echo '<div class="status-item status-pending status-current">⚪ Pending</div>';
                        } else {
                            // Show progression for non-pending statuses
                            $acceptedClass = 'status-accepted';
                            $processClass = 'status-process';
                            $completedClass = 'status-completed';
                            
                            if ($status === 'Accepted') {
                                $acceptedClass .= ' status-current';
                            } elseif ($status === 'In Process') {
                                $acceptedClass .= ' status-strikethrough';
                                $processClass .= ' status-current';
                            } elseif ($status === 'Completed') {
                                $acceptedClass .= ' status-strikethrough';
                                $processClass .= ' status-strikethrough';
                                $completedClass .= ' status-current';
                            }
                            
                            echo '<div class="status-item ' . $acceptedClass . '">🟠 Accepted</div>';
                            echo '<span class="status-arrow">→</span>';
                            echo '<div class="status-item ' . $processClass . '">🟡 In Process</div>';
                            echo '<span class="status-arrow">→</span>';
                            echo '<div class="status-item ' . $completedClass . '">🟢 Completed</div>';
                        }

                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="empty-state">';
                    echo '<div class="empty-icon">📋</div>';
                    echo '<div class="empty-text">No reports found</div>';
                    echo '<div class="empty-subtext">Be the first to submit a report!</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">
                <span class="section-icon">📝</span>
                My Reports
            </h2>
            <div class="reports-grid" id="myReportsGrid">
                <?php
                // Fetch the logged-in user's reports from the database, ordered by submission time (newest first)
                $sql = "SELECT id, title, created_at, is_accepted, is_process, is_completed
                        FROM reports
                        WHERE nim = ?
                        ORDER BY created_at DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $nim);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status = 'Pending';
                        if ($row['is_completed'] == 1) {
                            $status = 'Completed';
                        } elseif ($row['is_process'] == 1) {
                            $status = 'In Process';
                        } elseif ($row['is_accepted'] == 1) {
                            $status = 'Accepted';
                        }

                        echo '<div class="report-card">';
                        echo '<h3 class="report-title"><a href="view.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a></h3>';
                        echo '<div class="report-meta">';
                        echo '<div class="report-author">';
                        echo '<span>📄</span>';
                        echo '<span>Your Report</span>';
                        echo '</div>';
                        echo '<div class="report-date">📅 ' . date('M j, Y', strtotime($row['created_at'])) . '</div>';
                        echo '</div>';

                        // Generate status progression
                        echo '<div class="status-progression">';

                        if ($status === 'Pending') {
                            echo '<div class="status-item status-pending status-current">⚪ Pending</div>';
                        } else {
                            // Show progression for non-pending statuses
                            $acceptedClass = 'status-accepted';
                            $processClass = 'status-process';
                            $completedClass = 'status-completed';
                            
                            if ($status === 'Accepted') {
                                $acceptedClass .= ' status-current';
                            } elseif ($status === 'In Process') {
                                $acceptedClass .= ' status-strikethrough';
                                $processClass .= ' status-current';
                            } elseif ($status === 'Completed') {
                                $acceptedClass .= ' status-strikethrough';
                                $processClass .= ' status-strikethrough';
                                $completedClass .= ' status-current';
                            }
                            
                            echo '<div class="status-item ' . $acceptedClass . '">🟠 Accepted</div>';
                            echo '<span class="status-arrow">→</span>';
                            echo '<div class="status-item ' . $processClass . '">🟡 In Process</div>';
                            echo '<span class="status-arrow">→</span>';
                            echo '<div class="status-item ' . $completedClass . '">🟢 Completed</div>';
                        }

                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="empty-state">';
                    echo '<div class="empty-icon">📝</div>';
                    echo '<div class="empty-text">No reports submitted yet</div>';
                    echo '<div class="empty-subtext">Click "Submit Report" to create your first report</div>';
                    echo '</div>';
                }

                $stmt->close();
                ?>
            </div>
        </section>
    </main>

    <script>
        // Add staggered animation to report cards
        document.addEventListener('DOMContentLoaded', function() {
            const reportCards = document.querySelectorAll('.report-card');
            reportCards.forEach((card, index) => {
                card.style.animationDelay = (index * 0.1) + 's';
            });
        });

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

<?php
// Close database connection
$conn->close();
?>
