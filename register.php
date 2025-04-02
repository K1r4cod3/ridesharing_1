<?php
    require_once("config.php");

    function username_exists($conn, $username) {
        $query = "SELECT * FROM passengers WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        return mysqli_num_rows($result) > 0;
    }

    $usernameError = $passwordError = $confirmPasswordError = $firstNameError = $lastNameError = $emailError = $phoneError = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirmPassword']);
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        // Username validation
        if (empty($username)) {
            $usernameError = "Username is required.";
        } elseif (strlen($username) < 5) {
            $usernameError = "Username must be at least 5 characters.";
        } elseif (strlen($username) > 50) {
            $usernameError = "Username cannot exceed 50 characters.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $usernameError = "Username can only contain letters, numbers, and underscores.";
        } elseif (username_exists($conn, $username)) {
            $usernameError = "Username already exists.";
        }

        // Password validation
        if (empty($password)) {
            $passwordError = "Password is required.";
        } elseif (strlen($password) < 6) {
            $passwordError = "Password must be at least 6 characters.";
        } elseif (strlen($password) > 50) {
            $passwordError = "Password cannot exceed 50 characters.";
        }

        // Confirm password
        if (empty($confirmPassword)) {
            $confirmPasswordError = "Please confirm your password.";
        } elseif ($password !== $confirmPassword) {
            $confirmPasswordError = "Passwords do not match.";
        }

        // First name validation
        if (empty($firstName)) {
            $firstNameError = "First name is required.";
        } elseif (strlen($firstName) > 50) {
            $firstNameError = "First name cannot exceed 50 characters.";
        } elseif (!preg_match('/^[a-zA-Z \s]+$/', $firstName)) {
            $firstNameError = "First name can only contain letters.";
        }

        // Last name validation
        if (empty($lastName)) {
            $lastNameError = "Last name is required.";
        } elseif (strlen($lastName) > 50) {
            $lastNameError = "Last name cannot exceed 50 characters.";
        } elseif (!preg_match('/^[a-zA-Z \s]+$/', $lastName)) {
            $lastNameError = "Last name can only contain letters.";
        }

        // Email validation
        if (empty($email)) {
            $emailError = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailError = "Invalid email format.";
        } elseif (strlen($email) > 100) {
            $emailError = "Email cannot exceed 100 characters.";
        }

        // Phone validation
        if (empty($phone)) {
            $phoneError = "Phone number is required.";
        } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
            $phoneError = "Phone number must be exactly 10 digits.";
        }

        if (empty($usernameError) && empty($passwordError) && empty($confirmPasswordError) && 
            empty($firstNameError) && empty($lastNameError) && empty($emailError) && empty($phoneError)) {
            
            $query = "INSERT INTO passengers (username, password, first_name, last_name, email, phone_number) 
                     VALUES ('$username', '$password', '$firstName', '$lastName', '$email', '$phone')";
            
            if (mysqli_query($conn, $query)) {
                header("Location: login.php");
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
    <title>Register</title>
</head>
<body>
    <div class="border-b-2 border-gray-600 bg-[#ffddab]">
        <nav class="flex justify-between items-center max-w-[1240px] mx-auto py-4">
            <a href="index.php" class="text-[#FF9A9A] font-bold text-2xl">ORSP</a>
            <div>
                <a href="login.php" class="text-white bg-[#945034] rounded-lg px-4 py-2">Login</a>
                <a href="register.php" class="text-white bg-[#945034] rounded-lg px-4 py-2">Sign Up</a>
            </div>
        </nav>
    </div>

    <div class="mx-auto max-w-[1000px] p-4 bg-[#ffddab] mt-10 rounded-lg">
        <h1 class="text-black text-4xl font-bold text-center">Registration Page</h1>
        <form method="post" class="mx-auto flex flex-col gap-2 max-w-[400px] mt-10">
            <input type="text" name="username" id="username" class="rounded-lg px-4 py-2" placeholder="Username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            <span class="text-red-500 font-bold"><?php echo $usernameError ?></span>
            <input type="password" name="password" id="password" class="rounded-lg px-4 py-2" placeholder="Password">
            <span class="text-red-500 font-bold"><?php echo $passwordError ?></span>
            <input type="password" name="confirmPassword" id="confirmPassword" class="rounded-lg px-4 py-2" placeholder="Confirm Password">
            <span class="text-red-500 font-bold"><?php echo $confirmPasswordError ?></span>
            <input type="text" name="firstName" id="firstName" class="rounded-lg px-4 py-2" placeholder="First Name" value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>">
            <span class="text-red-500 font-bold"><?php echo $firstNameError ?></span>
            <input type="text" name="lastName" id="lastName" class="rounded-lg px-4 py-2" placeholder="Last Name" value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>">
            <span class="text-red-500 font-bold"><?php echo $lastNameError ?></span>
            <input type="email" name="email" id="email" class="rounded-lg px-4 py-2" placeholder="Email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <span class="text-red-500 font-bold"><?php echo $emailError ?></span>
            <input type="tel" name="phone" id="phone" class="rounded-lg px-4 py-2" placeholder="Phone Number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            <span class="text-red-500 font-bold"><?php echo $phoneError ?></span>
            <div class="flex gap-2 w-full">
                <button type="submit" class="bg-[#945034] rounded-lg px-4 py-4 w-full font-bold text-center text-white">Register</button>
                <a href="register.php" class="bg-[#945034] rounded-lg px-4 py-4 w-full font-bold text-center text-white">Clear</a>
            </div>
        </form>
        <p class="text-center mt-5">Already have an account? <a href="login.php" class="hover:underline hover:font-bold">Login here</a></p>
    </div>
</body>
</html> 