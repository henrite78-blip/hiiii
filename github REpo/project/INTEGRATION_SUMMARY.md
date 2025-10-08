# SmartBite Frontend Integration with ServeSoft Backend

## Summary of Changes

This document outlines the changes made to integrate the SmartBite frontend application with the ServeSoft PHP backend, removing the Node.js backend entirely and fixing the RBAC (Role-Based Access Control) routing issue.

## Problem Identified

**Original Issue**: When users registered, they were always redirected to the customer dashboard regardless of the role selected (Customer, Restaurant Owner, or Agent). The system was not properly handling role-based routing after registration.

**Root Cause**:
1. The registration API endpoint was not returning the user object with role information
2. The frontend had to perform a separate login after registration to get user data
3. The session wasn't properly initialized during registration

## Changes Made

### 1. Backend Changes (ServeSoft)

#### File: `api_auth.php`

**Added CORS Support**:
- Added `require 'cors.php';` at the beginning to enable cross-origin requests from the frontend

**Fixed Registration Response**:
- Modified the registration endpoint to immediately create the user session
- Now returns the complete user object with role information after registration
- Eliminates the need for a separate login call after registration

**Before**:
```php
echo json_encode([
    'success' => true,
    'message' => 'Account created successfully',
    'userId' => $userId
]);
```

**After**:
```php
// Set session immediately
$_SESSION['user_id'] = $userId;
$_SESSION['name'] = $name;
$_SESSION['email'] = $email;
$_SESSION['phone'] = $phoneNumber;
$_SESSION['primary_role'] = $primaryRole;
$_SESSION['role_data'] = $roleData;

echo json_encode([
    'success' => true,
    'message' => 'Account created successfully',
    'user' => [
        'id' => 'u' . $userId,
        'name' => $name,
        'email' => $email,
        'phone' => $phoneNumber,
        'role' => $primaryRole,
        'roleData' => $roleData
    ]
]);
```

### 2. Frontend Changes (SmartBite)

#### File: `src/services/api.js`

**Updated Registration Flow**:
- Modified to pass the selected role to the backend
- Now uses the user object returned directly from registration
- Falls back to login only if user object is not returned

**Added checkAdmin Endpoint**:
```javascript
checkAdmin: async () => {
  const response = await serveSoftAPI.request('api_auth.php?action=checkAdmin');
  return response;
}
```

#### Removed Node.js Backend

**Deleted**:
- Entire `/server` directory containing Node.js backend
- Removed unused dependencies from `package.json`:
  - `axios` (replaced with native fetch)
  - `socket.io-client`
  - `bcryptjs`
  - `concurrently`
  - `cors`
  - `dotenv`
  - `express`
  - `jsonwebtoken`
  - `mysql2`
  - `nodemon`
  - `socket.io`

**Updated Dependencies**:
- Downgraded React from 19.1.1 to 18.3.1 for better compatibility with lucide-react
- Updated lucide-react to latest compatible version (0.460.0)

### 3. How RBAC Now Works

The system now properly handles role-based routing:

1. **Registration**:
   - User selects role (Customer, Restaurant Owner, Agent, or Admin)
   - Backend creates appropriate database records based on role
   - Backend immediately establishes session with role information
   - Returns complete user object with role to frontend

2. **Authentication Context**:
   - Stores user object with role in React state and localStorage
   - AuthContext provides user role to all components

3. **Routing**:
   - App.tsx checks user role and redirects to appropriate dashboard
   - Protected routes enforce role-based access control
   - Routes:
     - `/customer` → Customer Dashboard
     - `/owner` → Restaurant Owner Dashboard
     - `/agent` → Delivery Agent Dashboard
     - `/admin` → Admin Dashboard

4. **Role Detection in Backend** (Login flow):
   - Checks Admin table
   - Checks Restaurant_Manager table (maps to 'owner' role)
   - Checks DeliveryAgent table (maps to 'agent' role)
   - Falls back to Customer table

## Testing the Integration

### Prerequisites
1. Ensure XAMPP/WAMP is running with Apache and MySQL
2. Database `smartbite` is created and populated
3. ServeSoft files are in the web server's document root (e.g., `/servesoft`)

### Steps to Test

1. **Start Frontend**:
   ```bash
   cd "github REpo/smartbite"
   npm install
   npm run dev
   ```

2. **Test Registration Flow**:
   - Navigate to `http://localhost:5173/register`
   - Select a role (e.g., "Restaurant Owner")
   - Fill in registration form
   - Submit
   - **Expected Result**: User should be redirected to `/owner` dashboard

3. **Test Different Roles**:
   - Register as Customer → Should redirect to `/customer`
   - Register as Agent → Should redirect to `/agent`
   - Register as Admin (first time only) → Should redirect to `/admin`

4. **Test Login Flow**:
   - Logout
   - Login with registered credentials
   - **Expected Result**: Should redirect to role-specific dashboard

## API Endpoints Used

### Authentication
- `POST /servesoft/api_auth.php?action=register` - Register new user
- `POST /servesoft/api_auth.php?action=login` - Login user
- `GET /servesoft/api_auth.php?action=check` - Verify session
- `POST /servesoft/api_auth.php?action=logout` - Logout user
- `GET /servesoft/api_auth.php?action=checkAdmin` - Check if admin exists

### Customer APIs
- Managed through `/servesoft/api_customer.php`

### Restaurant Manager APIs
- Managed through `/servesoft/api_manager.php`

### Delivery Agent APIs
- Managed through `/servesoft/api_driver.php`

### Admin APIs
- Managed through `/servesoft/api_admin.php`

## Configuration

### Vite Proxy Configuration
Located in `vite.config.ts`:
```typescript
server: {
  port: 5173,
  proxy: {
    '/servesoft': {
      target: 'http://localhost',
      changeOrigin: true,
      secure: false,
      rewrite: (path) => path
    }
  }
}
```

This proxies all `/servesoft/*` requests to `http://localhost/servesoft/*`

### CORS Configuration
The `cors.php` file in ServeSoft handles CORS headers for the following origins:
- `http://localhost:5173`
- `http://localhost:3000`
- `http://127.0.0.1:5173`

## Important Notes

1. **Session Management**: Uses PHP sessions, not JWT tokens
2. **Authentication**: Session-based authentication with cookies
3. **Credentials**: Set `credentials: 'include'` in all API calls to send cookies
4. **Build Success**: Project builds successfully without errors

## Next Steps

1. Test all user flows thoroughly
2. Add proper error handling for network failures
3. Implement password reset functionality
4. Add profile management features
5. Test with real data and multiple concurrent users
