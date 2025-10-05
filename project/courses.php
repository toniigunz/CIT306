<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Get all available courses
$stmt = $pdo->query("SELECT * FROM courses ORDER BY course_code");
$courses = $stmt->fetchAll();

// Get student's registered courses
$stmt = $pdo->prepare("SELECT r.*, c.course_code, c.course_name FROM registrations r JOIN courses c ON r.course_id = c.id WHERE r.student_id = ? ORDER BY r.semester, c.course_code");
$stmt->execute([$user_id]);
$registrations = $stmt->fetchAll();

// Handle course registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_courses'])) {
    $selected_courses = $_POST['courses'] ?? [];
    $semester = $_POST['semester'];
    
    // Delete existing registrations for this semester
    $stmt = $pdo->prepare("DELETE FROM registrations WHERE student_id = ? AND semester = ?");
    $stmt->execute([$user_id, $semester]);
    
    // Register new courses
    if (!empty($selected_courses)) {
        foreach ($selected_courses as $course_id) {
            $stmt = $pdo->prepare("INSERT INTO registrations (student_id, course_id, semester) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $course_id, $semester]);
        }
    }
    
    $message = "Course registration for $semester semester completed successfully!";
    header("Location: courses.php?message=" . urlencode($message));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Registration - Student Information Board</title>
    <style>
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
        
        .page-header {
            margin: 30px 0;
            text-align: center;
        }
        
        .courses-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .registration-form, .registered-courses {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group select, .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .course-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 10px;
        }
        
        .course-item {
            padding: 10px;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .course-item:last-child {
            border-bottom: none;
        }
        
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .btn-full {
            width: 100%;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .registered-list {
            list-style: none;
        }
        
        .registered-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .registered-item:last-child {
            border-bottom: none;
        }
        
        .semester-header {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            margin: 20px 0 10px;
        }
        
        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
        }
        
        @media (max-width: 968px) {
            .courses-container {
                grid-template-columns: 1fr;
            }
            
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 15px;
                justify-content: center;
            }
            
            nav ul li {
                margin: 0 10px;
            }
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
        <div class="page-header">
            <h1>Course Registration</h1>
            <p>Register for your courses for the upcoming semester</p>
        </div>
        
        <?php if (isset($_GET['message'])): ?>
            <div class="message success">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="courses-container">
            <div class="registration-form">
                <h2>Register New Courses</h2>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="semester">Select Semester:</label>
                        <select id="semester" name="semester" required>
                            <option value="">-- Select Semester --</option>
                            <option value="2023/2024 First Semester">2023/2024 First Semester</option>
                            <option value="2023/2024 Second Semester">2023/2024 Second Semester</option>
                            <option value="2024/2025 First Semester">2024/2025 First Semester</option>
                            <option value="2024/2025 Second Semester">2024/2025 Second Semester</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Select Courses:</label>
                        <div class="course-list">
                            <?php foreach ($courses as $course): ?>
                                <div class="course-item">
                                    <input type="checkbox" name="courses[]" value="<?php echo $course['id']; ?>" id="course-<?php echo $course['id']; ?>">
                                    <label for="course-<?php echo $course['id']; ?>">
                                        <?php echo htmlspecialchars($course['course_code']); ?> - 
                                        <?php echo htmlspecialchars($course['course_name']); ?> 
                                        (<?php echo $course['credits']; ?> credits)
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button type="submit" name="register_courses" class="btn btn-full">Register Selected Courses</button>
                </form>
            </div>
            
            <div class="registered-courses">
                <h2>Your Registered Courses</h2>
                
                <?php if ($registrations): ?>
                    <?php
                    // Group registrations by semester
                    $grouped_registrations = [];
                    foreach ($registrations as $registration) {
                        $semester = $registration['semester'];
                        if (!isset($grouped_registrations[$semester])) {
                            $grouped_registrations[$semester] = [];
                        }
                        $grouped_registrations[$semester][] = $registration;
                    }
                    
                    foreach ($grouped_registrations as $semester => $semester_registrations):
                    ?>
                        <div class="semester-header"><?php echo htmlspecialchars($semester); ?></div>
                        <ul class="registered-list">
                            <?php foreach ($semester_registrations as $registration): ?>
                                <li class="registered-item">
                                    <strong><?php echo htmlspecialchars($registration['course_code']); ?></strong> - 
                                    <?php echo htmlspecialchars($registration['course_name']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>You haven't registered for any courses yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2023 Department of Computer Science, Federal University of Technology, Owerri. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>