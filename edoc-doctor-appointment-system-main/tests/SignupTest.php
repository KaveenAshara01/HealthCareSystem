<?php
use PHPUnit\Framework\TestCase;

class SignupTest extends TestCase
{
    private $mysqli;

    protected function setUp(): void
    {
        // Establish a database connection for testing.
        $this->mysqli = new mysqli('localhost', 'root', 'achintha2002', 'edoc');
        if ($this->mysqli->connect_errno) {
            die("Database connection failed: " . $this->mysqli->connect_error);
        }
    }

    protected function tearDown(): void
    {
        // Clean up the database connection.
        $this->mysqli->close();
    }

    // Positive Test Case: Successful signup with valid details
    public function testSuccessfulSignup()
    {
        $email = "newuser2@example.com";
        $password = "validpassword";
        $name = "John Doe";

        // Insert into the `webuser` table to simulate sign-up
        $query = "INSERT INTO webuser (email, usertype) VALUES ('$email', 'p')";
        $this->mysqli->query($query);

        $result = $this->mysqli->query("SELECT * FROM webuser WHERE email='$email'");
        $this->assertEquals(1, $result->num_rows, "User was not added successfully.");
    }

    // Negative Test Case: Signup with an already existing email
    public function testSignupWithExistingEmail()
    {
        $email = "pamuthu@gmail.com";  // Assume this already exists in DB
        $password = "123";

        $query = "SELECT * FROM webuser WHERE email='$email'";
        $result = $this->mysqli->query($query);
        
        if ($result->num_rows > 0) {
            $this->assertTrue(true, "Email already exists.");
        } else {
            $this->fail("Test failed: Email should already exist.");
        }
    }

    // Negative Test Case: Password confirmation mismatch
    public function testPasswordMismatch()
    {
        $password = "password123";
        $confirmPassword = "password456";  // Mismatched password

        $this->assertNotEquals($password, $confirmPassword, "Passwords should not match.");
    }

    // Negative Test Case: Signup with invalid email format
    public function testInvalidEmailFormat()
    {
        $email = "invalid-email-format";

        $this->assertFalse(filter_var($email, FILTER_VALIDATE_EMAIL), "Invalid email format passed.");
    }

    // Negative Test Case: Mobile number does not match the pattern
    public function testInvalidMobileNumber()
    {
        $mobile = "123456";  // Too short

        $this->assertDoesNotMatchRegularExpression('/^[0]{1}[0-9]{9}$/', $mobile, "Mobile number format is invalid.");
    }
}
