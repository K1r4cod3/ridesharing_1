<?php
    require_once("config.php");

    $usernameError = $emailError = $newPasswordError = $confirmPasswordError = "";
    $successMessage = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $newPassword = trim($_POST['newPassword']);
        $confirmPassword = trim($_POST['confirmPassword']);

        // Username validation
        if (empty($username)) {
            $usernameError = "Username is required.";
        }

        // Email validation
        if (empty($email)) {
            $emailError = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailError = "Invalid email format.";
        }

        // New password validation
        if (empty($newPassword)) {
            $newPasswordError = "New password is required.";
        } elseif (strlen($newPassword) < 6) {
            $newPasswordError = "Password must be at least 6 characters.";
        } elseif (strlen($newPassword) > 50) {
            $newPasswordError = "Password cannot exceed 50 characters.";
        }

        // Confirm password validation
        if (empty($confirmPassword)) {
            $confirmPasswordError = "Please confirm your new password.";
        } elseif ($newPassword !== $confirmPassword) {
            $confirmPasswordError = "Passwords do not match.";
        }

        if (empty($usernameError) && empty($emailError) && empty($newPasswordError) && empty($confirmPasswordError)) {
            // First check if username and email exist
            $checkQuery = "SELECT * FROM passengers WHERE username = '$username' AND email = '$email'";
            $checkResult = mysqli_query($conn, $checkQuery);

            if (mysqli_num_rows($checkResult) == 1) {
                // Update password
                $updateQuery = "UPDATE passengers SET password = '$newPassword' WHERE username = '$username' AND email = '$email'";
                if (mysqli_query($conn, $updateQuery)) {
                    $successMessage = "Password has been reset successfully. Please login with your new password.";
                } else {
                    $errorMessage = "Error resetting password. Please try again.";
                }
            } else {
                $errorMessage = "Username and email combination not found. Please check your credentials.";
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
    <title>Reset Password</title>
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
        <h1 class="text-black text-4xl font-bold text-center">Reset Password</h1>
        <form method="post" class="mx-auto flex flex-col gap-2 max-w-[400px] mt-10">
            <input type="text" name="username" id="username" class="rounded-lg px-4 py-2" placeholder="Username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            <span class="text-red-500 font-bold"><?php echo $usernameError ?></span>
            <input type="email" name="email" id="email" class="rounded-lg px-4 py-2" placeholder="Email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <span class="text-red-500 font-bold"><?php echo $emailError ?></span>
            <input type="password" name="newPassword" id="newPassword" class="rounded-lg px-4 py-2" placeholder="New Password">
            <span class="text-red-500 font-bold"><?php echo $newPasswordError ?></span>
            <input type="password" name="confirmPassword" id="confirmPassword" class="rounded-lg px-4 py-2" placeholder="Confirm New Password">
            <span class="text-red-500 font-bold"><?php echo $confirmPasswordError ?></span>
            <span class="text-red-500 font-bold"><?php echo isset($errorMessage) ? $errorMessage : ""; ?></span>
            <span class="text-green-500 font-bold text-center"><?php echo $successMessage; ?></span>
            <div class="flex gap-2 w-full">
                <button type="submit" class="bg-[#945034] rounded-lg px-4 py-4 w-full font-bold text-center text-white">Reset Password</button>
                <a href="reset_password.php" class="bg-[#945034] rounded-lg px-4 py-4 w-full font-bold text-center text-white">Clear</a>
            </div>
        </form>
        <p class="text-center mt-5"><a href="login.php" class="hover:underline hover:font-bold">Back to Login</a></p>
    </div>
</body>
</html> 