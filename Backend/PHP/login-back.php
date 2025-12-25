<?php 
    include __DIR__ . "/connection.php";

    if(isset($_POST['submit'])){
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo '<script>window.loginError = "Invalid email format.";</script>';
            return;
        }
        
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);

        if($user && $user['role_id']){
            if(password_verify($password, $user['password'])){
                $userId = $user['user_id'];
                $role_id = intval($user['role_id']);

                if($role_id === 1){
                    // Admin login - redirect to admin dashboard
                    echo '<script>
                        window.loginSuccess = true;
                        localStorage.setItem("user_id", ' . $userId . ');
                        localStorage.setItem("user_role", "admin");
                        window.location.href = "../../../Admin/index.php";
                    </script>';
                } else {
                    // Regular user login
                    echo '<script>
                        window.loginSuccess = true;
                        localStorage.setItem("user_id", ' . $userId . ');
                        localStorage.setItem("user_role", "user");
                        // Restore cart from backend after login
                        window.restoreCartOnLogin = true;
                        window.loginUserId = ' . $userId . ';
                    </script>';
                }
            }
            else{
                echo '<script>window.loginError = "Invalid password.";</script>';
            }
        }
        else{
            echo '<script>window.loginError = "Invalid email or password.";</script>';
        }
    }
?>