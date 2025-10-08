<?php
session_start();
require 'config.php';

// Add this line:
header('Content-Type: application/json; charset=utf-8');

// Rest of your code...
$response = [
    'restaurants' => [],
    'tables' => [],
    'menuItems' => []
];

try {
    $restaurantQuery = $conn->query('SELECT RestaurantID, RestaurantName, Status, Location, ContactNumber, Address FROM Restaurant');
    while ($row = $restaurantQuery->fetch_assoc()) {
        $response['restaurants'][] = [
            'id' => 'r' . $row['RestaurantID'],
            'name' => $row['RestaurantName'],
            'status' => $row['Status'] ?? 'ACTIVE',
            'location' => $row['Location'] ?? '',
            'phone' => $row['ContactNumber'] ?? '',
            'address' => $row['Address'] ?? '',
            'hours' => null,
            'serviceRules' => null
        ];
    }
    $restaurantQuery->close();

    $tableQuery = $conn->query('SELECT TableID, RestaurantID, TableNumber, Capacity, Status FROM RestaurantTable');
    while ($row = $tableQuery->fetch_assoc()) {
        $response['tables'][] = [
            'id' => 't' . $row['TableID'],
            'restaurantId' => 'r' . $row['RestaurantID'],
            'label' => 'Table ' . $row['TableNumber'],
            'capacity' => (int) $row['Capacity'],
            'state' => $row['Status'] ?? 'FREE'
        ];
    }
    $tableQuery->close();

    $menuQuery = $conn->query('SELECT MenuID, RestaurantID, ItemName, ItemDescription, Availability, Price FROM MenuItem');
    while ($row = $menuQuery->fetch_assoc()) {
        $response['menuItems'][] = [
            'id' => 'm' . $row['MenuID'],
            'restaurantId' => 'r' . $row['RestaurantID'],
            'name' => $row['ItemName'],
            'description' => $row['ItemDescription'] ?? '',
            'category' => 'General',
            'available' => (bool) $row['Availability'],
            'price' => (float) $row['Price'],
            'modifiers' => []
        ];
    }
    $menuQuery->close();

    echo json_encode($response);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to read SERVESOFT data',
        'details' => $error->getMessage()
    ]);
}

ob_end_flush(); 
?>