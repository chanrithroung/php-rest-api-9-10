<?php 
    require_once('db.php');
    if( !isset($_COOKIE['user_id'])) {
        header("location: login.php");
    } else {
        $user_id = $_COOKIE['user_id'];
        $sql = "SELECT `id`, `first_name`, `last_name`, `profile` FROM `users` WHERE `id` = '$user_id';";

        $result = $connection->query($sql);
        $admin = mysqli_fetch_assoc($result);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | <?php echo $admin['first_name'] ?> </title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .dashboard {
            display: grid;
            grid-template-columns: 250px 1fr;
            grid-template-rows: 70px 1fr;
            grid-template-areas: 
                "sidebar header"
                "sidebar main";
            min-height: 100vh;
        }

        /* Header */
        .header {
            grid-area: header;
            background: white;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 100;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 1.5rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: background-color 0.3s;
        }

        .notification-btn:hover {
            background-color: #f8f9fa;
        }

        /* Sidebar */
        .sidebar {
            grid-area: sidebar;
            background: #2c3e50;
            color: white;
            padding: 2rem 0;
        }

        .admin-section {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid #34495e;
            margin-bottom: 2rem;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .admin-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3498db;
        }

        .admin-info h3 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
            color: #ecf0f1;
        }

        .admin-info p {
            font-size: 0.8rem;
            color: #bdc3c7;
        }

        .welcome-message {
            background: linear-gradient(135deg, #3498db, #2980b9);
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            margin-top: 1rem;
        }

        .welcome-message h4 {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .welcome-message p {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .nav-menu {
            list-style: none;
            padding: 0 1rem;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #bdc3c7;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: #34495e;
            color: #3498db;
        }

        .nav-icon {
            font-size: 1.1rem;
        }

        /* Main Content */
        .main-content {
            grid-area: main;
            padding: 2rem;
            overflow-y: auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-title {
            color: #7f8c8d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .stat-change {
            font-size: 0.8rem;
            color: #27ae60;
        }

        .chart-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .chart-header {
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.2rem;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .chart-subtitle {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .chart-placeholder {
            height: 300px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 1.1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
                grid-template-rows: 70px auto 1fr;
                grid-template-areas: 
                    "header"
                    "sidebar"
                    "main";
            }

            .sidebar {
                padding: 1rem 0;
            }

            .admin-section {
                padding: 0 1rem 1rem;
                margin-bottom: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Header -->
        <header class="header">
            <h1>Dashboard</h1>
            <div class="header-actions">
                <button class="notification-btn" onclick="showNotifications()">🔔</button>
                <span id="current-time"></span>
            </div>
        </header>

        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Admin Section -->
            <div class="admin-section">
                <div class="admin-profile">
                    <img src="uploads/<?php echo $admin['profile']; ?>" alt="Admin Avatar" class="admin-avatar" id="admin-avatar">
                    <div class="admin-info">
                        <h3 id="admin-name"><?php echo $admin['first_name'].' '.$admin['last_name'] ?> </h3>
                        <p id="admin-role">System Administrator</p>
                    </div>
                </div>
                <div class="welcome-message">
                    <h4>Welcome back!</h4>
                    <p id="welcome-text">Have a great day managing your dashboard</p>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link active" onclick="setActiveNav(this)">
                            <span class="nav-icon">📊</span>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="setActiveNav(this)">
                            <span class="nav-icon">👥</span>
                            <span>Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="setActiveNav(this)">
                            <span class="nav-icon">📈</span>
                            <span>Analytics</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="setActiveNav(this)">
                            <span class="nav-icon">⚙️</span>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="setActiveNav(this)">
                            <span class="nav-icon">📝</span>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="logout()">
                            <span class="nav-icon">🚪</span>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Total Users</span>
                        <div class="stat-icon" style="background: #3498db;">👥</div>
                    </div>
                    <div class="stat-value" id="total-users">1,234</div>
                    <div class="stat-change">+12% from last month</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Revenue</span>
                        <div class="stat-icon" style="background: #27ae60;">💰</div>
                    </div>
                    <div class="stat-value" id="revenue">$45,678</div>
                    <div class="stat-change">+8% from last month</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Orders</span>
                        <div class="stat-icon" style="background: #e74c3c;">📦</div>
                    </div>
                    <div class="stat-value" id="orders">567</div>
                    <div class="stat-change">+15% from last month</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Active Sessions</span>
                        <div class="stat-icon" style="background: #f39c12;">⚡</div>
                    </div>
                    <div class="stat-value" id="sessions">89</div>
                    <div class="stat-change">+3% from last hour</div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="chart-section">
                <div class="chart-header">
                    <h2 class="chart-title">Analytics Overview</h2>
                    <p class="chart-subtitle">Monthly performance metrics</p>
                </div>
                <div class="chart-placeholder">
                    📈 Chart visualization would go here
                </div>
            </div>
        </main>
    </div>

    <script>
        // Admin data
        
        // Initialize dashboard
        function initDashboard() {
            updateTime();
            animateStats();
            
            // Update time every second
            setInterval(updateTime, 1000);
            
            // Update stats every 30 seconds
            setInterval(updateStats, 30000);
        }

        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour12: false,
                hour: '2-digit',
                minute: '2-digit'
            });
            document.getElementById('current-time').textContent = timeString;
        }

        // Animate stats on load
        function animateStats() {
            const statValues = document.querySelectorAll('.stat-value');
            statValues.forEach(stat => {
                const finalValue = stat.textContent;
                const isNumber = !isNaN(parseInt(finalValue.replace(/[^0-9]/g, '')));
                
                if (isNumber) {
                    const number = parseInt(finalValue.replace(/[^0-9]/g, ''));
                    const prefix = finalValue.replace(/[0-9,]/g, '');
                    let current = 0;
                    const increment = number / 50;
                    
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= number) {
                            current = number;
                            clearInterval(timer);
                        }
                        stat.textContent = prefix + Math.floor(current).toLocaleString();
                    }, 30);
                }
            });
        }

        // Update stats with random values
        function updateStats() {
            const stats = {
                'total-users': Math.floor(Math.random() * 100) + 1200,
                'revenue': '$' + (Math.floor(Math.random() * 10000) + 40000).toLocaleString(),
                'orders': Math.floor(Math.random() * 100) + 500,
                'sessions': Math.floor(Math.random() * 50) + 70
            };

            Object.keys(stats).forEach(id => {
                document.getElementById(id).textContent = stats[id];
            });
        }

        // Navigation functions
        function setActiveNav(element) {
            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Add active class to clicked link
            element.classList.add('active');
            
            // Update main content based on selection
            const section = element.querySelector('span:last-child').textContent;
            console.log(`Navigating to: ${section}`);
        }

        // Utility functions
        function showNotifications() {
            alert('You have 3 new notifications:\n• New user registered\n• System backup completed\n• Monthly report ready');
        }

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        document.cookie = "user_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/myphp;";
        document.location.href = 'login.php';
    }
}

        // Initialize dashboard when page loads
        document.addEventListener('DOMContentLoaded', initDashboard);

        // Add some interactivity to stat cards
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('click', function() {
                    const title = this.querySelector('.stat-title').textContent;
                    alert(`Viewing detailed ${title} analytics...`);
                });
            });
        });
    </script>
</body>
</html>