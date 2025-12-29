-- ============================================================================
-- DATABASE PRIVILEGE REDUCTION - EXECUTABLE SCRIPT
-- Invoice Web App - Step 4: Principle of Least Privilege
-- ============================================================================
-- This script creates a new application user with minimal required
-- permissions instead of using the root account.
--
-- IMPORTANT: Change 'SecurePassword123!@#' to your own strong password
-- ============================================================================

-- Step 1: Verify we can access MySQL
SELECT 'Starting privilege reduction implementation...' AS status;

-- Step 2: Check current users
SELECT 'Current Database Users:' AS section;
SELECT user, host FROM mysql.user;

-- Step 3: Drop old user if exists (cleanup)
DROP USER IF EXISTS 'invoice_app_secure'@'localhost';

-- Step 4: Create new application user with reduced privileges
-- ⚠️  CHANGE THIS PASSWORD to your own strong password!
CREATE USER 'invoice_app_secure'@'localhost' IDENTIFIED BY 'SecurePassword123!@#';

-- Step 5: Grant ONLY necessary privileges on the pos database
-- Allows: SELECT, INSERT, UPDATE, DELETE
-- Denies: CREATE, ALTER, DROP, GRANT, FILE, SUPER, and all other dangerous privileges
GRANT SELECT, INSERT, UPDATE, DELETE ON `pos`.* TO 'invoice_app_secure'@'localhost';

-- Step 6: Apply changes immediately
FLUSH PRIVILEGES;

-- Step 7: Verify the new user and its permissions
SELECT 'New user created with restricted permissions:' AS section;
SHOW GRANTS FOR 'invoice_app_secure'@'localhost';

-- Step 8: Optional - Create read-only user for reporting purposes
DROP USER IF EXISTS 'invoice_app_readonly'@'localhost';
CREATE USER 'invoice_app_readonly'@'localhost' IDENTIFIED BY 'ReadOnlyPassword456!@#';
GRANT SELECT ON `pos`.* TO 'invoice_app_readonly'@'localhost';
FLUSH PRIVILEGES;

SELECT 'Read-only user created for future use:' AS section;
SHOW GRANTS FOR 'invoice_app_readonly'@'localhost';

-- ============================================================================
-- VERIFICATION AND SUMMARY
-- ============================================================================
SELECT '✓ Privilege reduction completed!' AS success;
SELECT 'All users with their hosts:' AS section;
SELECT user, host FROM mysql.user WHERE user LIKE '%invoice%' OR user = 'root';
