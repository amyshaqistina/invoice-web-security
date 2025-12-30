# 🔐 Web Application Security Enhancement Project
**Course:** INFO 4345 – Web Application Security

**Instructor:** Asst. Prof. Dr. Najhan Bin Muhamad Ibrahim

---

## 👥 Group Members
| Name | Matric Number |
|------|---------------|
| NABILAH BINTI AHMAD NORDIN | 2225498 |
| AMYSHA QISTINA BINTI AMEROLAZUAM | 2225998  |
| NURALYA MEDINA BINTI MOHAMMAD NIZAM | 2211788 |
| ALIN FARHAIN BINTI ABDUL RAJAT @ ABDUL RAZAK | 2224210 |


---

## 📌 Web Application Title
**Invoice Sensei Web Application- Security Enhancement**

---

## 📝 Introduction
This project focuses on enhancing the security of a previously developed web application from the **Web Application Development (INFO 3305)** course.

Web application security is critical to ensure the confidentiality, integrity, and availability of user data and system resources. Common threats such as **SQL Injection, Cross-Site Scripting (XSS), Cross-Site Request Forgery (CSRF), weak authentication, and improper authorization** can compromise web applications if not properly mitigated.

Therefore, this project implements multiple **web application security best practices** to harden the original application and protect it from common web-based attacks.

project utilizes **Laravel Filament** for building the admin panel and enhancing the user experience with an intuitive interface. Laravel Filament is an open-source admin panel for Laravel applications, designed to handle administration tasks with ease


FOLLOW THIS STEPS: 

1. make sure xammp + mysql start
3. run kat terminal vs code 👇🏻
4. Composer update
5. php artisan filament:upgrade
6. npm update
6. npm run dev (if cannot , cntrl c) 
7. make sure .env ada 
8. dlm .env
- tukar APP_URL : http/localhost to http://127.0.0.1:8000/
 
8. php artisan storage:link
9. php artisan key:generate
9. php artisan migrate:fresh --seed
10. php artisan config:clear
11. php artisan route:clear
12. php artisan view:clear
13. php artisan cache:clear
14. php artisan serve
15. run php artisan optimize:clear, run php artisan icon: cache 
16. default log in email👇🏻
- email => superadmin@test.com
- password => superadmin1234

---

## 🎯 Objectives of Security Enhancements
The objectives of this project are to:
- Improve input handling through proper **client-side and server-side validation**
- Strengthen **authentication mechanisms** to prevent unauthorized access
- Enforce **role-based authorization** for secure access control
- Prevent **XSS and CSRF attacks**
- Secure database interactions to prevent **SQL Injection**
- Protect server and application files from **file leaks**
- Align the application with **web application security best practices**

---

## 🔐 Web Application Security Enhancements

### 1️⃣ Input Validation

Validated Input Elements:
- Full Name  
- Email Address (restricted to Gmail accounts)  
- Password  
- Password Confirmation  
- Team Slug (URL identifier)  
- Password Confirmation for Bulk Delete Action  
---

Client-Side Validation:
- Implemented using Livewire real-time feedback (live(onBlur: true))
- Provides instant notifications when:
  - Name length is too short or exceeds limits  
  - Email is not a valid Gmail address  
  - Password does not meet strength requirements  
  - Password and confirmation do not match  
- Utilizes Filament Notification for immediate user guidance  
---
Server-Side Validation:
- Enforced during form submission and acts as the final authority
- Includes:
  - Required field enforcement (required)  
  - Length constraints (minLength, maxLength)  
  - Format validation using regular expressions (regex)  
  - Email uniqueness checks against the database (unique)  
  - Strong password enforcement (uppercase, lowercase, numeric, special characters)  
  - Secure password verification for bulk deletion using Hash::check  
  - Slug uniqueness enforcement for team URLs  
---

Techniques Used:
- Laravel validation rules (required, minLength, maxLength, regex, unique)  
- Livewire real-time validation (live(onBlur: true))  
- Custom validation closures for sensitive operations  
- Password hashing and verification using Laravel’s Hash facade  
- Confirmation modals for destructive actions  
- Rate limiting to prevent automated abuse
- Eloquent (already implemented before these security enhancements)
- ->Auth (already implemented before these security enhancements)
- Required() (already implemented before these security enhancements)


---

### 2️⃣ Authentication

**Methods Implemented:**
- Bcrypt Password hashing
- Enforce strong password policy (minimum length & complexity)
- Implement account lock after multiple failed attempt
- Sessions data tracking (login/logout time & last_activity time)
- Session timeout after inactivity
- Audit trail (store activity log)
- step-up verification





---

### 3️⃣ Authorization
Authorization ensures users only access permitted resources.

**Methods Implemented:**
- Re-check authorization before sensitive actions (TOCTTOU prevention)
- Admin dashboard manage users
- Insecure Direct Object References (IDOR)



---

### 4️⃣ XSS and CSRF Prevention

#### Cross-Site Scripting (XSS) Prevention
**Techniques Used:**


#### Cross-Site Request Forgery (CSRF) Prevention
**Techniques Used:**


---

### 5️⃣ Database Security Principles
To prevent **SQL Injection**, the following enhancements were implemented:

**Methods Used:**
- Privileges Restricted
- Elequont (already implemented before these security enhancements)

**Outcome:**
- Exclusive User can update, delete, and access the database (admin only)

---

### 6️⃣ File Security Principles
To prevent file leaks and unauthorised access:

**Techniques Implemented:**
- Isolation URL link
- Spatie Laravel Permission RBAC ->CRUD ((already implemented before these security enhancements)

**Outcome:**
- Url link path cannot be seen(Hidden)

---

# List of Files Modification Model, View, Controller, and Enhancement Techniques

| **Model**                               | **View**                                 | **Controller**                         | **Enhancement Technique**                                                       |
|-----------------------------------------|------------------------------------------|----------------------------------------|--------------------------------------------------------------------------------|
| - `RecentInvoiceTables.php`<br>- `Overview.php` | - `dashboard.blade.php`<br>- `overview.blade.php` | - `dashboard.php`<br>- `Statsoverview.php` | **Isolation** |
| - `RecentInvoiceTables.php`            | - `recentInvoices.blade.php`             | - `RecentInvoiceTable.php`             | **Isolation** |
| - `RecurringInvoiceTable.php`          | - `recurringInvoices.blade.php`          | - `RecurringInvoiceTable.php`          | **Isolation** |
| - `RecentPaymentTable.php`             | - `recentPayments.blade.php`             | - `RecentPaymentTable.php`             | **Isolation** |
| - `Customer.php`            | - `.blade.php`             | - `CustomerResource.php`             | **Isolation** |
| - `2fa.php`                            | - `2fa.blade.php`                        | - `SendOtp.php`<br>- `AdminPanelProvider.php` | **2FA** |
| - —                      | - Delete Confirmation Modal      | - `PasswordProtectedDeleteAction.php`         | **Password Confirmation (Delete)**      |
| - —                      | - Bulk Delete Confirmation Modal | - `PasswordProtectedDeleteBulkAction.php`     | **Password Confirmation (Bulk Delete)** |
| - —                      | - Export Confirmation Modal      | - `PasswordProtectedExportAction.php`         | **Password Confirmation (Export)**      |
| - —                      | - —                              | - `RequiresPasswordConfirmation.php`          | **Reusable Security Control**           |
| - `User.php`             | - `UserResource.php`             | - `UserResource.php`             | **Password-Protected Delete**               |
| - `Customer.php`         | - `CustomerResource.php`         | - `CustomerResource.php`         | **Password-Protected Delete & Export**      |
| - `Invoice.php`          | - `InvoiceResource.php`          | - `InvoiceResource.php`          | **Password-Protected Bulk Delete**          |
| - `Payment.php`          | - `PaymentResource.php`          | - `PaymentResource.php`          | **Password-Protected Bulk Delete**          |
| - `RecurringInvoice.php` | - `RecurringInvoiceResource.php` | - `RecurringInvoiceResource.php` | **Password-Protected Bulk Delete**          |
| - `Item.php`             | - `ItemResource.php`             | - `ItemResource.php`             | **Password-Protected Delete & Bulk Delete** |





# Table of Security and Functionality Enhancements.

This table outlines the before-and-after enhancements made to the **Input Validation**, **Authentication**, **Authorization**, **XSS and CSRF Prevention**, **Database Security**, and **File Security** within the context of a Laravel Filament V3 project.

**1️⃣ Input Validation**

| **Security & Functionality Aspect** | **Before Enhancement** | **After Enhancement** | **Visual Representation** |
|-------------------------------------|------------------------|------------------------|---------------------------|
|             | **Before:**<br>- Input validation function applied<br>- Eloquent path used for input validation<br>- Pages involved: `register.php`, `guest.php`<br><br>**After:**<br>- **Client-Side Validation**: Pop-up message on invalid input<br>- **Server-Side Validation**: Server-level protection (htcss), ensuring better security | **After Enhancement:**<br>Added pop-up messages for client-side validation for better user experience.<br>Enforced stricter server-side validation to protect against unauthorized data manipulation. <br> <img width="1401" height="836" alt="image" src="https://github.com/user-attachments/assets/66c952de-1825-4d4e-b092-9a2fb8b5e287" /> <br> <img width="625" height="208" alt="image" src="https://github.com/user-attachments/assets/fa14c659-2c8f-4761-8fb6-14c27d46cf19" />| <img width="1914" height="608" alt="Screenshot 2025-12-28 215253" src="https://github.com/user-attachments/assets/012cadac-4483-4654-a9c8-bbc7be47a1da" /> <br> <img width="1907" height="604" alt="Screenshot 2025-12-28 215314" src="https://github.com/user-attachments/assets/d8ddb6ac-e5e5-43e7-b863-872b025ba870" /> <br> <img width="1917" height="1002" alt="image" src="https://github.com/user-attachments/assets/a486c4ea-8e96-4069-a7ed-173b7efbf810" />|

**2️⃣ Authentication** 

| **Security & Functionality Aspect** | **Before Enhancement** | **After Enhancement** | **Visual Representation** |
|-------------------------------------|------------------------|------------------------|---------------------------|
| Bcrypt Password hashing | - Passwords were stored using plaintext <br>- vulnerable to brute‑force or rainbow table attacks. | - All passwords are now hashed using Bcrypt <br> <img width="728" height="591" alt="image" src="https://github.com/user-attachments/assets/e068c620-ef19-40cd-b3b2-a24f14e2fc7e" /> <br> <img width="542" height="203" alt="image" src="https://github.com/user-attachments/assets/d53933cd-b575-4fdc-8214-7fb193c5618d" /> | <img width="1585" height="285" alt="image" src="https://github.com/user-attachments/assets/98e47375-b423-489c-9916-f89e7db3dadf" /> |
| Enforce strong password policy (minimum length & complexity) | - Users could set simple or short passwords | - Enforced minimum length (8 characters) <br> - complexity requirements (uppercase, lowercase, numbers, special characters)<br> <img width="921" height="668" alt="image" src="https://github.com/user-attachments/assets/416d92c4-4c98-48ad-ad9d-9a54867d91e2" /> | <img width="1919" height="963" alt="image" src="https://github.com/user-attachments/assets/cb9d778a-bd56-4e13-bd40-8d52b362cdff" /> |
| Implement account lock after multiple failed attempt | - No mechanism to lock accounts after repeated failed attempts| - Accounts are automatically locked after 5 multiple failed login attempts (15 seconds)<br> <img width="836" height="380" alt="image" src="https://github.com/user-attachments/assets/5c561399-6512-4c4e-803c-7997335d520e" /> | <img width="1919" height="965" alt="image" src="https://github.com/user-attachments/assets/8a3e798f-c72f-40b6-af3d-14b211627956" /> |
| Sessions data tracking (login/logout time & last_activity time) | - Limited visibility into user sessions; login/logout events were not consistently recorded in the database. | - Each login and logout is recorded in the sessions table with timestamps (login_time, logout_time, last_activity)<br> <img width="754" height="397" alt="image" src="https://github.com/user-attachments/assets/fdf8e515-d277-42c5-b01f-06e46b07c9c6" /> <br> <img width="722" height="336" alt="image" src="https://github.com/user-attachments/assets/65166040-1a53-417f-a544-2caa17b956ad" /> | <img width="1583" height="240" alt="image" src="https://github.com/user-attachments/assets/7cd57ab6-11cb-4dcf-9699-796e55df9f33" />|
| Session timeout after inactivity | - Sessions remained active indefinitely unless manually logged out| - Inactive sessions automatically expire after 2 hours <br> <img width="226" height="149" alt="image" src="https://github.com/user-attachments/assets/e6082c5b-ec40-44ef-86f7-054efe4adff6" /> <br> <img width="447" height="64" alt="image" src="https://github.com/user-attachments/assets/e956757a-4bc5-4d15-80db-e417af72e515" /> |  |
| Audit trail (store activity log) | - No structured activity logging, making it difficult to trace user actions or investigate suspicious behavior. | - Comprehensive activity logs are now stored and displayed in maintenance page| <img width="1919" height="968" alt="image" src="https://github.com/user-attachments/assets/97d7dd18-32c5-4808-9c2c-41555946462c" /> <br> <img width="1581" height="469" alt="image" src="https://github.com/user-attachments/assets/2e2f7a69-4c4b-443e-9eed-27bee8489625" /> |

**3️⃣ Authorization**

| **Security & Functionality Aspect**                                  | **Before Enhancement**                                                                                                                 | **After Enhancement**                                                                                                                                                                                | **Visual Representation**                                                 |
| -------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------- |
| Step-Up Verification | **Before:**<br>- Sensitive actions (delete and export) could be performed immediately without re-authentication<br>- No additional verification layer for critical operations<br>- Security risk if user session left unattended | **After:**<br>- **Password Re-authentication Required** for all delete actions (individual & bulk) across all resources<br>-**30-Minute Session Timeout** - password confirmation valid for 30 minutes to balance security and usability<br>- **Modal-Based Verification** for better user experience<br>- Protection covers: Users, Customers, Invoices, Payments, Recurring Invoices, and Items <br> <img width="1401" height="836" alt="Screenshot 2025-12-29 092806" src="https://github.com/user-attachments/assets/45ece107-ccec-4dca-ba4b-77a2fe3bc443" /> <br> <img width="1368" height="619" alt="Screenshot 2025-12-29 015416" src="https://github.com/user-attachments/assets/7549fbd8-fab6-4cf8-a277-00be8c00ad11" /> <br> <img width="1030" height="288" alt="Screenshot 2025-12-29 015424" src="https://github.com/user-attachments/assets/d0fb38a9-6abf-47ef-a015-2cbd049edd5e" /> <br> <img width="625" height="208" alt="Screenshot 2025-12-29 093001" src="https://github.com/user-attachments/assets/b3f02f43-f00c-4ed7-83e0-c973d43fcaf7" />| <img width="1919" height="1011" alt="image" src="https://github.com/user-attachments/assets/dfe4a461-abbc-43b8-9585-6da5c6e3b379" /> <br> <img width="1919" height="1005" alt="image" src="https://github.com/user-attachments/assets/ef32bb7b-384e-4eb7-a43c-4b2a6ea46e06" />|
| (RBAC)Admin dashboard manage users                                         | Admin dashboard allowed access to user management without fine-grained role checks; any authenticated admin could perform all actions. | Role-based access control (RBAC) applied. Only users with `super-admin` role can delete users or change roles; other admins have limited permissions (view/edit). Policies define access per action. | <img width="1516" height="768" alt="image" src="https://github.com/user-attachments/assets/4bac487a-dd17-479a-ac73-0f1df918dcfa" /> |
| Insecure Direct Object References (IDOR)                             | URLs used numeric IDs (`/user/5/edit`) without validation. Any logged-in user could manipulate IDs to access other users’ data.        | Policies and route model binding enforce that users can only access resources they own or are permitted to. For example, `/user/{id}/edit` checks authorization before serving the page.             | |


 **4️⃣ XSS and CSRF Prevention** 

| **Security & Functionality Aspect** | **Before Enhancement** | **After Enhancement** | **Visual Representation** |
|-------------------------------------|------------------------|------------------------|---------------------------|
|  Core XSS Escaping Method  | **Before:**<br>- No escaping - raw user data displayed directly (`echo $userInput;`) implemented | **After:**<br>- **Automatic HTML entity conversion** applied (`return htmlspecialchars((string) $text, ENT_QUOTES ENT_HTML5, 'UTF-8', false);`) 
|  Table Column Output Escaping  | **Before:**<br>- Table columns show raw data (`Tables\Columns\TextColumn::make('name')->label('Customer Name')`) implemented | **After:**<br>- **Auto-escaping for display and tooltips** applied <img width="860" height="325" alt="Screenshot 2025-12-28 085814" src="https://github.com/user-attachments/assets/ff793d82-34a6-4d08-85a7-c3fa72ecf2e1" />
|  Notification Message Escaping  | **Before:**<br>- User data in notifications causes XSS (`Notification::make()->body("Issues found: $userInput")`) implemented | **After:**<br>- **All notification text escaped** applied <img width="636" height="224" alt="Screenshot 2025-12-28 085911" src="https://github.com/user-attachments/assets/2a1ab048-0e60-4d45-9d8e-6d482cd3f657" />
 |  Form Input Sanitization  | **Before:**<br>- Accepts any input, cleans only server-side (`Forms\Components\TextInput::make('name')->label('Customer Name')`) implemented | **After:**<br>- **Real-time client-side filtering + server validation** applied <img width="998" height="407" alt="Screenshot 2025-12-28 090004" src="https://github.com/user-attachments/assets/adeec43c-cb6e-4ff2-bf03-f68602e7ac45" />
 |   Filename Sanitization for XSS  | **Before:**<br>- Raw filenames used for downloads (`$filename = "invoice_$customerName.pdf";`) implemented | **After:**<br>- **Multiple sanitization layers** applied <img width="895" height="262" alt="Screenshot 2025-12-28 090055" src="https://github.com/user-attachments/assets/0d1a96c8-b430-4e60-9622-8c996be91437" />
|   Token-Based URL Generation  | **Before:**<br>- URLs without token verification (`Tables\Actions\Action::make('Print PDF')->url(fn ($record) => route('print.invoice', ['id' => $record->id]))`) implemented | **After:**<br>- **Unique session-based tokens** applied <img width="605" height="225" alt="Screenshot 2025-12-28 090215" src="https://github.com/user-attachments/assets/97db5aac-ef9b-4ba5-adfb-f0245332eac4" />


**5️⃣ Database Security Principles**

| **Security & Functionality Aspect** | **Before Enhancement** | **After Enhancement** | **Visual Representation** |
|-------------------------------------|------------------------|------------------------|---------------------------|
| | **Before:**<br>- **Eloquent ORM** used for all database queries<br>- No raw DB queries were used | **After:**<br>- **Privilege-Restricted Databases** to limit database access rights<br>- **Debug Mode Disabled** to avoid information leaks during development | <img width="305" height="177" alt="image" src="https://github.com/user-attachments/assets/406f2a4a-4de7-4119-850d-4ab3f6268c71" > <img width="619" height="256" alt="image" src="https://github.com/user-attachments/assets/81cb231d-57bd-4a4a-9fce-7b39815d4be2" />
 

**6️⃣ File Security Principles** 

| **Security & Functionality Aspect**                | **Before Enhancement**                                                                                         | **After Enhancement**                                                                                                      | **Visual Representation**                                                                                                        |
|----------------------------------------------------|-----------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------|
| **IDOR & RBAC Applied for File Security**         | **Before:**<br>- **IDOR** (Insecure Direct Object References) and **RBAC** (Role-Based Access Control) applied for file security | **After:**<br>- **Isolated URL Links** for improved file access security<br>**Explanation:**<br>Pages involved: `Dashboard.php`, `Dashboard.blade.php` <img width="975" height="855" alt="image" src="https://github.com/user-attachments/assets/2d6bc9cd-3306-4352-aa7c-b7b129e6725e" /> | **Before:** ![image](https://github.com/user-attachments/assets/649d2fb9-1993-48cd-afd2-71f150ad8714) <br> <img width="975" height="390" alt="image" src="https://github.com/user-attachments/assets/52197d12-37ac-41dc-8aad-43defe09f4aa" /> <br> **After:** <img width="975" height="457" alt="image" src="https://github.com/user-attachments/assets/bb01a24e-729d-4931-b7aa-f0f597808e04" /> |
| **File Encryption and Access Control**            | **Before:**<br>- **File encryption** and **access control** were implemented                                       | **After:**<br>- **Client-Side Protection** for additional measures against unauthorized file access<br>- **Privilege-Restricted Security Layers** for a deeper level of protection | <img width="960" height="540" alt="image" src="https://github.com/user-attachments/assets/31509717-0ad4-427a-9ffb-b9d1f9d853b5" /> |
| **File Type Validation Added**                     | **Before:**<br>- **File type validation** added                                                                    | **After:**<br>- **Privilege-Restricted Security Layers** for a deeper level of protection<br>**Changes Involved:**<br>Editing `.env` file | <img width="305" height="177" alt="image" src="https://github.com/user-attachments/assets/406f2a4a-4de7-4119-850d-4ab3f6268c71" />
                                        |

**7️⃣ Additional Enhancements Implementation** 

| **Feature**                | **Before Enhancement**                                                                                         | **After Enhancement**                                                                                                      | **Visual Representation**                                                                                                        |
|----------------------------|-----------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------|
| **2FA Implementation**      | **Before:**<br>- Two-factor authentication (2FA) not implemented.                                                   | **After:**<br>- 2FA added via **SendOTP.php**, **.env file**, **admin panel profile.php**, and **User.php**.<br>**Explanation:**<br>Added OTP-based authentication for improved security. <img width="482" height="65" alt="image" src="https://github.com/user-attachments/assets/b697d776-1986-4e28-98bf-531c7db8a262" /> | <img width="941" height="443" alt="image" src="https://github.com/user-attachments/assets/da73f53d-fc3d-4a5b-a488-f1d67e332e5a" /> for sign up <img width="937" height="440" alt="image" src="https://github.com/user-attachments/assets/ff4e40e9-66dd-4386-a31d-e2ad49f81d09" /> |
| **Audit Trail for User**    | **Before:**<br>- No logging for user actions.                                                                 | **After:**<br>- Implemented **Audit Trail** to track user activity for security and compliance purposes.<br>**Explanation:**<br>Records each user action for transparency and accountability. | <img width="1600" height="658" alt="image" src="https://github.com/user-attachments/assets/f36d4264-361e-48f2-a573-ac80f9794bfe" /> <img width="855" height="347" alt="image" src="https://github.com/user-attachments/assets/09acfa64-7d72-495a-aa49-0724ddd72311" /> |

---

## Highlighted Code Snippets Enhancements for 6 Main Parts (i - vi)

### 1️⃣ Input Validation
```php
// Before: Input validation function
$user = $this->wrapInDatabaseTransaction(function () {
    $this->callHook('beforeValidate');
    $data = $this->form->getState();
    $this->callHook('afterValidate');
});
// After: Enhanced validation client and server-side protection
return TextInput::make('name')
            ->label('Full Name')
            ->required()
            ->minLength(2)
            ->maxLength(255)
            ->live(onBlur: true)
            
$this->form->validate([
    'email' => ['required', 'email'],
    'password' => ['required', 'min:8']
]);


### 2️⃣ Authentication
```php

// Before: Middleware for session management
->middleware([
    'auth',
    'throttle:login'
]);

// After: Session blocking for multiple logins
->middleware([
    'auth',
    'preventMultipleLogins'
]);

3️⃣ Authorization (2FA Implementation)
```php
// After: Enabling 2FA login
public function login(Request $request)
{
    $this->validate($request, [
        'email' => 'required|email',
        'password' => 'required',
        'two_factor_code' => 'required|numeric'
    ]);

    if ($this->isValidTwoFactorCode($request->two_factor_code)) {
        Auth::login($user);
    }
}

//Filament2fa.php

 'options' => [
        TwoFactorType::authenticator,
        TwoFactorType::email,
        TwoFactorType::phone,
    ],

 'sms_service' => null, // For example 'vonage', 'twilio', 'nexmo', etc.
    'send_otp_class' => App\Notifications\SendOTP::class,
    'phone_number_field' => 'phone',

4️⃣ CSRF Prevention
```php
// Before: CSRF token middleware
->middleware([VerifyCsrfToken::class]);

// After: Applying strict validation rules
$this->validate($request, [
    'csrf_token' => 'required|csrf_token'
]);


5️⃣ Database Security
```php
// Before: Using Eloquent ORM
User::where('email', $request->email)->first();

// After: Privilege-restricted database queries
User::where('email', $request->email)
    ->where('role', 'admin')
    ->first();


6️⃣ File Security 
```php
// Before: File validation
public function storeFile(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:jpg,png,pdf|max:10240'
    ]);

    $file = $request->file('file');
    $file->storeAs('uploads', $file->getClientOriginalName());
}

// After: Isolated file URL links
$path = Storage::disk('s3')->url('uploads/' . $file->getClientOriginalName());

// Another implimentation isolation url link for Dashboard page

    <!-- Dynamically Render Content Based on the Selected Section -->
    <div class="content">
        @if($pageurl == 'overview')
            <div class="page-content">
                <h2 class="text-2xl font-semibold">Overview Chart</h2>
                <!-- Include the Overview Chart widget -->
                @livewire(\App\Filament\Widgets\StatsOverview::class)
            </div>
        @elseif($pageurl == 'invoices')
            <div class="page-content">
                <h2 class="text-2xl font-semibold">Recent Invoices</h2>
                <!-- Include the Recent Invoices Table widget -->
                 @livewire(\App\Filament\Widgets\RecentInvoicesTable::class)
            </div>
        @elseif($pageurl == 'payments')
            <div class="page-content">
                <h2 class="text-2xl font-semibold">Recent Payments</h2>
                <!-- Include the Recent Payments Table widget -->
                 @livewire(\App\Filament\Widgets\RecentPaymentTable::class)
            </div>
        @elseif($pageurl == 'recurring-invoices')
            <div class="page-content">
                <h2 class="text-2xl font-semibold">Recurring Invoices</h2>
                <!-- Include the Recurring Invoices Table widget -->
                @livewire(\App\Filament\Widgets\RecurringInvoiceTable::class)
            </div>
        @else
            <div class="page-content">
                <h2 class="text-2xl font-semibold">No Page Selected</h2>
                <p>Please select a page to display the content.</p>
            </div>
        @endif
    </div>




## 📁 Project Repository Structure

>>>>>>> acaab02171d2f328a4c284b7558708824aaabc27
