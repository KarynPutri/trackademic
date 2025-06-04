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

// Helper function to check if a user is an admin
function is_admin($user_id, $conn) {
    $sql = "SELECT is_admin FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['is_admin'] == 1;
    }
    return false;
}

// Check if admin is logged in
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || !is_admin($_SESSION['user_id'], $conn)) {
    header("Location: index.php"); // Redirect to login if not admin
    exit();
}

// Function to delete user
function delete_user($user_id, $conn) {
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}

// Handle user deletion
if (isset($_GET['delete_id']) && $_GET['delete_id'] != $_SESSION['user_id']) {
    $delete_id = sanitize_input($_GET['delete_id']);
    if (delete_user($delete_id, $conn)) {
        $success_message = "User deleted successfully.";
    } else {
        $error_message = "Failed to delete user.";
    }
}

// Handle create staff form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = sanitize_input($_POST['nama']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $role = sanitize_input($_POST['role']); // "staff" or "admin"

    // Validate input
    if (empty($nama) || empty($email) || empty($password) || empty($role)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Determine is_dosen and is_admin values
        $is_dosen = ($role == "staff") ? 1 : 0;
        $is_admin = ($role == "admin") ? 1 : 0;

        // Insert data into the database
        $sql = "INSERT INTO users (nim, nama, email, password, is_dosen, is_admin) VALUES (NULL, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $nama, $email, $hashed_password, $is_dosen, $is_admin);

        if ($stmt->execute()) {
            $success_message = "Staff/Admin created successfully.";
        } else {
            $error_message = "Failed to create staff/admin.";
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
    <title>Admin Panel</title>
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
            max-width: 1200px;
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

        .role-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-admin {
            background: rgba(255, 69, 0, 0.2);
            color: #ff4500;
            border: 1px solid rgba(255, 69, 0, 0.3);
        }

        .role-staff {
            background: rgba(30, 144, 255, 0.2);
            color: #1E90FF;
            border: 1px solid rgba(30, 144, 255, 0.3);
        }

        .role-user {
            background: rgba(128, 128, 128, 0.2);
            color: #808080;
            border: 1px solid rgba(128, 128, 128, 0.3);
        }

        /* Form styles */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #e0e0e0;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus, .form-select:focus {
            border-color: rgba(30, 144, 255, 0.6);
            background: rgba(255, 255, 255, 0.06);
            box-shadow: 0 0 0 3px rgba(30, 144, 255, 0.1);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        /* Button styles */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
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
            box-shadow: 0 4px 10px rgba(30, 144, 255, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4169E1 0%, #6495ED 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(30, 144, 255, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: #fff;
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(220, 53, 69, 0.4);
        }

        .btn-small {
            padding: 8px 16px;
            font-size: 12px;
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

            .form-grid {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px 8px;
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
            <h1 class="header-title">Admin Panel</h1>
            <nav>
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
                <span class="section-icon">👥</span>
                User Management
            </h2>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NIM</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all users from the database
                        $sql = "SELECT id, nim, nama, email, is_dosen, is_admin FROM users";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $role = "";
                                $roleClass = "";
                                if ($row['is_admin'] == 1) {
                                    $role = "Admin";
                                    $roleClass = "role-admin";
                                } elseif ($row['is_dosen'] == 1) {
                                    $role = "Staff";
                                    $roleClass = "role-staff";
                                } else {
                                    $role = "User";
                                    $roleClass = "role-user";
                                }

                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . ($row['nim'] ? $row['nim'] : "N/A") . "</td>";
                                echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td><span class='role-badge " . $roleClass . "'>" . $role . "</span></td>";
                                echo "<td>";
                                if ($row['id'] != $_SESSION['user_id']) {
                                    echo "<a href='admin.php?delete_id=" . $row['id'] . "' class='btn btn-danger btn-small' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>";
                                } else {
                                    echo "<span style='color: rgba(255,255,255,0.5); font-size: 12px;'>Cannot delete yourself</span>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align: center; color: rgba(255,255,255,0.5);'>No users found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">
                <span class="section-icon">➕</span>
                Create Staff/Admin
            </h2>
            
            <form method="post" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nama" class="form-label">Name</label>
                        <input type="text" id="nama" name="nama" class="form-input" placeholder="Enter full name" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="Enter email address" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="Create password" required>
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Role</label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Create User</button>
            </form>
        </section>
    </main>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
