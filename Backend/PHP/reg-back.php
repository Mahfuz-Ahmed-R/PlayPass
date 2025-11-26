<?php 
    // Use a path relative to this file so it works regardless of current working directory
    include __DIR__ . "/connection.php";

    if (isset($_POST['submit'])){
        $name       = $_POST['name']       ?? '';
        $email      = $_POST['email']      ?? '';
        $phone      = $_POST['phone']      ?? '';
        $password   = $_POST['password']   ?? '';
        $repassword = $_POST['repassword'] ?? '';

        if ($password !== $repassword) {
            echo '<script>window.registrationError = "Passwords do not match.";</script>';
            return;
        }
    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<script>window.registrationError = "Invalid email format.";</script>';
        return;
    }

    // Phone validation (example: 11-15 digits)
    if (!preg_match('/^\d{1,15}$/', $phone)) {
        echo '<script>window.registrationError = "Phone number must be 11-15 digits.";</script>';
        return;
    }

    // Password strength validation (at least 8 chars, 1 letter, 1 number, special characters allowed)
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password)) {
        echo '<script>window.registrationError = "Password must be at least 8 characters, with at least one letter and one number.";</script>';
        return;
    }
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);


        $sql = "INSERT INTO `users` (name, email, password, phone, role_id)
                VALUES ('$name', '$email', '$hashedPassword', '$phone', 1)";

        $result = mysqli_query($conn, $sql);

        if ($result) {

            echo '<script>window.registrationSuccess = true;</script>';
        } else {

            die('Database error: ' . mysqli_error($conn));
        }
    }
?>