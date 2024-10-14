<?php

use PHPUnit\Framework\TestCase;

class AddRecordTest extends TestCase
{
    private $db;
    private $doctorId = 1;  // Replace with an actual or mock doctor ID
    private $patientId = 1; // Replace with an actual or mock patient ID

    /**
     * Setup the database connection.
     */
    protected function setUp(): void
    {
        $this->db = new mysqli('localhost', 'root', 'achintha2002', 'edoc');
    }

    /**
     * Clean up after each test by removing the added records.
     */
    protected function tearDown(): void
    {
        $this->db->query("DELETE FROM records WHERE record_name = 'Test Record'");
        $this->db->close();
    }

    /**
     * Positive Test: Add a record with a valid name.
     */
    public function testAddRecordWithValidName()
    {
        $result = $this->db->query("
            INSERT INTO records (patient_id, description, doctor_id, record_name) 
            VALUES ('$this->patientId', 'Details of the record.', '$this->doctorId', 'Test Record')
        ");
        $this->assertTrue($result, "Failed to add a record with a valid name.");
    }

    /**
     * Negative Test: Add a record with an empty name.
     */
    public function testAddRecordWithEmptyName()
    {
        $this->expectException(mysqli_sql_exception::class);
        $this->expectExceptionMessage("Column 'record_name' cannot be null");

        $this->db->query("
            INSERT INTO records (patient_id, description, doctor_id, record_name) 
            VALUES ('$this->patientId', 'Details of the record.', '$this->doctorId', NULL)
        ");
    }
}
