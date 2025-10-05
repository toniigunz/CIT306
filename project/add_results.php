<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

$message = '';

// Get all students and courses
$students = $pdo->query("SELECT * FROM users WHERE role = 'student' ORDER BY username")->fetchAll();
$courses = $pdo->query("SELECT * FROM courses ORDER BY course_code")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $grade = $_POST['grade'];
    $semester = $_POST['semester'];
    
    // Check if result already exists
    $stmt = $pdo->prepare("SELECT * FROM results WHERE student_id = ? AND course_id = ? AND semester = ?");
    $stmt->execute([$student_id, $course_id, $semester]);
    
    if ($stmt->rowCount() > 0) {
        // Update existing result
        $stmt = $pdo->prepare("UPDATE results SET grade = ? WHERE student_id = ? AND course_id = ? AND semester = ?");
        $success = $stmt->execute([$grade, $student_id, $course_id, $semester]);
        $action = "updated";
    } else {
        // Insert new result
        $stmt = $pdo->prepare("INSERT INTO results (student_id, course_id, grade, semester) VALUES (?, ?, ?, ?)");
        $success = $stmt->execute([$student_id, $course_id, $grade, $semester]);
        $action = "added";
    }
    
    if ($success) {
        $message = "Result $action successfully!";
    } else {
        $message = "Error $action result. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Result - Student Information Board</title>
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
            max-width: 800px;
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
            list-style: none;
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
        
        .form-container {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
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
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
        }
        
        @media (max-width: 768px) {
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
            <h1>Add/Update Student Result</h1>
            <p>Add or update student results for courses</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="post" action="">
                <div class="form-group">
                    <label for="student_id">Select Student</label>
                    <select id="student_id" name="student_id" required>
                        <option value="">-- Select Student --</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['username']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="course_id">Select Course</label>
                    <select id="course_id" name="course_id" required>
                        <option value="">-- Select Course --</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['course_code']); ?> - <?php echo htmlspecialchars($course['course_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="grade">Grade</label>
                    <select id="grade" name="grade" required>
                        <option value="">-- Select Grade --</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        <option value="F">F</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester" required>
                        <option value="">-- Select Semester --</option>
                        <option value="2023/2024 First Semester">2023/2024 First Semester</option>
                        <option value="2023/2024 Second Semester">2023/2024 Second Semester</option>
                        <option value="2024/2025 First Semester">2024/2025 First Semester</option>
                        <option value="2024/2025 Second Semester">2024/2025 Second Semester</option>
                    </select>
                </div>
                
                <button type="submit" class="btn">Save Result</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2023 Department of Computer Science, Federal University of Technology, Owerri. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>