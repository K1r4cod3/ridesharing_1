<?php
    require_once("config.php");
    session_start();

    // Redirect if driver is not logged in
    if (!isset($_SESSION['driver_logged_in'])) {
        header("Location: driver_login.php");
        exit();
    }

    // Handle ride acceptance
    if (isset($_POST['accept'])) {
        $booking_id = $_POST['booking_id'];
        $driver_id = $_SESSION['driver_id'];

        // Update the booking status and assign driver
        $query = "UPDATE ride_bookings 
                 SET status = 'accepted', 
                     driver_id = '$driver_id'
                 WHERE booking_id = '$booking_id'";
        
        mysqli_query($conn, $query);
        header("Location: driver_view_booking.php?booking_id=$booking_id");
        exit();
    }

    // Fetch all pending ride bookings
    $query = "SELECT rb.*, 
                     CONCAT(p.first_name, ' ', p.last_name) as passenger_name
              FROM ride_bookings rb 
              JOIN passengers p ON rb.passenger_id = p.passenger_id 
              WHERE rb.status = 'pending'";
    $result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>Available Rides</title>
</head>
<body>
    <div class="border-b-2 border-gray-600 bg-[#ffddab]">
        <nav class="flex justify-between items-center max-w-[1240px] mx-auto py-4">
            <a href="index.php" class="text-[#FF9A9A] font-bold text-2xl">ORSP</a>
            <ul class="text-[#FF9A9A] flex gap-[50px] font-bold">
                <li><a href="driver_view_rides.php" class="hover:bg-[#5F8B4C] px-4 py-2 rounded-lg">Available Rides</a></li>
                <li><a href="ride_history.php" class="hover:bg-[#5F8B4C] px-4 py-2 rounded-lg">My History</a></li>
            </ul>
            <div>
                <a href="logout.php" class="text-white bg-[#945034] rounded-lg px-4 py-2">Logout</a>
            </div>
        </nav>
    </div>

    <div class="mx-auto max-w-[1000px] p-4 bg-[#ffddab] mt-10 rounded-lg">
        <h1 class="text-black text-4xl font-bold text-center mb-8">Available Ride Requests</h1>
        
        <div class="space-y-4">
            <?php while($ride = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Passenger Name:</p>
                            <p class="text-black"><?php echo $ride['passenger_name']; ?></p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Price:</p>
                            <p class="text-black"><?php echo number_format($ride['price'], 0, ',', '.'); ?> VND</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Distance:</p>
                            <p class="text-black"><?php echo $ride['distance_km']; ?> km</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Pickup Location:</p>
                            <p class="text-black"><?php echo $ride['pickup_location']; ?></p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Destination:</p>
                            <p class="text-black"><?php echo $ride['destination']; ?></p>
                        </div>
                    </div>
                    
                    <form method="POST" class="mt-6">
                        <input type="hidden" name="booking_id" value="<?php echo $ride['booking_id']; ?>">
                        <button type="submit" name="accept" class="bg-[#945034] rounded-lg px-4 py-4 w-full font-bold text-center text-white hover:bg-[#7a4029]">
                            Accept Ride
                        </button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html> 