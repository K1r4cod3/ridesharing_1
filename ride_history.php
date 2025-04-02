<?php
    require_once("config.php");
    session_start();

    // Check if user is logged in (either as driver or passenger)
    if (!isset($_SESSION['driver_logged_in']) && !isset($_SESSION['logged_in'])) {
        header("Location: login.php");
        exit();
    }

    // Determine if user is driver or passenger
    $is_driver = isset($_SESSION['driver_logged_in']);
    $user_id = $is_driver ? $_SESSION['driver_id'] : $_SESSION['passenger_id'];

    // Fetch completed rides
    $query = "SELECT rb.*, 
                     CONCAT(p.first_name, ' ', p.last_name) as passenger_name,
                     CONCAT(d.first_name, ' ', d.last_name) as driver_name
              FROM ride_bookings rb 
              JOIN passengers p ON rb.passenger_id = p.passenger_id 
              JOIN drivers d ON rb.driver_id = d.driver_id
              WHERE rb.status = 'completed' 
              AND " . ($is_driver ? "rb.driver_id = $user_id" : "rb.passenger_id = $user_id") . "
              ORDER BY rb.booking_id DESC";
    
    $result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>Ride History</title>
</head>
<body>
    <div class="border-b-2 border-gray-600 bg-[#ffddab]">
        <nav class="flex justify-between items-center max-w-[1240px] mx-auto py-4">
            <a href="index.php" class="text-[#FF9A9A] font-bold text-2xl">ORSP</a>
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
        </nav>
    </div>

    <div class="mx-auto max-w-[1000px] p-4 bg-[#ffddab] mt-10 rounded-lg">
        <h1 class="text-black text-4xl font-bold text-center mb-8">Completed Rides History</h1>
        
        <div class="space-y-4">
            <?php while($ride = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Passenger:</p>
                            <p class="text-black"><?php echo $ride['passenger_name']; ?></p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Driver:</p>
                            <p class="text-black"><?php echo $ride['driver_name']; ?></p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Pickup Location:</p>
                            <p class="text-black"><?php echo $ride['pickup_location']; ?></p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Destination:</p>
                            <p class="text-black"><?php echo $ride['destination']; ?></p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Distance:</p>
                            <p class="text-black"><?php echo $ride['distance_km']; ?> km</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Price:</p>
                            <p class="text-black"><?php echo number_format($ride['price'], 0, ',', '.'); ?> VND</p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <?php if (mysqli_num_rows($result) == 0): ?>
                <div class="bg-white rounded-lg p-6 shadow-md text-center">
                    <p class="text-gray-500">No completed rides found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 