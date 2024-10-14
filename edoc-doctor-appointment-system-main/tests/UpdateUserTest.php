<?php

use PHPUnit\Framework\TestCase;

class UpdateUserTest extends TestCase
{
    private $db;
    private $insertedId; // Store the ID of the inserted user for each test

    protected function setUp(): void
    {
        $this->db = new mysqli('localhost', 'root', 'achintha2002', 'edoc');

        // Insert a mock user and store the inserted ID
        $this->db->query("
            INSERT INTO patient (pname, pemail, paddress, pdob, pnic, ptel) 
            VALUES ('John Doe', 'testuser@example.com', '123 Main St', '2000-01-01', '123456789V', '0712345678')
        ");
        $this->insertedId = $this->db->insert_id;
    }

    protected function tearDown(): void
    {
        $this->db->query("DELETE FROM patient WHERE pid = {$this->insertedId}");
        $this->db->close();
    }

    public function testUpdateUserDetailsWithValidData()
    {
        $result = $this->db->query("UPDATE patient SET pname = 'Jane Doe' WHERE pid = {$this->insertedId}");
        $this->assertTrue($result, "Failed to update user details with valid data.");

        $query = $this->db->query("SELECT pname FROM patient WHERE pid = {$this->insertedId}");
        $updatedName = $query->fetch_assoc()['pname'];
        $this->assertEquals('Jane Doe', $updatedName, "User name was not updated correctly.");
    }

   public function testUpdateUserDetailsWithEmptyName()
{
    try {
        // Try to update the user with an empty name
        $this->db->query("UPDATE patient SET pname = '' WHERE pid = {$this->insertedId}");

        // If no exception occurs, the test should fail
        $this->fail("Expected a constraint violation, but the update succeeded.");
    } catch (mysqli_sql_exception $e) {
        // Assert that the error is due to the CHECK constraint
        $this->assertStringContainsString(
            "constraint 'chk_name_not_empty'", 
            $e->getMessage(), 
            "Unexpected exception message: " . $e->getMessage()
        );
    }
}

    public function testUpdateNonExistentUser()
    {
        $result = $this->db->query("UPDATE patient SET pname = 'Ghost' WHERE pid = 999");
        $this->assertFalse($this->db->affected_rows > 0, "Update succeeded for a non-existent user.");
    }
}
