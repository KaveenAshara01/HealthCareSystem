<?php

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        // Create a mock connection to the database
        $this->db = new mysqli('localhost', 'root', 'achintha2002', 'edoc'); // Adjust DB details as needed
    }

    protected function tearDown(): void
    {
        // Close DB connection after test
        $this->db->close();
    }

    /** @test */
    public function testValidPatientLogin()
    {
        $email = 'pamuthu@gmail.com'; // Use valid test email
        $password = '123'; // Valid password

        // Simulate SQL query
        $result = $this->db->query("SELECT * FROM webuser WHERE email='$email'");
        $this->assertEquals(1, $result->num_rows, 'User not found in the system.');

        $userType = $result->fetch_assoc()['usertype'];
        $this->assertEquals('p', $userType, 'User type is not patient.');

        $patientCheck = $this->db->query("SELECT * FROM patient WHERE pemail='$email' AND ppassword='$password'");
        $this->assertEquals(1, $patientCheck->num_rows, 'Invalid credentials for patient login.');
    }

    /** @test */
    public function testInvalidPassword()
    {
        $email = 'pamuthu@gmail.com'; // Use valid test email
        $password = 'wrongpassword'; // Invalid password

        $result = $this->db->query("SELECT * FROM patient WHERE pemail='$email' AND ppassword='$password'");
        $this->assertEquals(0, $result->num_rows, 'Login succeeded with incorrect password.');
    }

    /** @test */
    public function testNonExistentUser()
    {
        $email = 'nonexistent@example.com'; // Email that doesn't exist
        $password = 'password123';

        $result = $this->db->query("SELECT * FROM webuser WHERE email='$email'");
        $this->assertEquals(0, $result->num_rows, 'Non-existent user was found.');
    }

    /** @test */
    public function testEmptyEmailAndPassword()
    {
        $email = '';
        $password = '';

        $this->assertEmpty($email, 'Email field is not empty.');
        $this->assertEmpty($password, 'Password field is not empty.');
    }
}
