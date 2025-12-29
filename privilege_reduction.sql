-- ============================================================================
-- DATABASE PRIVILEGE REDUCTION SCRIPT
-- Invoice Web App - Step 4: Principle of Least Privilege
-- ============================================================================
-- This script implements database account privilege reduction following
-- the principle of least privilege. It creates a new application user
-- with minimal required permissions instead of using the root account.
-- ============================================================================

-- SAFETY CHECK: Ensure we're on the correct database
-- Comment this out if you want to run the script without warnings
SELECT '⚠️  IMPORTANT: Ensure you are using the correct MySQL/MariaDB instance' AS WARNING;
SELECT 'Application Database: pos' AS TARGET_DATABASE;
SELECT '-------------------------------------------' AS SEPARATOR;

-- ============================================================================
-- PART 1: AUDIT - View Current Permissions
-- ============================================================================
SECTION_1_AUDIT:
BEGIN
  SELECT '=== AUDIT: Current Database Users ===' AS SECTION;
  SELECT user, host FROM mysql.user WHERE user LIKE '%root%' OR user LIKE '%invoice%';

  SELECT '=== AUDIT: Current invoice_app Permissions ===' AS AUDIT;
  SHOW GRANTS FOR 'invoice_app'@'localhost';
END//

-- ============================================================================
-- PART 2: CREATE NEW APPLICATION USER WITH REDUCED PRIVILEGES
-- ============================================================================
SECTION_2_CREATE_USER:
BEGIN
  SELECT '=== Creating invoice_app_secure User ===' AS SECTION;

  -- Check if user already exists (won't error even if exists)
  -- First, we'll try to drop and recreate
  DROP USER IF EXISTS 'invoice_app_secure'@'localhost';

  -- Create new user with strong password
  -- IMPORTANT: Change 'SecurePassword123!@#' to your own strong password
  -- Password requirements: Min 12 chars, uppercase, lowercase, numbers, special chars
  CREATE USER 'invoice_app_secure'@'localhost' IDENTIFIED BY 'SecurePassword123!@#';

  SELECT '✓ User invoice_app_secure@localhost created successfully' AS STATUS;
END//

-- ============================================================================
-- PART 3: GRANT MINIMAL NECESSARY PRIVILEGES
-- ============================================================================
SECTION_3_GRANT_PRIVILEGES:
BEGIN
  SELECT '=== Granting Minimal Privileges ===' AS SECTION;

  -- Grant CRUD permissions on pos database only
  GRANT SELECT, INSERT, UPDATE, DELETE ON `pos`.* TO 'invoice_app_secure'@'localhost';

  -- Explicitly DENY dangerous privileges
  -- Note: MySQL/MariaDB doesn't have DENY, so we just don't grant them

  FLUSH PRIVILEGES;

  SELECT '✓ Privileges granted: SELECT, INSERT, UPDATE, DELETE on pos.*' AS GRANTED;
  SELECT '✓ Dangerous privileges NOT granted: CREATE, ALTER, DROP, GRANT, SUPER, FILE' AS DENIED;

END//

-- ============================================================================
-- PART 4: VERIFICATION - Show New Privileges
-- ============================================================================
SECTION_4_VERIFY:
BEGIN
  SELECT '=== VERIFICATION: New User Privileges ===' AS SECTION;

  SHOW GRANTS FOR 'invoice_app_secure'@'localhost';

  SELECT '-------------------------------------------' AS SEPARATOR;
  SELECT '✓ Privilege reduction completed successfully!' AS SUCCESS;
  SELECT '✓ User can now ONLY: SELECT, INSERT, UPDATE, DELETE' AS ALLOWED_OPERATIONS;
  SELECT '✓ User CANNOT: CREATE, ALTER, DROP, GRANT, or access other databases' AS BLOCKED_OPERATIONS;

END//

-- ============================================================================
-- PART 5: OPTIONAL - Create Read-Only User (for future reporting)
-- ============================================================================
SECTION_5_READONLY_USER:
BEGIN
  SELECT '=== OPTIONAL: Creating Read-Only User ===' AS SECTION;

  -- Drop if exists
  DROP USER IF EXISTS 'invoice_app_readonly'@'localhost';

  -- Create read-only user
  CREATE USER 'invoice_app_readonly'@'localhost' IDENTIFIED BY 'ReadOnlyPassword456!@#';

  -- Grant only SELECT
  GRANT SELECT ON `pos`.* TO 'invoice_app_readonly'@'localhost';

  FLUSH PRIVILEGES;

  SELECT '✓ Read-only user created for future use' AS STATUS;
  SELECT '✓ This user can only: SELECT (view data)' AS CAPABILITIES;
  SELECT '✓ Use for reporting dashboards, analytics, exports' AS USE_CASE;

END//

-- ============================================================================
-- PART 6: SUMMARY COMPARISON
-- ============================================================================
SECTION_6_SUMMARY:
BEGIN
  SELECT '=== SECURITY SUMMARY ===' AS SECTION;
  SELECT '-------------------------------------------' AS SEPARATOR;

  SELECT 'OLD CONFIGURATION (UNSAFE)' AS CONFIG_TYPE;
  SELECT 'User: root@localhost' AS USER_ACCOUNT;
  SELECT 'Access: ALL DATABASES, ALL PRIVILEGES' AS ACCESS_LEVEL;
  SELECT 'Risk: CRITICAL - Full server compromise if credentials stolen' AS RISK_LEVEL;

  UNION ALL

  SELECT 'NEW CONFIGURATION (SECURE)' AS CONFIG_TYPE;
  SELECT 'User: invoice_app_secure@localhost' AS USER_ACCOUNT;
  SELECT 'Access: pos database, SELECT/INSERT/UPDATE/DELETE only' AS ACCESS_LEVEL;
  SELECT 'Risk: LOW - Impact limited to pos database if credentials stolen' AS RISK_LEVEL;

END//

-- ============================================================================
-- FINAL CHECKLIST
-- ============================================================================
SECTION_7_CHECKLIST:
BEGIN
  SELECT '' AS EMPTY;
  SELECT '╔════════════════════════════════════════════════════════════╗' AS CHECKLIST;
  SELECT '║ NEXT STEPS - UPDATE APPLICATION CONFIGURATION            ║' AS CHECKLIST_2;
  SELECT '╚════════════════════════════════════════════════════════════╝' AS CHECKLIST_3;

  SELECT '1. Edit .env file in application root:' AS STEP1;
  SELECT '   Change: DB_USERNAME=root' AS STEP1_DETAIL1;
  SELECT '   To:     DB_USERNAME=invoice_app_secure' AS STEP1_DETAIL2;

  SELECT '2. Update database password:' AS STEP2;
  SELECT '   Change: DB_PASSWORD=' AS STEP2_DETAIL1;
  SELECT '   To:     DB_PASSWORD=SecurePassword123!@#' AS STEP2_DETAIL2;

  SELECT '3. Test application connection:' AS STEP3;
  SELECT '   Run: php artisan migrate:status' AS STEP3_DETAIL1;
  SELECT '   Or:  php artisan tinker' AS STEP3_DETAIL2;

  SELECT '4. Run application test suite:' AS STEP4;
  SELECT '   Run: php artisan test' AS STEP4_DETAIL;

  SELECT '5. Verify all features work:' AS STEP5;
  SELECT '   - Create invoice' AS STEP5_DETAIL1;
  SELECT '   - Update customer' AS STEP5_DETAIL2;
  SELECT '   - Delete estimate' AS STEP5_DETAIL3;
  SELECT '   - View reports' AS STEP5_DETAIL4;

  SELECT '6. IMPORTANT: Use strong password!' AS STEP6;
  SELECT '   - Min 12 characters' AS STEP6_DETAIL1;
  SELECT '   - Mix: uppercase, lowercase, numbers, special chars' AS STEP6_DETAIL2;
  SELECT '   - Store in password manager' AS STEP6_DETAIL3;

  SELECT '' AS EMPTY2;
  SELECT '✓ Security hardening Step 4 completed!' AS COMPLETION;

END//

-- ============================================================================
-- TROUBLESHOOTING
-- ============================================================================
-- If application can't connect after changes:
-- 1. Verify password matches .env file
-- 2. Check user exists: SELECT USER FROM mysql.user WHERE user='invoice_app_secure';
-- 3. Check grants: SHOW GRANTS FOR 'invoice_app_secure'@'localhost';
-- 4. Test connection: mysql -u invoice_app_secure -p -e "USE pos; SHOW TABLES;"
-- 5. Review MySQL error logs: tail -f /var/log/mysql/error.log
-- ============================================================================

-- ============================================================================
-- EXECUTION NOTES
-- ============================================================================
-- This script uses multiple SELECTs for user feedback instead of
-- stored procedures for maximum compatibility with all MySQL/MariaDB versions.
--
-- To execute in batch mode:
-- mysql -u root < privilege_reduction.sql
--
-- To execute interactively:
-- mysql -u root
-- SOURCE privilege_reduction.sql;
-- ============================================================================
