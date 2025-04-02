<?php
    require_once("config.php");
    session_start();

    if (!isset($_SESSION['logged_in']) && !isset($_SESSION['driver_logged_in'])) {
        header("Location: login.php");
        exit();
    }

    $is_driver = isset($_SESSION['driver_logged_in']);
    $user_id = $is_driver ? $_SESSION['driver_id'] : $_SESSION['passenger_id'];

    // Fetch completed rides from ride_records
    $query = "SELECT rr.*, rb.pickup_location, rb.destination, rb.distance_km, rb.price,
                     CONCAT(p.first_name, ' ', p.last_name) as passenger_name,
                     CONCAT(d.first_name, ' ', d.last_name) as driver_name,
                     d.vehicle_type, d.vehicle_plate
              FROM ride_records rr
              JOIN ride_bookings rb ON rr.booking_id = rb.booking_id
              JOIN passengers p ON rr.passenger_id = p.passenger_id
              JOIN drivers d ON rr.driver_id = d.driver_id
              WHERE " . ($is_driver ? "rr.driver_id = $user_id" : "rr.passenger_id = $user_id") . "
              ORDER BY rr.record_id DESC";

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
            <ul class="text-[#FF9A9A] flex gap-[50px] font-bold ml-[120px]">
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
                            <p class="text-black"><?php echo htmlspecialchars($ride['passenger_name']); ?></p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Driver:</p>
                            <p class="text-black"><?php echo htmlspecialchars($ride['driver_name']); ?></p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Vehicle:</p>
                            <p class="text-black">
                                <?php echo htmlspecialchars($ride['vehicle_type']); ?> 
                                (<?php echo htmlspecialchars($ride['vehicle_plate']); ?>)
                            </p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Distance:</p>
                            <p class="text-black"><?php echo $ride['distance_km']; ?> km</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Price:</p>
                            <p class="text-black"><?php echo number_format($ride['price'], 0, ',', '.'); ?> VND</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[#945034] font-bold">Route:</p>
                            <p class="text-black">
                                From: <?php echo htmlspecialchars($ride['pickup_location']); ?><br>
                                To: <?php echo htmlspecialchars($ride['destination']); ?>
                            </p>
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