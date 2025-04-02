<?php
    require_once("config.php");
    session_start();

    if (!isset($_GET['booking_id'])) {
        header("Location: driver_view_rides.php");
        exit();
    }

    $booking_id = $_GET['booking_id'];
    
    // Handle ride completion
    if (isset($_POST['complete'])) {
        $query = "UPDATE ride_bookings SET status = 'in_progress' WHERE booking_id = '$booking_id'";
        mysqli_query($conn, $query);
        header("Location: driver_view_rides.php");
        exit();
    }

    // Fetch booking details
    $query = "SELECT rb.*, 
                     CONCAT(p.first_name, ' ', p.last_name) as passenger_name,
                     p.phone_number as passenger_phone
              FROM ride_bookings rb 
              JOIN passengers p ON rb.passenger_id = p.passenger_id 
              WHERE rb.booking_id = $booking_id 
              AND rb.driver_id = {$_SESSION['driver_id']}";
    $result = mysqli_query($conn, $query);
    $booking = mysqli_fetch_assoc($result);

    // Redirect if booking not found or doesn't belong to this driver
    if (!$booking) {
        header("Location: driver_view_rides.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>View Ride Details</title>
</head>
<body>
    <div class="border-b-2 border-gray-600 bg-[#ffddab]">
        <nav class="flex justify-between items-center max-w-[1240px] mx-auto py-4">
            <a href="index.php" class="text-[#FF9A9A] font-bold text-2xl">ORSP</a>
            <div>
                <a href="logout.php" class="text-white bg-[#945034] rounded-lg px-4 py-2">Logout</a>
            </div>
        </nav>
    </div>

    <div class="mx-auto max-w-[1000px] p-4 bg-[#ffddab] mt-10 rounded-lg">
        <h1 class="text-black text-4xl font-bold text-center mb-8">Ride Details</h1>

        <!-- Booking Details -->
        <div class="bg-white rounded-lg p-6 shadow-md mb-6">
            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-2">
                    <p class="text-[#945034] font-bold">Pickup Location:</p>
                    <p class="text-black"><?php echo $booking['pickup_location']; ?></p>
                </div>
                <div class="space-y-2">
                    <p class="text-[#945034] font-bold">Destination:</p>
                    <p class="text-black"><?php echo $booking['destination']; ?></p>
                </div>
                <div class="space-y-2">
                    <p class="text-[#945034] font-bold">Distance:</p>
                    <p class="text-black"><?php echo $booking['distance_km']; ?> km</p>
                </div>
                <div class="space-y-2">
                    <p class="text-[#945034] font-bold">Price:</p>
                    <p class="text-black"><?php echo number_format($booking['price'], 0, ',', '.'); ?> VND</p>
                </div>
                <div class="space-y-2">
                    <p class="text-[#945034] font-bold">Status:</p>
                    <p class="text-black"><?php echo ucfirst($booking['status']); ?></p>
                </div>
            </div>
        </div>

        <!-- Passenger Details -->
        <div class="bg-white rounded-lg p-6 shadow-md mb-6">
            <h2 class="text-2xl font-bold mb-4">Passenger Information</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-2">
                    <p class="text-[#945034] font-bold">Passenger Name:</p>
                    <p class="text-black"><?php echo $booking['passenger_name']; ?></p>
                </div>
                <div class="space-y-2">
                    <p class="text-[#945034] font-bold">Phone Number:</p>
                    <p class="text-black"><?php echo $booking['passenger_phone']; ?></p>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <?php if ($booking['status'] == 'accepted'): ?>
        <div class="flex justify-center">
            <form method="POST">
                <button type="submit" name="complete" class="bg-[#5F8B4C] text-white px-8 py-4 rounded-lg font-bold hover:bg-[#4a6d3c]">
                    Complete Ride
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html> 