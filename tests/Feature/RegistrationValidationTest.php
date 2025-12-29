<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that registration rejects non-Gmail email addresses
     */
    public function test_registration_rejects_non_gmail_email()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@yahoo.com',
            'password' => 'SecurePass123!',
            'passwordConfirmation' => 'SecurePass123!',
        ]);

        $this->assertNull(User::where('email', 'test@yahoo.com')->first());
    }

    /**
     * Test that registration accepts valid Gmail addresses
     */
    public function test_registration_accepts_valid_gmail()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'testuser@gmail.com',
            'password' => 'SecurePass123!',
            'passwordConfirmation' => 'SecurePass123!',
        ]);

        $user = User::where('email', 'testuser@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user->name);
    }

    /**
     * Test that registration rejects short passwords
     */
    public function test_registration_rejects_short_password()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'Pass1!',  // Only 6 characters
            'passwordConfirmation' => 'Pass1!',
        ]);

        $this->assertNull(User::where('email', 'test@gmail.com')->first());
    }

    /**
     * Test that registration rejects passwords without uppercase
     */
    public function test_registration_rejects_password_without_uppercase()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'securepass123!',
            'passwordConfirmation' => 'securepass123!',
        ]);

        $this->assertNull(User::where('email', 'test@gmail.com')->first());
    }

    /**
     * Test that registration rejects passwords without digits
     */
    public function test_registration_rejects_password_without_digit()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'SecurePass!abc',
            'passwordConfirmation' => 'SecurePass!abc',
        ]);

        $this->assertNull(User::where('email', 'test@gmail.com')->first());
    }

    /**
     * Test that registration rejects passwords without special characters
     */
    public function test_registration_rejects_password_without_symbol()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'SecurePass123',
            'passwordConfirmation' => 'SecurePass123',
        ]);

        $this->assertNull(User::where('email', 'test@gmail.com')->first());
    }

    /**
     * Test that registration rejects mismatched passwords
     */
    public function test_registration_rejects_mismatched_passwords()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'SecurePass123!',
            'passwordConfirmation' => 'DifferentPass123!',
        ]);

        $this->assertNull(User::where('email', 'test@gmail.com')->first());
    }

    /**
     * Test complete valid registration
     */
    public function test_valid_registration_creates_user()
    {
        $response = $this->post('/register', [
            'name' => 'Valid User',
            'email' => 'validuser@gmail.com',
            'password' => 'SecurePassword123!',
            'passwordConfirmation' => 'SecurePassword123!',
        ]);

        $user = User::where('email', 'validuser@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Valid User', $user->name);
    }
}
