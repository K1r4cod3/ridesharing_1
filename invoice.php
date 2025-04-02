<?php
    require_once("config.php");
    require_once("payment_methods.php");
    session_start();

    if (!isset($_GET['booking_id']) || !isset($_GET['payment_method'])) {
        header("Location: ridebooking.php");
        exit();
    }

    $booking_id = $_GET['booking_id'];
    $payment_method = $_GET['payment_method'];
    
    // Validate payment method
    if (!array_key_exists($payment_method, $payment_methods)) {
        header("Location: ridebooking.php");
        exit();
    }

    // Fetch booking details
    $query = "SELECT rb.*, 
                     CONCAT(p.first_name, ' ', p.last_name) as passenger_name,
                     CONCAT(d.first_name, ' ', d.last_name) as driver_name,
                     d.vehicle_type, d.vehicle_plate
              FROM ride_bookings rb 
              JOIN passengers p ON rb.passenger_id = p.passenger_id 
              JOIN drivers d ON rb.driver_id = d.driver_id
              WHERE rb.booking_id = $booking_id 
              AND rb.passenger_id = {$_SESSION['passenger_id']}";
    $result = mysqli_query($conn, $query);
    $booking = mysqli_fetch_assoc($result);

    if (!$booking || $booking['status'] != 'in_progress') {
        header("Location: ridebooking.php");
        exit();
    }

    // Handle confirmation
    if (isset($_POST['confirm_payment'])) {
        // Start transaction
        mysqli_begin_transaction($conn);

        try {
            // Update booking status
            $update_query = "UPDATE ride_bookings SET status = 'completed' WHERE booking_id = $booking_id";
            mysqli_query($conn, $update_query);

            // Insert into ride_records
            $insert_query = "INSERT INTO ride_records (booking_id, passenger_id, driver_id) 
                           VALUES ($booking_id, {$booking['passenger_id']}, {$booking['driver_id']})";
            mysqli_query($conn, $insert_query);

            // Commit transaction
            mysqli_commit($conn);
            header("Location: ridebooking.php");
            exit();
        } catch (Exception $e) {
            // Rollback on error
            mysqli_rollback($conn);
            $error = "An error occurred while processing your payment. Please try again.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>Invoice</title>
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

    <div class="mx-auto max-w-[800px] p-8 bg-white mt-10 rounded-lg shadow-lg">
        <h1 class="text-4xl font-bold text-center mb-8">Invoice</h1>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="space-y-6">
            <div class="border-b pb-4">
                <h2 class="text-2xl font-bold mb-4">Ride Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Pickup Location:</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($booking['pickup_location']); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Destination:</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($booking['destination']); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Distance:</p>
                        <p class="font-semibold"><?php echo $booking['distance_km']; ?> km</p>
                    </div>
                </div>
            </div>

            <div class="border-b pb-4">
                <h2 class="text-2xl font-bold mb-4">People</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Passenger:</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($booking['passenger_name']); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Driver:</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($booking['driver_name']); ?></p>
                    </div>
                </div>
            </div>

            <div class="border-b pb-4">
                <h2 class="text-2xl font-bold mb-4">Payment</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Amount:</p>
                        <p class="font-semibold text-xl"><?php echo number_format($booking['price'], 0, ',', '.'); ?> VND</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Payment Method:</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($payment_methods[$payment_method]); ?></p>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <form method="POST">
                    <button type="submit" name="confirm_payment" class="bg-[#5F8B4C] text-white px-8 py-4 rounded-lg font-bold hover:bg-[#4a6d3c]">
                        Confirm Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 