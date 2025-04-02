<?php
    session_start();
    $is_driver = isset($_SESSION['driver_logged_in']);
    $is_logged_in = isset($_SESSION['logged_in']) || isset($_SESSION['driver_logged_in']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>ORSP - Online Ride Sharing Platform</title>
</head>
<body>
    <div class="border-b-2 border-gray-600 bg-[#ffddab]">
        <nav class="flex justify-between items-center max-w-[1240px] mx-auto py-4">
            <a href="index.php" class="text-[#FF9A9A] font-bold text-2xl">ORSP</a>
            <?php if ($is_logged_in): ?>
                <ul class="text-[#FF9A9A] flex gap-[50px] font-bold">
                    <?php if ($is_driver): ?>
                        <li><a href="driver_view_rides.php" class="hover:bg-[#5F8B4C] px-4 py-2 rounded-lg">Available Rides</a></li>
                    <?php else: ?>
                        <li><a href="ridebooking.php" class="hover:bg-[#5F8B4C] px-4 py-2 rounded-lg">Book a Ride</a></li>
                    <?php endif; ?>
                    <li><a href="ride_history.php" class="hover:bg-[#5F8B4C] px-4 py-2 rounded-lg">My History</a></li>
                </ul>
                <div>
                    <a href="logout.php" class="text-white bg-[#945034] rounded-lg px-4 py-2">Logout</a>
                </div>
            <?php else: ?>
                <div class="flex gap-4">
                    <a href="driver_login.php" class="text-white bg-[#5F8B4C] rounded-lg px-4 py-2">Driver Login</a>
                    <a href="login.php" class="text-white bg-[#945034] rounded-lg px-4 py-2">Login</a>
                    <a href="register.php" class="text-white bg-[#945034] rounded-lg px-4 py-2">Register</a>
                </div>
            <?php endif; ?>
        </nav>
    </div>

    <div class="mx-auto max-w-[1000px] p-8 bg-[#ffddab] mt-10 rounded-lg text-center">
        <h1 class="text-4xl font-bold mb-6">Welcome to ORSP</h1>
        <p class="text-xl mb-8">Your trusted online ride-sharing platform</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-[#945034] text-xl font-bold mb-4">Quick Booking</h2>
                <p>Book your ride in just a few clicks. Fast, easy, and convenient.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-[#945034] text-xl font-bold mb-4">Safe Rides</h2>
                <p>All our drivers are verified and monitored to ensure your safety.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-[#945034] text-xl font-bold mb-4">Fair Pricing</h2>
                <p>Transparent pricing based on distance. No hidden charges.</p>
            </div>
        </div>

        <?php if (!$is_logged_in): ?>
            <div class="space-x-4">
                <a href="register.php" class="inline-block bg-[#945034] text-white px-6 py-3 rounded-lg font-bold hover:bg-[#794329]">Get Started</a>
                <a href="driver_register.php" class="inline-block bg-[#5F8B4C] text-white px-6 py-3 rounded-lg font-bold hover:bg-[#4F7A3C]">Become a Driver</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 