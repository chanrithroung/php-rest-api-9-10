<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h1>Welcome Back</h1>
                <p>Enter your credentials to access your account</p>
            </div>
            
            <form class="login-form" method="post">

                <?php 
                 if(isset($_COOKIE['user_id'])) {
                        header("location: dashborad.php");
                    }
                    require_once('db.php');

                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        if (isset($_POST['email']) and $_POST['password']) {
                            $email = $_POST['email'];
                            $password = md5($_POST['password']);

                            $sql = "SELECT `id` FROM `users` WHERE `email` = '$email' and `password` = '$password';";
                            $result = $connection->query($sql);
                            $user = mysqli_fetch_assoc($result);
                            if (isset($user['id'])) {
                                setcookie('user_id', $user['id'], time() + ( 3600 * 24 * 7 ));
                                header("Location: dashborad.php");
                            }
                        }
                    }
                ?>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <div class="password-toggle" onclick="togglePassword('password')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </div>
                </div>
                
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
                
                <div class="social-login">
                    <p>Or login with</p>
                    <div class="social-buttons">
                        <button type="button" class="btn-social btn-google">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#EA4335">
                                <path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/>
                            </svg>
                            Google
                        </button>
                        <button type="button" class="btn-social btn-facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#1877F2">
                                <path d="M20.007,3H3.993C3.445,3,3,3.445,3,3.993v16.013C3,20.555,3.445,21,3.993,21h8.621v-6.971h-2.346v-2.717h2.346V9.31 c0-2.325,1.42-3.591,3.494-3.591c0.993,0,1.847,0.074,2.096,0.107v2.43l-1.438,0.001c-1.128,0-1.346,0.536-1.346,1.323v1.734h2.69 l-0.35,2.717h-2.34V21h4.587C20.555,21,21,20.555,21,20.007V3.993C21,3.445,20.555,3,20.007,3z"/>
                            </svg>
                            Facebook
                        </button>
                    </div>
                </div>
                
                <div class="form-footer">
                    <p>Don't have an account? <a href="register.php">Register</a></p>
                </div>
            </form>
        </div>
        
        <div class="image-container">
            <div class="image-overlay"></div>
            <div class="welcome-text">
                <h2>Join our community</h2>
                <p>Discover amazing features and connect with others</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }
    </script>
</body>
</html>


