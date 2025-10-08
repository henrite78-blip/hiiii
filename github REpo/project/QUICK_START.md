# Quick Start Guide - SmartBite with ServeSoft Backend

## Setup Instructions

### 1. Database Setup
```sql
-- Make sure your MySQL server is running
-- Import the database schema
mysql -u root -p smartbite < seed_smartbite.sql
```

### 2. ServeSoft Backend Setup
- Ensure files are in your web server root (e.g., `C:\xampp\htdocs\servesoft`)
- Verify `config.php` has correct database credentials:
  ```php
  $host = 'localhost';
  $username = 'root';
  $password = '';
  $database = 'smartbite';
  ```

### 3. Frontend Setup
```bash
cd "github REpo/smartbite"
npm install
npm run dev
```

## Testing the Fix

### Test 1: Register as Customer
1. Go to: `http://localhost:5173/register`
2. Select: **Customer**
3. Fill in details and submit
4. ✅ Should redirect to: `/customer` (Customer Dashboard)

### Test 2: Register as Restaurant Owner
1. Go to: `http://localhost:5173/register`
2. Select: **Restaurant Owner**
3. Fill in details and submit
4. ✅ Should redirect to: `/owner` (Restaurant Dashboard)

### Test 3: Register as Agent
1. Go to: `http://localhost:5173/register`
2. Select: **Agent**
3. Fill in details and submit
4. ✅ Should redirect to: `/agent` (Delivery Dashboard)

### Test 4: Login Flow
1. Logout from current session
2. Go to: `http://localhost:5173/login`
3. Login with any registered account
4. ✅ Should redirect to role-specific dashboard

## Troubleshooting

### Issue: "CORS Error"
**Solution**: Ensure `cors.php` is included in API files
```php
<?php
require 'cors.php';
session_start();
```

### Issue: "Not redirecting to correct dashboard"
**Check**:
1. Browser console for errors
2. Network tab - check `/api_auth.php?action=register` response
3. Should contain: `user: { role: 'customer|owner|agent|admin' }`

### Issue: "Session not persisting"
**Solution**: Check that cookies are being sent
- Verify `credentials: 'include'` in API calls
- Check browser allows cookies from localhost

## Key Files Modified

### Backend (ServeSoft)
- ✅ `api_auth.php` - Fixed registration to return user with role
- ✅ `cors.php` - Handles CORS for frontend

### Frontend (SmartBite)
- ✅ `src/services/api.js` - Updated to send role and handle response
- ✅ `src/App.tsx` - Routes users based on role
- ✅ `src/contexts/AuthContext.tsx` - Manages user state
- ✅ `package.json` - Removed Node.js dependencies
- ✅ Deleted `/server` directory entirely

## Default Credentials for Testing

You can create test accounts for each role:

**Customer**:
- Email: customer@test.com
- Password: Test@123
- Role: Customer

**Restaurant Owner**:
- Email: owner@test.com
- Password: Test@123
- Role: Owner

**Delivery Agent**:
- Email: agent@test.com
- Password: Test@123
- Role: Agent

**Admin** (Only one allowed):
- Email: admin@test.com
- Password: Admin@123
- Role: Admin

## Role-Based Dashboard Features

### Customer Dashboard (`/customer`)
- Browse restaurants
- View menu items
- Add items to cart
- Place orders
- Track order status

### Restaurant Owner Dashboard (`/owner`)
- Manage restaurant profile
- Add/edit/delete menu items
- View incoming orders
- Update order status

### Delivery Agent Dashboard (`/agent`)
- View available delivery jobs
- Accept deliveries
- Update delivery status
- Track earnings

### Admin Dashboard (`/admin`)
- Manage users
- Approve/reject restaurants
- View system statistics
- Monitor platform activity

## Architecture Overview

```
Frontend (React + Vite)
    ↓
Vite Proxy (/servesoft → http://localhost/servesoft)
    ↓
ServeSoft Backend (PHP)
    ↓
MySQL Database (smartbite)
```

## Support

If you encounter issues:
1. Check browser console for errors
2. Check PHP error logs
3. Verify database connection
4. Ensure all services are running (Apache, MySQL)
