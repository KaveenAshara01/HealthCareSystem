<?php

use PHPUnit\Framework\TestCase;

class AddDoctorTest extends TestCase
{
    private $db;

    /**
     * Setup the database connection.
     */
    protected function setUp(): void
    {
        $this->db = new mysqli('localhost', 'root', 'achintha2002', 'edoc');
    }

    /**
     * Positive Test: Add a valid doctor.
     */
    public function testAddValidDoctor()
    {
        $result = $this->db->query("
            INSERT INTO doctor (docname, docemail, docnic, doctel, specialties, docpassword) 
            VALUES ('John Doe', 'valid@example.com', '123456789V', '0712345678', 1, 'password123')
        ");
        $this->assertTrue($result, "Failed to add a valid doctor.");
    }

    /**
     * Negative Test: Add a doctor with missing name.
     */
    public function testAddDoctorWithMissingName()
    {
        $this->expectException(mysqli_sql_exception::class);
        $this->expectExceptionMessage("Column 'docname' cannot be null");

        $this->db->query("
            INSERT INTO doctor (docname, docemail, docnic, doctel, specialties, docpassword) 
            VALUES (NULL, 'missingname@example.com', '987654321V', '0712345678', 1, 'password123')
        ");
    }

    /**
     * Negative Test: Add a doctor with duplicate email.
     */
    public function testAddDoctorWithDuplicateEmail()
{
    // Insert the first doctor with duplicate email
    $this->db->query("
        INSERT INTO doctor (docname, docemail, docnic, doctel, specialties, docpassword) 
        VALUES ('Jane Doe', 'duplicate@example.com', '987654321V', '0712345678', 1, 'password123')
    ");

    $this->expectException(mysqli_sql_exception::class);

    // Attempt to insert a second doctor with the same email
    try {
        $this->db->query("
            INSERT INTO doctor (docname, docemail, docnic, doctel, specialties, docpassword) 
            VALUES ('John Smith', 'duplicate@example.com', '123456789V', '0712345679', 2, 'password123')
        ");
    } catch (mysqli_sql_exception $e) {
        // Adjust the assertion to match the full message structure
        $this->assertStringContainsString(
            "Duplicate entry 'duplicate@example.com' for key",
            $e->getMessage(),
            "Duplicate email constraint was not correctly enforced."
        );
        throw $e;
    }
}


    /**
     * Negative Test: Add a doctor with an invalid NIC.
     */
    public function testAddDoctorWithInvalidNIC()
    {
        $this->expectException(mysqli_sql_exception::class);
        $this->expectExceptionMessage("Check constraint 'chk_nic_not_empty' is violated");

        $this->db->query("
            INSERT INTO doctor (docname, docemail, docnic, doctel, specialties, docpassword) 
            VALUES ('Invalid NIC', 'invalidnic@example.com', '', '0712345678', 1, 'password123')
        ");
    }

    /**
     * Clean up after each test.
     */
    protected function tearDown(): void
    {
        $this->db->query("DELETE FROM doctor WHERE docemail IN ('valid@example.com', 'duplicate@example.com')");
        $this->db->close();
    }
}
