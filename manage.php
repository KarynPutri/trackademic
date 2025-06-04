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

// Connect to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper function to check if a user is a staff member
function is_staff($user_id, $conn) {
    $sql = "SELECT is_dosen FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['is_dosen'] == 1;
    }
    return false;
}

// Check if staff is logged in
if (!isset($_SESSION['user_id']) || !is_staff($_SESSION['user_id'], $conn)) {
    header("Location: index.php"); // Redirect to login if not staff
    exit();
}

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $report_id = sanitize_input($_POST['report_id']);
    $is_accepted = isset($_POST['is_accepted']) ? 1 : 0;
    $is_process = isset($_POST['is_process']) ? 1 : 0;
    $is_completed = isset($_POST['is_completed']) ? 1 : 0;

    // Get current status from the database
    $sql = "SELECT is_accepted, is_process, is_completed FROM reports WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_is_accepted = $row['is_accepted'];
        $current_is_process = $row['is_process'];
        $current_is_completed = $row['is_completed'];

        // Enforce status update logic
        if ($is_process == 1 && $current_is_accepted == 0) {
            $error_message = "Cannot set 'In Process' to true if 'Accepted' is false.";
        } elseif ($is_completed == 1 && $current_is_process == 0) {
            $error_message = "Cannot set 'Completed' to true if 'In Process' is false.";
        } elseif ($is_process == 0 && $current_is_completed == 1) {
            $error_message = "Cannot set 'In Process' to false if 'Completed' is true.";
        } elseif ($is_accepted == 0 && $current_is_process == 1) {
            $error_message = "Cannot set 'Accepted' to false if 'In Process' is true.";
        } else {
            // Update the status in the database
            $sql = "UPDATE reports SET is_accepted = ?, is_process = ?, is_completed = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiii", $is_accepted, $is_process, $is_completed, $report_id);

            if ($stmt->execute()) {
                $success_message = "Report status updated successfully.";
            } else {
                $error_message = "Failed to update report status.";
            }
        }
    } else {
        $error_message = "Report not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Panel</title>
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

        /* Main content */
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
            position: relative;
            z-index: 10;
        }

        .section {
            background: rgba(30, 30, 30, 0.8);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
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
            color: #4169E1;
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

        /* Table styles */
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.02);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            vertical-align: top;
        }

        th {
            background: rgba(30, 144, 255, 0.1);
            color: #4169E1;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 12px;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-bottom: 5px;
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

        /* Status form */
        .status-form {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 15px;
        }

        .checkbox-group {
            margin-bottom: 10px;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 8px;
            cursor: pointer;
        }

        .checkbox-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #4169E1;
        }

        /* Button styles */
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1E90FF 0%, #4169E1 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(30, 144, 255, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4169E1 0%, #6495ED 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 144, 255, 0.4);
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

            .section {
                padding: 20px;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px 8px;
            }

            .status-form {
                padding: 10px;
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
            <h1 class="header-title">Staff Panel</h1>
            <nav>
                <a href="about.php" class="nav-btn">ℹ️ About</a>
                <a href="logout.php" class="nav-btn">🚪 Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <?php if (isset($success_message)): ?>
            <div class="message success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="message error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <section class="section">
            <h2 class="section-title">
                <span class="section-icon">📋</span>
                Report Management
            </h2>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Submitted By</th>
                            <th>Date</th>
                            <th>Current Status</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all reports from the database, ordered by submission time (newest first)
                        $sql = "SELECT reports.id, reports.title, users.nama, reports.created_at, reports.is_accepted, reports.is_process, reports.is_completed
                                FROM reports
                                INNER JOIN users ON reports.nim = users.nim
                                ORDER BY reports.created_at DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $status = 'Pending';
                                $statusClass = 'status-pending';
                                
                                if ($row['is_completed'] == 1) {
                                    $status = 'Completed';
                                    $statusClass = 'status-completed';
                                } elseif ($row['is_process'] == 1) {
                                    $status = 'In Process';
                                    $statusClass = 'status-process';
                                } elseif ($row['is_accepted'] == 1) {
                                    $status = 'Accepted';
                                    $statusClass = 'status-accepted';
                                }

                                echo "<tr>";
                                echo "<td><strong>" . htmlspecialchars($row['title']) . "</strong></td>";
                                echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                echo "<td>" . date('M j, Y g:i A', strtotime($row['created_at'])) . "</td>";
                                echo "<td><span class='status-badge " . $statusClass . "'>" . $status . "</span></td>";
                                echo "<td>";
                                echo "<div class='status-form'>";
                                echo "<form method='post' action=''>";
                                echo "<input type='hidden' name='report_id' value='" . $row['id'] . "'>";
                                
                                echo "<div class='checkbox-group'>";
                                echo "<label><input type='checkbox' name='is_accepted' value='1'" . ($row['is_accepted'] == 1 ? " checked" : "") . "> ✅ Accepted</label>";
                                echo "</div>";
                                
                                echo "<div class='checkbox-group'>";
                                echo "<label><input type='checkbox' name='is_process' value='1'" . ($row['is_process'] == 1 ? " checked" : "") . "> 🔄 In Process</label>";
                                echo "</div>";
                                
                                echo "<div class='checkbox-group'>";
                                echo "<label><input type='checkbox' name='is_completed' value='1'" . ($row['is_completed'] == 1 ? " checked" : "") . "> ✅ Completed</label>";
                                echo "</div>";
                                
                                echo "<button type='submit' name='update_status' class='btn btn-primary'>Update Status</button>";
                                echo "</form>";
                                echo "</div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align: center; color: rgba(255,255,255,0.5);'>No reports found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
