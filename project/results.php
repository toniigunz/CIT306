<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get results for the logged-in student
$stmt = $pdo->prepare("SELECT r.*, c.course_code, c.course_name FROM results r JOIN courses c ON r.course_id = c.id WHERE r.student_id = ? ORDER BY r.semester DESC, c.course_code ASC");
$stmt->execute([$user_id]);
$results = $stmt->fetchAll();

// Group results by semester
$grouped_results = [];
foreach ($results as $result) {
    $semester = $result['semester'];
    if (!isset($grouped_results[$semester])) {
        $grouped_results[$semester] = [];
    }
    $grouped_results[$semester][] = $result;
}

// Calculate GPA for each semester
$gpa_data = [];
foreach ($grouped_results as $semester => $semester_results) {
    $total_grade_points = 0;
    $total_credits = 0;
    
    foreach ($semester_results as $result) {
        // Convert grade to grade points
        $grade = $result['grade'];
        $grade_points = 0;
        
        switch ($grade) {
            case 'A': $grade_points = 5.0; break;
            case 'B': $grade_points = 4.0; break;
            case 'C': $grade_points = 3.0; break;
            case 'D': $grade_points = 2.0; break;
            case 'E': $grade_points = 1.0; break;
            case 'F': $grade_points = 0.0; break;
        }
        
        // Assuming each course is 3 credits for this example
        $credits = 3;
        
        $total_grade_points += $grade_points * $credits;
        $total_credits += $credits;
    }
    
    $gpa = $total_credits > 0 ? $total_grade_points / $total_credits : 0;
    $gpa_data[$semester] = number_format($gpa, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - Student Information Board</title>
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
        
        .results-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .semester-results {
            margin-bottom: 30px;
        }
        
        .semester-header {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .results-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .results-table th, .results-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .results-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .grade-A { color: #27ae60; font-weight: bold; }
        .grade-B { color: #2ecc71; font-weight: bold; }
        .grade-C { color: #f39c12; font-weight: bold; }
        .grade-D { color: #e67e22; font-weight: bold; }
        .grade-E { color: #e74c3c; font-weight: bold; }
        .grade-F { color: #c0392b; font-weight: bold; }
        
        .gpa-badge {
            background-color: #2c3e50;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .no-results {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
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
            
            .results-table {
                display: block;
                overflow-x: auto;
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
            <h1>Academic Results</h1>
            <p>View your academic performance and grades</p>
        </div>
        
        <div class="results-container">
            <?php if ($results): ?>
                <?php foreach ($grouped_results as $semester => $semester_results): ?>
                    <div class="semester-results">
                        <div class="semester-header">
                            <h2><?php echo htmlspecialchars($semester); ?> Semester</h2>
                            <div class="gpa-badge">GPA: <?php echo $gpa_data[$semester]; ?></div>
                        </div>
                        
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($semester_results as $result): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($result['course_code']); ?></td>
                                        <td><?php echo htmlspecialchars($result['course_name']); ?></td>
                                        <td class="grade-<?php echo $result['grade']; ?>"><?php echo htmlspecialchars($result['grade']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <h3>No results found.</h3>
                    <p>Your results will appear here once they are published.</p>
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