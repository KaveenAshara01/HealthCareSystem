<?php

use PHPUnit\Framework\TestCase;

class InsertSessionTest extends TestCase
{
    private $db;

    /**
     * Setup database connection.
     */
    protected function setUp(): void
    {
        $this->db = new mysqli('localhost', 'root', 'achintha2002', 'edoc');
    }

    /**
     * Clean up after each test by removing inserted sessions.
     */
    protected function tearDown(): void
    {
        $this->db->query("DELETE FROM schedule WHERE title = 'Test Session' OR title = 'Past Session'");
        $this->db->close();
    }

    /**
     * Positive Test: Insert a valid session.
     */
    public function testInsertValidSession()
    {
        $result = $this->db->query("
            INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop, venue_id, price)
            VALUES (1, 'Test Session', '2024-10-31', '21:00:00', 50, 1, 10000.00)
        ");

        $this->assertTrue($result, "Failed to insert a valid session.");
    }

    /**
     * Negative Test: Insert a session with an empty title.
     */
    public function testInsertSessionWithEmptyTitle()
    {
        $this->expectException(mysqli_sql_exception::class);
        $this->expectExceptionMessage("Check constraint 'chk_title_not_empty' is violated.");

        $this->db->query("
            INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop, venue_id, price)
            VALUES (1, '', '2024-10-31', '21:00:00', 50, 1, 10000.00)
        ");
    }

    /**
     * Negative Test: Insert a session with a past date.
     */
    public function testInsertSessionWithPastDate()
    {
        $this->expectException(mysqli_sql_exception::class);
        $this->expectExceptionMessage("Scheduled date must be today or a future date.");

        $this->db->query("
            INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop, venue_id, price)
            VALUES (1, 'Past Session', '2023-10-01', '21:00:00', 50, 1, 10000.00)
        ");
    }

    /**
     * Negative Test: Insert a session with an invalid price.
     */
    public function testInsertSessionWithInvalidPrice()
    {
        $this->expectException(mysqli_sql_exception::class);
        $this->expectExceptionMessage("Incorrect decimal value: 'abc' for column 'price' at row 1");

        $this->db->query("
            INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop, venue_id, price)
            VALUES (1, 'Invalid Price Session', '2024-10-31', '21:00:00', 50, 1, 'abc')
        ");
    }
}
