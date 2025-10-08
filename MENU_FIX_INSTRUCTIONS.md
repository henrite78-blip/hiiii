# Menu Management Fix Instructions

## Problem Identified

The database is missing the `Category` column in the `MenuItem` table, which is causing the API to fail with 500 errors when trying to fetch or add menu items.

## Solution

### Step 1: Add the Missing Category Column

Run the migration script to add the `Category` column to your database:

```bash
cd "github REpo/servesoft"
php run_migration.php
```

This will:
1. Check if the `Category` column exists
2. Add it if missing with a default value of 'Main Course'
3. Update existing menu items to have the default category
4. Display the updated table structure

### Step 2: Verify the Database Structure

After running the migration, your `MenuItem` table should have these columns:
- `MenuID` (int, primary key, auto_increment)
- `RestaurantID` (int)
- `ItemName` (varchar)
- `ItemDescription` (text)
- `Category` (varchar) ← **NEWLY ADDED**
- `Price` (decimal)
- `Availability` (tinyint)
- `CreatedAt` (timestamp)

### Step 3: Test the Menu Management

1. Log in as a restaurant manager/owner
2. Navigate to "Manage Menu"
3. Try to add a new menu item with all fields filled:
   - Item Name
   - Description
   - Price
   - Category (select from dropdown)
   - Prep Time
4. Verify the item appears in the list
5. Try editing an existing item
6. Try deleting an item
7. Try toggling availability

## Technical Details

### API Endpoints Fixed

1. **api_manager.php**:
   - Added proper OPTIONS handling for CORS
   - Fixed error response formatting
   - Ensured JSON headers are set early

2. **Frontend API (api.js)**:
   - Changed menu operations to use `api_manager.php` instead of `api_customer.php`
   - Fixed field name mapping between frontend and backend
   - Added proper response data transformation

### Field Name Mapping

**Frontend → Backend:**
- `item_name` → `name` (in API request body)
- `item_description` → `description`
- `item_price` → `price`
- `is_available` → `available`

**Backend Database → Frontend Response:**
- `ItemName` → `item_name`
- `ItemDescription` → `item_description`
- `Price` → `item_price`
- `Availability` → `is_available`
- `Category` → `category`

## Alternative: Manual SQL Execution

If you prefer to run the SQL directly:

```sql
-- Add Category column
ALTER TABLE MenuItem
ADD COLUMN Category varchar(50) DEFAULT 'Main Course' AFTER ItemDescription;

-- Update existing records
UPDATE MenuItem
SET Category = 'Main Course'
WHERE Category IS NULL OR Category = '';
```

## Verification

After applying the fix, you should see:
- ✓ Menu items loading without errors
- ✓ Ability to add new menu items
- ✓ Ability to edit existing items
- ✓ Ability to delete items
- ✓ Ability to toggle item availability
- ✓ All changes persisting to the database

## Troubleshooting

If you still see errors:

1. **Check PHP error log** in your server logs
2. **Check browser console** for JavaScript errors
3. **Verify database connection** in `config.php`
4. **Ensure you're logged in** as a manager/owner role
5. **Check that your user has a restaurant assigned** in the database
