<?php
session_start();
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departmental Student Information Board</title>
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
        
        .hero {
            background-color: #3498db;
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .feature-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .feature-card p {
            margin-bottom: 15px;
        }
        
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
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
            
            .hero h1 {
                font-size: 2rem;
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
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h1>Welcome to the Student Information Portal</h1>
            <p>Your one-stop platform for announcements, events, results, and course registration</p>
        </div>
    </section>

    <div class="container">
        <section class="features">
            <div class="feature-card">
                <h3>Announcements</h3>
                <p>Stay updated with the latest departmental announcements and important notices.</p>
                <a href="announcements.php" class="btn">View Announcements</a>
            </div>
            
            <div class="feature-card">
                <h3>Events</h3>
                <p>Check out upcoming events, seminars, and activities in the department.</p>
                <a href="events.php" class="btn">View Events</a>
            </div>
            
            <div class="feature-card">
                <h3>Results</h3>
                <p>Access your academic results and performance records.</p>
                <a href="results.php" class="btn">Check Results</a>
            </div>
            
            <div class="feature-card">
                <h3>Course Registration</h3>
                <p>Register for your courses for the upcoming semester.</p>
                <a href="courses.php" class="btn">Register Courses</a>
            </div>
        </section>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2023 Department of Computer Science, Federal University of Technology, Owerri. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>