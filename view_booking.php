<?php
    require_once("config.php");
    session_start();

    if (!isset($_GET['booking_id'])) {
        header("Location: ridebooking.php");
        exit();
    }

    $booking_id = $_GET['booking_id'];
    
    // Fetch booking details
    $query = "SELECT rb.*, 
                     CONCAT(p.first_name, ' ', p.last_name) as passenger_name,
                     CONCAT(d.first_name, ' ', d.last_name) as driver_name,
                     d.vehicle_type, d.vehicle_plate
              FROM ride_bookings rb 
              JOIN passengers p ON rb.passenger_id = p.passenger_id 
              LEFT JOIN drivers d ON rb.driver_id = d.driver_id
              WHERE rb.booking_id = $booking_id";
    $result = mysqli_query($conn, $query);
    $booking = mysqli_fetch_assoc($result);

    // Check if booking belongs to current user
    if ($booking['passenger_id'] != $_SESSION['passenger_id']) {
        header("Location: ridebooking.php");
        exit();
    }

    // Handle payment button
    if (isset($_POST['process_payment'])) {
        if ($booking['status'] != 'completed') {
            $error = "Cannot process payment - ride is not completed yet!";
        } else {
            // Process payment logic here
            header("Location: ridebooking.php");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>View Booking</title>
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
        <h1 class="text-black text-4xl font-bold text-center mb-8">Your Ride</h1>

        <!-- Booking Details -->
        <div class="bg-white rounded-lg p-6 shadow-md mb-6">
            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-2">
                    <p class="text-[#945034] font-bold">Pickup Location:</p>
                    <p class="text-black"><?php echo htmlspecialchars($booking['pickup_location']); ?></p>
                </div>
                <div class="space-y-2">
                    <p class="text-[#945034] font-bold">Destination:</p>
                    <p class="text-black"><?php echo htmlspecialchars($booking['destination']); ?></p>
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

        <!-- Driver Details (shown only when a driver accepts) -->
        <?php if ($booking['driver_id']): ?>
        <div class="bg-white rounded-lg p-6 shadow-md mb-6">
            <h2 class="text-2xl font-bold mb-4">Driver Information</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-2">
                    <p class="text-[#945034] font-bold">Driver Name:</p>
                    <p class="text-black"><?php echo htmlspecialchars($booking['driver_name']); ?></p>
                </div>
                <div class="space-y-2">
                    <p class="text-[#945034] font-bold">Vehicle Type:</p>
                    <p class="text-black"><?php echo htmlspecialchars($booking['vehicle_type']); ?></p>
                </div>
                <div class="space-y-2">
                    <p class="text-[#945034] font-bold">Vehicle Plate:</p>
                    <p class="text-black"><?php echo htmlspecialchars($booking['vehicle_plate']); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="flex gap-4 justify-center">
            <?php if ($booking['status'] == 'pending'): ?>
                <a href="ridebooking.php" class="bg-red-700 text-white px-8 py-4 rounded-lg font-bold">
                    Cancel Ride
                </a>
            <?php else: ?>
                <form method="POST">
                    <button type="submit" name="process_payment" class="bg-[#5F8B4C] text-white px-8 py-4 rounded-lg font-bold hover:bg-[#4a6d3c]">
                        Process Payment
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 