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
**Validated Input Elements:**


**Client-Side Validation:**


**Server-Side Validation:**


**Techniques Used:**


---

### 2️⃣ Authentication
Authentication was enhanced based on **authentication best practices**.

**Methods Implemented:**


**Enhancement Outcome:**


---

### 3️⃣ Authorization
Authorization ensures users only access permitted resources.

**Methods Implemented:**


**Examples:**


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


**Outcome:**


---

### 6️⃣ File Security Principles
To prevent file leaks and unauthorized access:

**Techniques Implemented:**


**Outcome:**


---

# Table of Security and Functionality Enhancements.

This table outlines the before-and-after enhancements made to the **Input Validation**, **Authentication**, **Authorization**, **XSS and CSRF Prevention**, **Database Security**, and **File Security** within the context of a Laravel Filament V3 project.

**1️⃣ Input Validation**

| **Security & Functionality Aspect** | **Before Enhancement** | **After Enhancement** | **Visual Representation** |
|-------------------------------------|------------------------|------------------------|---------------------------|
|             | **Before:**<br>- Input validation function applied<br>- Eloquent path used for input validation<br>- Pages involved: `register.php`, `guest.php`<br><br>**After:**<br>- **Client-Side Validation**: Pop-up message on invalid input<br>- **Server-Side Validation**: Server-level protection (htcss), ensuring better security | **After Enhancement:**<br>Added pop-up messages for client-side validation for better user experience.<br>Enforced stricter server-side validation to protect against unauthorized data manipulation. | ![Input Validation Image](sandbox:/mnt/data/8bdc3764-b89f-4471-9d13-f87bd43ca1b3.png) |

**2️⃣ Authentication** 

| **Security & Functionality Aspect** | **Before Enhancement** | **After Enhancement** | **Visual Representation** |
|-------------------------------------|------------------------|------------------------|---------------------------|
|              | **Before:**<br>- Middleware for session management applied<br>- Login time limit enforced | **After:**<br>- **Session Blocking** for multiple logins per user to prevent session hijacking and unauthorized access. | ![Authentication Image](sandbox:/mnt/data/969460a6-ab49-4129-a7d4-44f3028c7814.png) |

**3️⃣ Authorization**

| **Security & Functionality Aspect** | **Before Enhancement** | **After Enhancement** | **Visual Representation** |
|-------------------------------------|------------------------|------------------------|---------------------------|
|                | **Before:**<br>- **Bcrypt** hashing function for password security | **After:**<br>- **2FA Login** enabled for an additional layer of security during user authentication. | |

 **4️⃣ XSS and CSRF Prevention** 

| **Security & Functionality Aspect** | **Before Enhancement** | **After Enhancement** | **Visual Representation** |
|-------------------------------------|------------------------|------------------------|---------------------------|
|    | **Before:**<br>- CSRF token middleware (`VerifyCsrfToken::class`) implemented<br>- Session cookies encrypted (`EncryptCookies::class`) | **After:**<br>- **Strict Validation Rules** applied for both CSRF and XSS prevention to ensure secure form submissions. | |

**5️⃣ Database Security Principles**

| **Security & Functionality Aspect** | **Before Enhancement** | **After Enhancement** | **Visual Representation** |
|-------------------------------------|------------------------|------------------------|---------------------------|
| | **Before:**<br>- **Eloquent ORM** used for all database queries<br>- No raw DB queries were used | **After:**<br>- **Privilege-Restricted Databases** to limit database access rights<br>- **Debug Mode Disabled** to avoid information leaks during development | |

**6️⃣ File Security Principles** 

| **Security & Functionality Aspect** | **Before Enhancement** | **After Enhancement** | **Visual Representation** |
|-------------------------------------|------------------------|------------------------|---------------------------|
|    | **Before:**<br>- **IDOR** (Insecure Direct Object References) and RBAC (Role-Based Access Control) applied for file security<br>- File encryption and access control were implemented<br>- File type validation added | **After:**<br>- **Isolated URL Links** for improved file access security<br>- **Client-Side Protection** for additional measures against unauthorized file access<br>- **Privilege-Restricted Security Layers** for a deeper level of protection | |

---

## Code Snippets Enhancements for 6 Main Parts (i - vi)

### 1️⃣ Input Validation
```php
// Before: Input validation function
$user = $this->wrapInDatabaseTransaction(function () {
    $this->callHook('beforeValidate');
    $data = $this->form->getState();
    $this->callHook('afterValidate');
});

// After: Enhanced validation with server-side protection
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





## 📁 Project Repository Structure

