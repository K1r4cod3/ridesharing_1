<?php
    require_once("config.php");
    require_once("fare_calculator.php");
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
        header("Location: login.php");
        exit();
    }

    $pickupError = $destinationError = "";
    $successMessage = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $pickupAddress = trim($_POST['pickupAddress']);
        $pickupLat = trim($_POST['pickupLat']);
        $pickupLng = trim($_POST['pickupLng']);
        $destinationAddress = trim($_POST['destinationAddress']);
        $destinationLat = trim($_POST['destinationLat']);
        $destinationLng = trim($_POST['destinationLng']);

        // Validate inputs
        if (empty($pickupAddress) || empty($pickupLat) || empty($pickupLng)) {
            $pickupError = "Please select a valid pickup location.";
        }

        if (empty($destinationAddress) || empty($destinationLat) || empty($destinationLng)) {
            $destinationError = "Please select a valid destination.";
        }

        if (empty($pickupError) && empty($destinationError)) {
            // Tính khoảng cách và giá tiền
            $distance = calculateDistance($pickupLat, $pickupLng, $destinationLat, $destinationLng);
            $fare = calculateFare($distance);
            $total_fare = $fare['total_fare'];

            // Insert booking
            $bookingQuery = "INSERT INTO ride_bookings (passenger_id, pickup_location, destination, distance_km, price) 
                           VALUES ({$_SESSION['passenger_id']}, '$pickupAddress', '$destinationAddress', $distance, $total_fare)";
            
            if (mysqli_query($conn, $bookingQuery)) {
                $booking_id = mysqli_insert_id($conn);
                header("Location: view_booking.php?booking_id=$booking_id");
                exit();
            } else {
                $errorMessage = "Error creating booking. Please try again.";
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
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <title>Book a Ride</title>
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
        }
        .suggestion-list {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-height: 200px;
            overflow-y: auto;
            width: 100%;
            z-index: 1000;
            display: none;
        }
        .suggestion-item {
            padding: 8px 12px;
            cursor: pointer;
        }
        .suggestion-item:hover {
            background-color: #f0f0f0;
        }
        .location-input-container {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="border-b-2 border-gray-600 bg-[#ffddab]">
        <nav class="flex justify-between items-center max-w-[1240px] mx-auto py-4">
            <a href="index.php" class="text-[#FF9A9A] font-bold text-2xl">ORSP</a>
            <ul class="text-[#FF9A9A] flex gap-[50px] font-bold">
                <li><a href="ridebooking.php" class="hover:bg-[#5F8B4C] px-4 py-2 rounded-lg">Book a Ride</a></li>
                <li><a href="ride_history.php" class="hover:bg-[#5F8B4C] px-4 py-2 rounded-lg">My History</a></li>
            </ul>
            <div>
                <a href="logout.php" class="text-white bg-[#945034] rounded-lg px-4 py-2">Logout</a>
            </div>
        </nav>
    </div>
    <div class="mx-auto max-w-[1000px] p-4 bg-[#ffddab] mt-10 rounded-lg">
        <h1 class="text-black text-4xl font-bold text-center">Book a Ride</h1>
        <form method="post" class="mx-auto flex flex-col gap-4 max-w-[800px] mt-10">
            <!-- Pickup Location -->
            <div class="location-input-container">
                <input type="text" id="pickupInput" name="pickupAddress" class="rounded-lg px-4 py-2 w-full" 
                       placeholder="Enter pickup location" value="<?php echo isset($_POST['pickupAddress']) ? htmlspecialchars($_POST['pickupAddress']) : ''; ?>">
                <input type="hidden" id="pickupLat" name="pickupLat" value="<?php echo isset($_POST['pickupLat']) ? htmlspecialchars($_POST['pickupLat']) : ''; ?>">
                <input type="hidden" id="pickupLng" name="pickupLng" value="<?php echo isset($_POST['pickupLng']) ? htmlspecialchars($_POST['pickupLng']) : ''; ?>">
                <div id="pickupSuggestions" class="suggestion-list"></div>
                <span class="text-red-500 font-bold"><?php echo $pickupError ?></span>
            </div>

            <!-- Destination Location -->
            <div class="location-input-container">
                <input type="text" id="destinationInput" name="destinationAddress" class="rounded-lg px-4 py-2 w-full" 
                       placeholder="Enter destination" value="<?php echo isset($_POST['destinationAddress']) ? htmlspecialchars($_POST['destinationAddress']) : ''; ?>">
                <input type="hidden" id="destinationLat" name="destinationLat" value="<?php echo isset($_POST['destinationLat']) ? htmlspecialchars($_POST['destinationLat']) : ''; ?>">
                <input type="hidden" id="destinationLng" name="destinationLng" value="<?php echo isset($_POST['destinationLng']) ? htmlspecialchars($_POST['destinationLng']) : ''; ?>">
                <div id="destinationSuggestions" class="suggestion-list"></div>
                <span class="text-red-500 font-bold"><?php echo $destinationError ?></span>
            </div>

            <!-- Map -->
            <div id="map"></div>

            <!-- Fare Estimate -->
            <div id="fareEstimate" class="bg-white p-4 rounded-lg hidden">
                <h2 class="text-xl font-bold mb-2">Fare Estimate</h2>
                <div class="grid grid-cols-2 gap-2">
                    <div>Distance:</div>
                    <div id="estimatedDistance">-</div>
                    <div>Base Fare:</div>
                    <div id="baseFare">-</div>
                    <div>Distance Fare:</div>
                    <div id="distanceFare">-</div>
                    <div class="font-bold text-lg border-t pt-2">Total Fare:</div>
                    <div id="totalFare" class="font-bold text-lg border-t pt-2">-</div>
                </div>
            </div>

            <span class="text-red-500 font-bold"><?php echo isset($errorMessage) ? $errorMessage : ""; ?></span>
            <span class="text-green-500 font-bold"><?php echo $successMessage; ?></span>

            <button type="submit" class="bg-[#945034] text-white px-4 py-2 rounded-lg">Book Ride</button>
        </form>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize map
        const map = L.map('map').setView([10.762622, 106.660172], 13); // Default to Ho Chi Minh City

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let pickupMarker = null;
        let destinationMarker = null;
        let routeLine = null;

        // Function to search location
        async function searchLocation(input, suggestionsDiv, latInput, lngInput) {
            const query = input.value;
            if (query.length < 2) {
                suggestionsDiv.style.display = 'none';
                return;
            }

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
                const data = await response.json();

                suggestionsDiv.innerHTML = '';
                
                if (data.length > 0) {
                    data.forEach(location => {
                        const div = document.createElement('div');
                        div.className = 'suggestion-item';
                        div.textContent = location.display_name;
                        div.addEventListener('click', () => {
                            input.value = location.display_name;
                            latInput.value = location.lat;
                            lngInput.value = location.lon;
                            suggestionsDiv.style.display = 'none';
                            updateMap(input.id === 'pickupInput' ? 'pickup' : 'destination', location);
                        });
                        suggestionsDiv.appendChild(div);
                    });
                    suggestionsDiv.style.display = 'block';
                } else {
                    suggestionsDiv.style.display = 'none';
                }
            } catch (error) {
                console.error('Error:', error);
                suggestionsDiv.style.display = 'none';
            }
        }

        // Function to format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        }

        // Function to update fare estimate
        async function updateFareEstimate() {
            if (!pickupMarker || !destinationMarker) return;

            const pickup = pickupMarker.getLatLng();
            const destination = destinationMarker.getLatLng();

            try {
                const response = await fetch(`fare_calculator.php?action=calculate&pickupLat=${pickup.lat}&pickupLng=${pickup.lng}&destLat=${destination.lat}&destLng=${destination.lng}`);
                const data = await response.json();

                document.getElementById('fareEstimate').classList.remove('hidden');
                document.getElementById('estimatedDistance').textContent = `${data.distance} km`;
                document.getElementById('baseFare').textContent = formatCurrency(data.fare.base_fare);
                document.getElementById('distanceFare').textContent = formatCurrency(data.fare.distance_fare);
                document.getElementById('totalFare').textContent = formatCurrency(data.fare.total_fare);
            } catch (error) {
                console.error('Error calculating fare:', error);
            }
        }

        // Function to update map
        function updateMap(type, location) {
            const lat = parseFloat(location.lat);
            const lon = parseFloat(location.lon);

            if (type === 'pickup') {
                if (pickupMarker) map.removeLayer(pickupMarker);
                pickupMarker = L.marker([lat, lon], {icon: L.divIcon({className: 'custom-div-icon', html: '<div style="background-color: green; width: 10px; height: 10px; border-radius: 50%;"></div>'})}).addTo(map);
                pickupMarker.bindPopup('Pickup Location').openPopup();
            } else {
                if (destinationMarker) map.removeLayer(destinationMarker);
                destinationMarker = L.marker([lat, lon], {icon: L.divIcon({className: 'custom-div-icon', html: '<div style="background-color: red; width: 10px; height: 10px; border-radius: 50%;"></div>'})}).addTo(map);
                destinationMarker.bindPopup('Destination').openPopup();
            }

            // Update map view to show both markers
            if (pickupMarker && destinationMarker) {
                const bounds = L.latLngBounds([pickupMarker.getLatLng(), destinationMarker.getLatLng()]);
                map.fitBounds(bounds, {padding: [50, 50]});
                updateFareEstimate(); // Add fare estimate update
            }
        }

        // Add event listeners for input fields
        const pickupInput = document.getElementById('pickupInput');
        const destinationInput = document.getElementById('destinationInput');
        const pickupSuggestions = document.getElementById('pickupSuggestions');
        const destinationSuggestions = document.getElementById('destinationSuggestions');

        let timeoutId;

        pickupInput.addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                searchLocation(this, pickupSuggestions, document.getElementById('pickupLat'), document.getElementById('pickupLng'));
            }, 300);
        });

        destinationInput.addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                searchLocation(this, destinationSuggestions, document.getElementById('destinationLat'), document.getElementById('destinationLng'));
            }, 300);
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!pickupInput.contains(e.target) && !pickupSuggestions.contains(e.target)) {
                pickupSuggestions.style.display = 'none';
            }
            if (!destinationInput.contains(e.target) && !destinationSuggestions.contains(e.target)) {
                destinationSuggestions.style.display = 'none';
            }
        });
    </script>
</body>
</html> 