# SmartBite RBAC Testing Checklist

## Pre-Test Setup
- [ ] MySQL server is running
- [ ] Apache server is running
- [ ] Database `smartbite` exists and is populated
- [ ] ServeSoft files are in web root (e.g., `/servesoft`)
- [ ] Frontend dependencies installed (`npm install`)
- [ ] Frontend dev server running (`npm run dev`)

## Test 1: Customer Registration & Routing ✅
**Steps**:
1. Navigate to `http://localhost:5173/register`
2. Select role: **Customer**
3. Enter details:
   - Full Name: Test Customer
   - Email: customer1@test.com
   - Phone: +237600000001
   - Town: Yaoundé
   - Password: Test@123
   - Confirm Password: Test@123
4. Click "Create Account"

**Expected Results**:
- ✅ No errors in console
- ✅ Redirected to `/customer`
- ✅ Customer dashboard is displayed
- ✅ User name shows "Test Customer" in header
- ✅ Header shows "Customer" badge
- ✅ Can browse restaurants

**Verification**:
- Check database: `SELECT * FROM User WHERE Email='customer1@test.com'`
- Check session: User should have `role: 'customer'`

---

## Test 2: Restaurant Owner Registration & Routing ✅
**Steps**:
1. Navigate to `http://localhost:5173/register`
2. Select role: **Restaurant Owner**
3. Enter details:
   - Full Name: Test Owner
   - Email: owner1@test.com
   - Phone: +237600000002
   - Town: Douala
   - Password: Test@123
   - Confirm Password: Test@123
4. Click "Create Account"

**Expected Results**:
- ✅ No errors in console
- ✅ Redirected to `/owner`
- ✅ Restaurant dashboard is displayed
- ✅ User name shows "Test Owner" in header
- ✅ Header shows "Restaurant Owner" or "Owner" badge
- ✅ Can see "Manage Menu" and "Orders" sections

**Verification**:
- Check database:
  - `SELECT * FROM User WHERE Email='owner1@test.com'`
  - `SELECT * FROM Restaurant_Manager WHERE UserID = (SELECT UserID FROM User WHERE Email='owner1@test.com')`

---

## Test 3: Delivery Agent Registration & Routing ✅
**Steps**:
1. Navigate to `http://localhost:5173/register`
2. Select role: **Agent**
3. Enter details:
   - Full Name: Test Agent
   - Email: agent1@test.com
   - Phone: +237600000003
   - Town: Bamenda
   - Password: Test@123
   - Confirm Password: Test@123
4. Click "Create Account"

**Expected Results**:
- ✅ No errors in console
- ✅ Redirected to `/agent`
- ✅ Delivery dashboard is displayed
- ✅ User name shows "Test Agent" in header
- ✅ Header shows "Agent" or "Delivery Agent" badge
- ✅ Can see available deliveries

**Verification**:
- Check database:
  - `SELECT * FROM User WHERE Email='agent1@test.com'`
  - `SELECT * FROM DeliveryAgent WHERE UserID = (SELECT UserID FROM User WHERE Email='agent1@test.com')`

---

## Test 4: Admin Registration & Routing ✅
**Steps**:
1. Navigate to `http://localhost:5173/register`
2. **Verify**: Admin option is visible (only if no admin exists)
3. Select role: **Admin**
4. Enter details:
   - Full Name: Test Admin
   - Email: admin1@test.com
   - Phone: +237600000004
   - Town: Yaoundé
   - Password: Admin@123
   - Confirm Password: Admin@123
5. Click "Create Account"

**Expected Results**:
- ✅ No errors in console
- ✅ Redirected to `/admin`
- ✅ Admin dashboard is displayed
- ✅ User name shows "Test Admin" in header
- ✅ Header shows "Admin" badge
- ✅ Can see user management, restaurant management

**Verification**:
- Check database:
  - `SELECT * FROM User WHERE Email='admin1@test.com'`
  - `SELECT * FROM Admin WHERE UserID = (SELECT UserID FROM User WHERE Email='admin1@test.com')`
- Try to register another admin: Should show error "Admin account already exists"

---

## Test 5: Login Flow - Customer ✅
**Steps**:
1. Logout if logged in
2. Navigate to `http://localhost:5173/login`
3. Enter credentials:
   - Email: customer1@test.com
   - Password: Test@123
4. Click "Sign In"

**Expected Results**:
- ✅ No errors in console
- ✅ Redirected to `/customer`
- ✅ Customer dashboard displayed
- ✅ User data persists on page refresh

---

## Test 6: Login Flow - Restaurant Owner ✅
**Steps**:
1. Logout if logged in
2. Navigate to `http://localhost:5173/login`
3. Enter credentials:
   - Email: owner1@test.com
   - Password: Test@123
4. Click "Sign In"

**Expected Results**:
- ✅ No errors in console
- ✅ Redirected to `/owner`
- ✅ Restaurant dashboard displayed
- ✅ User data persists on page refresh

---

## Test 7: Login Flow - Delivery Agent ✅
**Steps**:
1. Logout if logged in
2. Navigate to `http://localhost:5173/login`
3. Enter credentials:
   - Email: agent1@test.com
   - Password: Test@123
4. Click "Sign In"

**Expected Results**:
- ✅ No errors in console
- ✅ Redirected to `/agent`
- ✅ Delivery dashboard displayed
- ✅ User data persists on page refresh

---

## Test 8: Login Flow - Admin ✅
**Steps**:
1. Logout if logged in
2. Navigate to `http://localhost:5173/login`
3. Enter credentials:
   - Email: admin1@test.com
   - Password: Admin@123
4. Click "Sign In"

**Expected Results**:
- ✅ No errors in console
- ✅ Redirected to `/admin`
- ✅ Admin dashboard displayed
- ✅ User data persists on page refresh

---

## Test 9: Protected Route Access ✅
**Steps**:
1. Login as Customer
2. Try to manually navigate to:
   - `http://localhost:5173/owner`
   - `http://localhost:5173/agent`
   - `http://localhost:5173/admin`

**Expected Results**:
- ✅ Each attempt redirects back to `/customer`
- ✅ User cannot access other role dashboards

**Repeat for each role**:
- Owner should only access `/owner` routes
- Agent should only access `/agent` routes
- Admin should only access `/admin` routes

---

## Test 10: Session Persistence ✅
**Steps**:
1. Login as any role
2. Refresh the page (F5)
3. Close browser tab and reopen
4. Navigate to `http://localhost:5173`

**Expected Results**:
- ✅ User remains logged in after page refresh
- ✅ Redirects to correct role-based dashboard
- ✅ User data is preserved

---

## Test 11: Logout Functionality ✅
**Steps**:
1. Login as any role
2. Click logout button (if available) or clear session
3. Navigate to `http://localhost:5173`

**Expected Results**:
- ✅ User is logged out
- ✅ Redirected to landing page
- ✅ Cannot access protected routes
- ✅ Redirected to `/login` when trying to access protected routes

---

## Test 12: Error Handling ✅
**Test 12.1: Email Already Exists**
**Steps**:
1. Try to register with existing email

**Expected Results**:
- ✅ Error message: "Email already exists"
- ✅ User remains on registration page
- ✅ Form data is preserved

**Test 12.2: Password Mismatch**
**Steps**:
1. Enter different passwords in password and confirm password fields

**Expected Results**:
- ✅ Error message: "Passwords do not match"
- ✅ User remains on registration page

**Test 12.3: Invalid Credentials**
**Steps**:
1. Try to login with wrong password

**Expected Results**:
- ✅ Error message: "Invalid credentials" or "Invalid email or password"
- ✅ User remains on login page

---

## Test 13: API Communication ✅
**Steps**:
1. Open browser DevTools (F12)
2. Go to Network tab
3. Register a new user
4. Check the network requests

**Expected Results**:
- ✅ POST request to `/servesoft/api_auth.php?action=register`
- ✅ Status code: 200 OK
- ✅ Response includes: `{ success: true, user: { id, name, email, role, ... } }`
- ✅ Cookies are set (PHPSESSID)
- ✅ No CORS errors

---

## Test 14: Database Integrity ✅
**Steps**:
1. After each registration, check database

**SQL Queries**:
```sql
-- Check user was created
SELECT * FROM User ORDER BY CreatedAt DESC LIMIT 1;

-- Check role-specific records
SELECT * FROM Customer WHERE UserID = (SELECT UserID FROM User ORDER BY CreatedAt DESC LIMIT 1);
SELECT * FROM Restaurant_Manager WHERE UserID = (SELECT UserID FROM User ORDER BY CreatedAt DESC LIMIT 1);
SELECT * FROM DeliveryAgent WHERE UserID = (SELECT UserID FROM User ORDER BY CreatedAt DESC LIMIT 1);
SELECT * FROM Admin WHERE UserID = (SELECT UserID FROM User ORDER BY CreatedAt DESC LIMIT 1);
```

**Expected Results**:
- ✅ User record exists in User table
- ✅ Corresponding role record exists in appropriate table
- ✅ Password is properly hashed
- ✅ All required fields are populated

---

## Common Issues & Solutions

### Issue: User always redirected to `/customer`
**Cause**: Backend not returning correct role
**Solution**:
- Check `api_auth.php` register endpoint returns `user.role`
- Verify role detection logic in login endpoint

### Issue: CORS errors
**Cause**: CORS headers not set
**Solution**:
- Ensure `cors.php` is included at top of API files
- Check `Access-Control-Allow-Origin` header is present

### Issue: Session not persisting
**Cause**: Cookies not being sent
**Solution**:
- Verify `credentials: 'include'` in fetch calls
- Check browser allows cookies from localhost

### Issue: Protected routes accessible to all
**Cause**: Role check not working in ProtectedRoute
**Solution**:
- Verify `user.role` is correctly stored in AuthContext
- Check route definition in App.tsx

---

## Success Criteria

All tests should pass with:
- ✅ No console errors
- ✅ No network errors
- ✅ Correct role-based routing
- ✅ Database records properly created
- ✅ Sessions properly maintained
- ✅ Protected routes enforced
- ✅ Logout works correctly
- ✅ Login redirects to correct dashboard

---

## Additional Notes

**Testing Multiple Roles**:
- Use different email addresses for each test
- Clear localStorage if needed: `localStorage.clear()`
- Clear PHP session: Logout or delete PHPSESSID cookie

**Browser DevTools**:
- Console: Check for JavaScript errors
- Network: Verify API requests/responses
- Application → Storage → Cookies: Check PHPSESSID
- Application → Storage → Local Storage: Check smartbite_user

**Database Verification**:
Use these queries to verify data:
```sql
-- Count users by role
SELECT
    COUNT(CASE WHEN c.CustomerID IS NOT NULL THEN 1 END) as Customers,
    COUNT(CASE WHEN rm.ManagerID IS NOT NULL THEN 1 END) as Owners,
    COUNT(CASE WHEN da.AgentID IS NOT NULL THEN 1 END) as Agents,
    COUNT(CASE WHEN a.AdminID IS NOT NULL THEN 1 END) as Admins
FROM User u
LEFT JOIN Customer c ON u.UserID = c.UserID
LEFT JOIN Restaurant_Manager rm ON u.UserID = rm.UserID
LEFT JOIN DeliveryAgent da ON u.UserID = da.UserID
LEFT JOIN Admin a ON u.UserID = a.UserID;
```
