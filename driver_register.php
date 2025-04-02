<?php
    require_once("config.php");

    function username_exists($conn, $username) {
        $query = "SELECT * FROM drivers WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        return mysqli_num_rows($result) > 0;
    }

    $usernameError = $passwordError = $confirmPasswordError = $firstNameError = $lastNameError = $emailError = $phoneError = $licenseError = $vehicleTypeError = $vehiclePlateError = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirmPassword']);
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $license = trim($_POST['license']);
        $vehicleType = trim($_POST['vehicleType']);
        $vehiclePlate = trim($_POST['vehiclePlate']);

        // Basic validation
        if (empty($username)) {
            $usernameError = "Username is required.";
        } elseif (strlen($username) < 5) {
            $usernameError = "Username must be at least 5 characters.";
        } elseif (username_exists($conn, $username)) {
            $usernameError = "Username already exists.";
        }

        if (empty($password)) {
            $passwordError = "Password is required.";
        } elseif (strlen($password) < 6) {
            $passwordError = "Password must be at least 6 characters.";
        }

        if (empty($confirmPassword)) {
            $confirmPasswordError = "Please confirm your password.";
        } elseif ($password !== $confirmPassword) {
            $confirmPasswordError = "Passwords do not match.";
        }

        if (empty($firstName)) {
            $firstNameError = "First name is required.";
        }

        if (empty($lastName)) {
            $lastNameError = "Last name is required.";
        }

        if (empty($email)) {
            $emailError = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailError = "Invalid email format.";
        }

        if (empty($phone)) {
            $phoneError = "Phone number is required.";
        } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
            $phoneError = "Phone number must be 10 digits.";
        }

        if (empty($license)) {
            $licenseError = "License number is required.";
        }

        if (empty($vehicleType)) {
            $vehicleTypeError = "Vehicle type is required.";
        }

        if (empty($vehiclePlate)) {
            $vehiclePlateError = "Vehicle plate number is required.";
        }

        if (empty($usernameError) && empty($passwordError) && empty($confirmPasswordError) && 
            empty($firstNameError) && empty($lastNameError) && empty($emailError) && empty($phoneError) &&
            empty($licenseError) && empty($vehicleTypeError) && empty($vehiclePlateError)) {
            
            $query = "INSERT INTO drivers (username, password, first_name, last_name, email, phone_number, license_number, vehicle_type, vehicle_plate) 
                     VALUES ('$username', '$password', '$firstName', '$lastName', '$email', '$phone', '$license', '$vehicleType', '$vehiclePlate')";
            
            if (mysqli_query($conn, $query)) {
                header("Location: driver_login.php?registered=1");
                exit();
            } else {
                $error = "Error in registration. Please try again.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>Driver Registration</title>
</head>
<body>
    <div class="border-b-2 border-gray-600 bg-[#ffddab]">
        <nav class="flex justify-between items-center max-w-[1240px] mx-auto py-4">
            <a href="index.php" class="text-[#FF9A9A] font-bold text-2xl">ORSP</a>
            <div>
                <a href="driver_login.php" class="text-white bg-[#5F8B4C] rounded-lg px-4 py-2">Driver Login</a>
                <a href="driver_register.php" class="text-white bg-[#5F8B4C] rounded-lg px-4 py-2">Driver Register</a>
            </div>
        </nav>
    </div>
    <div class="mx-auto max-w-[1000px] p-4 bg-[#ffddab] mt-10 rounded-lg">
        <h1 class="text-black text-4xl font-bold text-center">Driver Registration</h1>
        <form method="post" class="mx-auto flex flex-col gap-2 max-w-[600px] mt-10">
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <input type="text" name="firstName" id="firstName" class="rounded-lg px-4 py-2" placeholder="First Name" value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>">
                    <span class="text-red-500 font-bold"><?php echo $firstNameError ?></span>
                </div>
                <div class="flex flex-col">
                    <input type="text" name="lastName" id="lastName" class="rounded-lg px-4 py-2" placeholder="Last Name" value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>">
                    <span class="text-red-500 font-bold"><?php echo $lastNameError ?></span>
                </div>
            </div>
            
            <input type="email" name="email" id="email" class="rounded-lg px-4 py-2" placeholder="Email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <span class="text-red-500 font-bold"><?php echo $emailError ?></span>
            
            <input type="tel" name="phone" id="phone" class="rounded-lg px-4 py-2" placeholder="Phone Number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            <span class="text-red-500 font-bold"><?php echo $phoneError ?></span>
            
            <input type="text" name="username" id="username" class="rounded-lg px-4 py-2" placeholder="Username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            <span class="text-red-500 font-bold"><?php echo $usernameError ?></span>
            
            <input type="password" name="password" id="password" class="rounded-lg px-4 py-2" placeholder="Password">
            <span class="text-red-500 font-bold"><?php echo $passwordError ?></span>
            
            <input type="password" name="confirmPassword" id="confirmPassword" class="rounded-lg px-4 py-2" placeholder="Confirm Password">
            <span class="text-red-500 font-bold"><?php echo $confirmPasswordError ?></span>
            
            <div class="border-t-2 border-gray-400 my-2 pt-2">
                <h2 class="text-xl font-bold">Driver Information</h2>
            </div>
            
            <input type="text" name="license" id="license" class="rounded-lg px-4 py-2" placeholder="License Number" value="<?php echo isset($_POST['license']) ? htmlspecialchars($_POST['license']) : ''; ?>">
            <span class="text-red-500 font-bold"><?php echo $licenseError ?></span>
            
            <select name="vehicleType" id="vehicleType" class="rounded-lg px-4 py-2">
                <option value="" disabled <?php echo !isset($_POST['vehicleType']) ? 'selected' : ''; ?>>Select Vehicle Type</option>
                <option value="car" <?php echo (isset($_POST['vehicleType']) && $_POST['vehicleType'] == 'car') ? 'selected' : ''; ?>>Car</option>
                <option value="motorcycle" <?php echo (isset($_POST['vehicleType']) && $_POST['vehicleType'] == 'motorcycle') ? 'selected' : ''; ?>>Motorcycle</option>
                <option value="suv" <?php echo (isset($_POST['vehicleType']) && $_POST['vehicleType'] == 'suv') ? 'selected' : ''; ?>>SUV</option>
                <option value="van" <?php echo (isset($_POST['vehicleType']) && $_POST['vehicleType'] == 'van') ? 'selected' : ''; ?>>Van</option>
            </select>
            <span class="text-red-500 font-bold"><?php echo $vehicleTypeError ?></span>
            
            <input type="text" name="vehiclePlate" id="vehiclePlate" class="rounded-lg px-4 py-2" placeholder="Vehicle Plate Number" value="<?php echo isset($_POST['vehiclePlate']) ? htmlspecialchars($_POST['vehiclePlate']) : ''; ?>">
            <span class="text-red-500 font-bold"><?php echo $vehiclePlateError ?></span>
            
            <span class="text-red-500 font-bold"><?php echo isset($error) ? $error : ""; ?></span>
            
            <div class="flex gap-2 w-full mt-4">
                <button type="submit" class="bg-[#5F8B4C] rounded-lg px-4 py-4 w-full font-bold text-center text-white">Register</button>
                <a href="driver_register.php" class="bg-[#5F8B4C] rounded-lg px-4 py-4 w-full font-bold text-center text-white">Clear</a>
            </div>
        </form>
        <p class="text-center mt-5">Already have an account? <a href="driver_login.php" class="hover:underline hover:font-bold">Login here</a></p>
    </div>
</body>
</html> 