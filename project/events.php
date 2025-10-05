<?php
session_start();
include 'config.php';

// Get all events
$stmt = $pdo->query("SELECT e.*, u.username FROM events e JOIN users u ON e.posted_by = u.id ORDER BY e.event_date DESC");
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Student Information Board</title>
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
        
        .events-list {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .event-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .event-item:last-child {
            border-bottom: none;
        }
        
        .event-info {
            flex: 1;
        }
        
        .event-title {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .event-meta {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .event-date {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            text-align: center;
            min-width: 120px;
        }
        
        .event-day {
            font-size: 24px;
            font-weight: bold;
        }
        
        .event-month {
            font-size: 14px;
            text-transform: uppercase;
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
        
        .text-right {
            text-align: right;
            margin-top: 20px;
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
            
            .event-item {
                flex-direction: column;
                text-align: center;
            }
            
            .event-date {
                margin-top: 15px;
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>Departmental Events</h1>
            <p>Stay informed about upcoming events, seminars, and activities</p>
        </div>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <div class="text-right">
                <a href="add_event.php" class="btn">Add New Event</a>
            </div>
        <?php endif; ?>
        
        <div class="events-list">
            <?php if ($events): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-item">
                        <div class="event-info">
                            <h2 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h2>
                            <div class="event-meta">
                                Posted by <?php echo htmlspecialchars($event['username']); ?> on 
                                <?php echo date('F j, Y', strtotime($event['created_at'])); ?>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                        </div>
                        <div class="event-date">
                            <div class="event-day"><?php echo date('j', strtotime($event['event_date'])); ?></div>
                            <div class="event-month"><?php echo date('M', strtotime($event['event_date'])); ?></div>
                            <div class="event-year"><?php echo date('Y', strtotime($event['event_date'])); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="event-item">
                    <p>No events found.</p>
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