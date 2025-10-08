<?php
require 'config.php';

echo "Running database migration to add Category column...\n";

// Check if Category column exists
$result = $conn->query("SHOW COLUMNS FROM MenuItem LIKE 'Category'");

if ($result->num_rows == 0) {
    echo "Category column does not exist. Adding it now...\n";

    // Add Category column
    $sql = "ALTER TABLE MenuItem ADD COLUMN Category varchar(50) DEFAULT 'Main Course' AFTER ItemDescription";

    if ($conn->query($sql) === TRUE) {
        echo "✓ Category column added successfully\n";

        // Update existing items
        $updateSql = "UPDATE MenuItem SET Category = 'Main Course' WHERE Category IS NULL OR Category = ''";
        if ($conn->query($updateSql) === TRUE) {
            echo "✓ Existing menu items updated\n";
        }
    } else {
        echo "✗ Error adding Category column: " . $conn->error . "\n";
    }
} else {
    echo "✓ Category column already exists\n";
}

// Verify the column was added
$result = $conn->query("DESCRIBE MenuItem");
echo "\nMenuItem table structure:\n";
echo str_repeat("-", 80) . "\n";
printf("%-20s %-20s %-10s %-10s\n", "Field", "Type", "Null", "Default");
echo str_repeat("-", 80) . "\n";

while ($row = $result->fetch_assoc()) {
    printf("%-20s %-20s %-10s %-10s\n",
        $row['Field'],
        $row['Type'],
        $row['Null'],
        $row['Default'] ?? 'NULL'
    );
}

echo "\nMigration complete!\n";
$conn->close();
?>
