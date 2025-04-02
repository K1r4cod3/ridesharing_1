<?php
    require_once("config.php");

    $usernameError = $passwordError = "";
    $errorMessage = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Username validation
        if (empty($username)) {
            $usernameError = "Username is required.";
        }

        // Password validation
        if (empty($password)) {
            $passwordError = "Password is required.";
        }

        if (empty($usernameError) && empty($passwordError)) {
            $query = "SELECT * FROM passengers WHERE username = '$username' AND password = '$password'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 1) {
                session_start();
                $passenger = mysqli_fetch_assoc($result);
                $_SESSION["passenger_id"] = $passenger['passenger_id'];
                $_SESSION["username"] = $username;
                $_SESSION["logged_in"] = true;
                header("Location: ridebooking.php");
                exit();
            } else {
                $errorMessage = "Incorrect username or password.";
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
    <title>Login</title>
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
        <h1 class="text-black text-4xl font-bold text-center">Login Page</h1>
        <form method="post" class="mx-auto flex flex-col gap-2 max-w-[400px] mt-10">
            <input type="text" name="username" id="username" class="rounded-lg px-4 py-2" placeholder="Username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            <span class="text-red-500 font-bold"><?php echo $usernameError ?></span>
            <input type="password" name="password" id="password" class="rounded-lg px-4 py-2" placeholder="Password">
            <span class="text-red-500 font-bold"><?php echo $passwordError; ?></span>
            <span class="text-red-500 font-bold"><?php echo $errorMessage; ?></span>
            <div class="flex gap-2 w-full">
                <button type="submit" class="bg-[#945034] rounded-lg px-4 py-4 w-full font-bold text-center text-white">Login</button>
                <a href="login.php" class="bg-[#945034] rounded-lg px-4 py-4 w-full font-bold text-center text-white">Clear</a>
            </div>
        </form>
        <div class="mt-5">
            <p class="text-center">Don't have an account? <a href="register.php" class="hover:underline hover:font-bold">Register here</a></p>
            <p class="text-center"><a href="reset_password.php" class="hover:underline hover:font-bold">Forgot Password?</a></p>
        </div>
    </div>
</body>
</html> 