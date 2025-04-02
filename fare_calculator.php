<?php
require_once("config.php");

// Các hằng số để tính giá
define('BASE_FARE', 15000);           // Giá khởi điểm: 15,000 VND
define('PRICE_PER_KM', 5000);         // Giá mỗi km: 5,000 VND

function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Bán kính trái đất tính bằng km

    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    $latDelta = $lat2 - $lat1;
    $lonDelta = $lon2 - $lon1;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($lonDelta / 2), 2)));
    return $angle * $earthRadius;
}

function calculateFare($distance) {
    $baseFare = BASE_FARE;
    $distanceFare = $distance * PRICE_PER_KM;

    return [
        'base_fare' => $baseFare,
        'distance_fare' => $distanceFare,
        'total_fare' => $baseFare + $distanceFare
    ];
}

// API endpoint để tính giá
if (isset($_GET['action']) && $_GET['action'] === 'calculate') {
    header('Content-Type: application/json');
    
    // Tính khoảng cách dựa trên tọa độ
    $distance = calculateDistance(
        $_GET['pickupLat'],
        $_GET['pickupLng'],
        $_GET['destLat'],
        $_GET['destLng']
    );
    
    $fare = calculateFare($distance);
    
    echo json_encode([
        'distance' => round($distance, 2),
        'fare' => $fare
    ]);
    exit;
}
?> 