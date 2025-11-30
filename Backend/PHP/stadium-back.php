<?php
    include __DIR__ . "/connection.php";
    
    // Initialize error array
    $errors = [];
    
    if(isset($_POST['submit'])){
        $name = isset($_POST['stadiumName']) ? trim($_POST['stadiumName']) : '';
        $location = isset($_POST['locationName']) ? trim($_POST['locationName']) : '';
        $capacity = isset($_POST['capacity']) ? trim($_POST['capacity']) : '';
        $contact_info = isset($_POST['contact']) ? trim($_POST['contact']) : '';

        // Validation checks
        if(empty($name)){
            $errors[] = "Error: Stadium name is required.";
        }
        if(empty($location)){
            $errors[] = "Error: Location is required.";
        }
        if(empty($capacity)){
            $errors[] = "Error: Capacity is required.";
        } elseif(!is_numeric($capacity) || $capacity <= 0){
            $errors[] = "Error: Capacity must be a positive number.";
        }
        if(empty($contact_info)){
            $errors[] = "Error: Contact information is required.";
        }

        // Check for connection errors
        if(!$con){
            $errors[] = "Error: Database connection failed.";
        }

        // If no validation errors, proceed with database insertion
        if(empty($errors)){
            $sql = "insert into `stadium` (name,location,capacity,contact_info)
            values('$name','$location','$capacity','$contact_info')";
            $result = mysqli_query($con, $sql);    
            
            if($result){
                echo "Data inserted successfully";
            } else{
                echo "Error: Unable to insert stadium data. " . mysqli_error($con);
            }
        } else {
            // Display all errors
            foreach($errors as $error){
                echo $error . "<br>";
            }
        }
    }
?>
