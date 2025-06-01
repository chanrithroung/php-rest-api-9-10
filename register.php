<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
</head>
<body>
    <div class="container">
        <div class="form-container register-form-container">
            <div class="form-header">
                <h1>Create Account</h1>
                <p>Fill in your details to get started</p>
            </div>
            
            <form class="register-form" method="post" enctype="multipart/form-data">
                <div class="profile-upload">
                    <div class="profile-preview" id="profilePreview">
                        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="user-icon">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="upload-controls">
                        <label for="profile-image" class="upload-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            Upload Photo
                        </label>
                        <input type="file" name="profile" id="profile-image" accept="image/*" onchange="previewImage(this)">
                        <p class="upload-hint">JPG, PNG or GIF (Max. 2MB)</p>
                    </div>
                </div>
                
                <div class="input-row">
                    <div class="input-group">
                        <label for="first-name">First Name</label>
                        <input type="text" id="first-name" name="first-name" placeholder="Enter your first name" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="last-name">Last Name</label>
                        <input type="text" id="last-name" name="last-name" placeholder="Enter your last name" required>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                
                <div class="input-group">
                    <label for="reg-password">Password</label>
                    <input type="password" id="reg-password" name="password" placeholder="Create a password" required>
                    <div class="password-toggle" onclick="togglePassword('reg-password')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
                    <div class="password-toggle" onclick="togglePassword('confirm-password')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </div>
                </div>
                
                <div class="terms-checkbox">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Account</button>
                
                <div class="form-footer">
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </form>
        </div>
        
        <div class="image-container register-image">
            <div class="image-overlay"></div>
            <div class="welcome-text">
                <h2>Welcome to our platform</h2>
                <p>Create an account and start your journey with us</p>
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

        function previewImage(input) {
            const preview = document.getElementById('profilePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Profile Preview">`;
                    preview.classList.add('has-image');
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>

<?php   
    require_once('db.php');

    if(isset($_COOKIE['user_id'])) {
        header("location: dashborad.php");
    }


    function uploadProfile($sourse_file) {
        $filename = rand(0, 9999999).date('Y-m-d-h-i-s').'.'.pathinfo($sourse_file['name'], PATHINFO_EXTENSION);
        move_uploaded_file($sourse_file['tmp_name'], './uploads/'.$filename);
        return $filename;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ( isset( $_FILES['profile']) and isset( $_POST['first-name']) and isset( $_POST['last-name']) and isset( $_POST['email']) and isset( $_POST['password']) ) {
            $profile = $_FILES['profile'];
            $filename = uploadProfile($profile);

            $first_name = $_POST['first-name'];
            $last_name = $_POST['last-name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $password = md5($password);

            $sql = "INSERT INTO `users`(`first_name`, `last_name`, `email`, `password`, `profile`) 
                    VALUES ('$first_name','$last_name','$email','$password','$filename');";

            $connection->query($sql);

            header("Location: login.php");
        }
    } else {
        ///...
    }
?>