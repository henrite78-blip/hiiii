<?php
function getUserRoles($conn, $userId) {
    $roles = [];

    // Check customer role
    $stmt = $conn->prepare('SELECT CustomerID FROM customer WHERE UserID = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $roles[] = ['type' => 'customer', 'id' => $row['CustomerID']];
    }
    $stmt->close();

    // Check admin role
    $stmt = $conn->prepare('SELECT AdminID FROM admin WHERE UserID = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $roles[] = ['type' => 'admin', 'id' => $row['AdminID']];
    }
    $stmt->close();

    // Check staff roles - FIXED: use restaurant_staff (with underscore)
    $stmt = $conn->prepare('SELECT StaffID, RestaurantID, Role, Status FROM restaurant_staff WHERE UserID = ? AND Status = "Active"');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $staff = $result->fetch_assoc();
        $staffId = $staff['StaffID'];
        $restaurantId = $staff['RestaurantID'];

        // Check if manager - use restaurant_manager (with underscore)
        $stmt2 = $conn->prepare('SELECT ManagerID FROM restaurant_manager WHERE StaffID = ?');
        $stmt2->bind_param('i', $staffId);
        $stmt2->execute();
        $managerResult = $stmt2->get_result();
        if ($managerResult->num_rows > 0) {
            $manager = $managerResult->fetch_assoc();
            $roles[] = ['type' => 'manager', 'id' => $manager['ManagerID'], 'staffId' => $staffId, 'restaurantId' => $restaurantId];
        }
        $stmt2->close();

        // Check if delivery agent - use deliveryagent (no underscore)
        $stmt3 = $conn->prepare('SELECT DeliveryAgentID FROM deliveryagent WHERE StaffID = ?');
        $stmt3->bind_param('i', $staffId);
        $stmt3->execute();
        $driverResult = $stmt3->get_result();
        if ($driverResult->num_rows > 0) {
            $driver = $driverResult->fetch_assoc();
            $roles[] = ['type' => 'driver', 'id' => $driver['DeliveryAgentID'], 'staffId' => $staffId, 'restaurantId' => $restaurantId];
        }
        $stmt3->close();

        // If staff but not manager/driver, add as regular staff
        $hasSpecialRole = false;
        foreach ($roles as $role) {
            if ($role['type'] === 'manager' || $role['type'] === 'driver') {
                $hasSpecialRole = true;
                break;
            }
        }
        
        if (!$hasSpecialRole) {
            $roles[] = ['type' => 'staff', 'id' => $staffId, 'restaurantId' => $restaurantId, 'role' => $staff['Role']];
        }
    }
    $stmt->close();

    return $roles;
}

function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }
}

function requireRole($conn, $userId, $requiredRole) {
    $roles = getUserRoles($conn, $userId);
    
    $hasRole = false;
    foreach ($roles as $role) {
        if ($role['type'] === $requiredRole) {
            $hasRole = true;
            break;
        }
    }
    
    if (!$hasRole) {
        http_response_code(403);
        echo json_encode([
            'error' => 'Insufficient permissions', 
            'required' => $requiredRole,
            'userRoles' => array_column($roles, 'type')
        ]);
        exit;
    }
    
    return $roles;
}
?>