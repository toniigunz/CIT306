<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get announcements
$stmt = $pdo->query("SELECT a.*, u.username FROM announcements a JOIN users u ON a.posted_by = u.id ORDER BY a.created_at DESC LIMIT 5");
$announcements = $stmt->fetchAll();

// Get events
$stmt = $pdo->query("SELECT e.*, u.username FROM events e JOIN users u ON e.posted_by = u.id ORDER BY e.event_date DESC LIMIT 5");
$events = $stmt->fetchAll();

// Get results for student
if ($_SESSION['role'] == 'student') {
    $stmt = $pdo->prepare("SELECT r.*, c.course_code, c.course_name FROM results r JOIN courses c ON r.course_id = c.id WHERE r.student_id = ? ORDER BY r.created_at DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $results = $stmt->fetchAll();
}

// Get registered courses for student
if ($_SESSION['role'] == 'student') {
    $stmt = $pdo->prepare("SELECT r.*, c.course_code, c.course_name FROM registrations r JOIN courses c ON r.course_id = c.id WHERE r.student_id = ? ORDER BY r.registered_at DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $registrations = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Information Board</title>
    <style>
        /* Add the same styles as index.php plus dashboard styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 20px;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        nav ul li a:hover {
            background-color: #34495e;
        }
        
        .welcome-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .dashboard-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .dashboard-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .item-list {
            list-style: none;
        }
        
        .item-list li {
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .item-list li:last-child {
            border-bottom: none;
        }
        
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
            font-size: 14px;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .text-right {
            text-align: right;
            margin-top: 15px;
        }
        
        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
        }
        
        .admin-actions {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
        }
        
        .admin-actions h4 {
            margin-bottom: 10px;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo">Departmental Student Information Board</div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="announcements.php">Announcements</a></li>
                    <li><a href="events.php">Events</a></li>
                    <li><a href="results.php">Results</a></li>
                    <li><a href="courses.php">Course Registration</a></li>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="welcome-section">
            <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            <p>This is your personal dashboard where you can see the latest updates and manage your account.</p>
            
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <div class="admin-actions">
                    <h4>Admin Actions</h4>
                    <a href="add_announcement.php" class="btn">Add Announcement</a>
                    <a href="add_event.php" class="btn">Add Event</a>
                    <a href="add_result.php" class="btn">Add Result</a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Latest Announcements</h3>
                <ul class="item-list">
                    <?php if ($announcements): ?>
                        <?php foreach ($announcements as $announcement): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($announcement['title']); ?></strong>
                                <p><?php echo substr(htmlspecialchars($announcement['content']), 0, 100); ?>...</p>
                                <small>Posted by: <?php echo htmlspecialchars($announcement['username']); ?> on <?php echo date('M j, Y', strtotime($announcement['created_at'])); ?></small>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No announcements found.</li>
                    <?php endif; ?>
                </ul>
                <div class="text-right">
                    <a href="announcements.php" class="btn">View All</a>
                </div>
            </div>
            
            <div class="dashboard-card">
                <h3>Upcoming Events</h3>
                <ul class="item-list">
                    <?php if ($events): ?>
                        <?php foreach ($events as $event): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                                <p><?php echo substr(htmlspecialchars($event['description']), 0, 100); ?>...</p>
                                <small>Date: <?php echo date('M j, Y', strtotime($event['event_date'])); ?></small>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No events found.</li>
                    <?php endif; ?>
                </ul>
                <div class="text-right">
                    <a href="events.php" class="btn">View All</a>
                </div>
            </div>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <div class="admin-actions">
                  <h4>Admin Actions</h4>
                    <a href="add_announcement.php" class="btn">Add Announcement</a>
                    <a href="add_event.php" class="btn">Add Event</a>
                    <a href="add_result.php" class="btn">Add Result</a>
                    <a href="register_admin.php" class="btn">Create Admin Account</a>
                </div>
            <?php endif; ?>
            <?php if ($_SESSION['role'] == 'student'): ?>
                <div class="dashboard-card">
                    <h3>Recent Results</h3>
                    <ul class="item-list">
                        <?php if ($results): ?>
                            <?php foreach ($results as $result): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($result['course_code']); ?></strong>
                                    <p><?php echo htmlspecialchars($result['course_name']); ?></p>
                                    <small>Grade: <?php echo htmlspecialchars($result['grade']); ?> | Semester: <?php echo htmlspecialchars($result['semester']); ?></small>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No results found.</li>
                        <?php endif; ?>
                    </ul>
                    <div class="text-right">
                        <a href="results.php" class="btn">View All</a>
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <h3>Course Registrations</h3>
                    <ul class="item-list">
                        <?php if ($registrations): ?>
                            <?php foreach ($registrations as $registration): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($registration['course_code']); ?></strong>
                                    <p><?php echo htmlspecialchars($registration['course_name']); ?></p>
                                    <small>Semester: <?php echo htmlspecialchars($registration['semester']); ?></small>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No course registrations found.</li>
                        <?php endif; ?>
                    </ul>
                    <div class="text-right">
                        <a href="courses.php" class="btn">Register Courses</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2023 Department of Computer Science, Federal University of Technology, Owerri. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>