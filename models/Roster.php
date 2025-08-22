<?php
require_once __DIR__ . '/../config/Database.php';

class Roster
{
    private $db;
    private $userID;

    public function __construct($userID)
    {
        $this->db = new Database;
        $this->userID = $userID;
    }

    public function getLatestCycle()
    {
        $this->db->query("SELECT cycle FROM rosters WHERE userID = :userID GROUP BY cycle ORDER BY cycle DESC");
        $this->db->bind(":userID", $this->userID);
        return $this->db->getResult();
    }

    public function findCycles()
    {
        $this->db->query("SELECT cycle FROM rosters WHERE userID = :userID GROUP BY cycle");
        $this->db->bind(":userID", $this->userID);
        return $this->db->getResults() ?: [];
    }

    public function setRoster($cycle, $date, $type, $remark)
    {
        $this->db->query("INSERT INTO rosters (userID, cycle, date, type, remark) VALUES (:userID, :cycle, :date, :type, :remark)");
        $this->db->bind(":userID", $this->userID);
        $this->db->bind(":cycle", $cycle);
        $this->db->bind(":date", $date);
        $this->db->bind(":type", $type);
        $this->db->bind(":remark", $remark);
        return $this->db->execute();
    }

    public function getRosterByCycle($cycle)
    {
        $this->db->query("SELECT * FROM rosters WHERE userID = :userID AND cycle = :cycle");
        $this->db->bind(":userID", $this->userID);
        $this->db->bind(":cycle", $cycle);
        return $this->db->getResults() ?: [];
    }

    public function getRostersByID($roster_id)
    {
        $this->db->query("SELECT * FROM rosters WHERE id = :id AND userID = :userID");
        $this->db->bind(":id", $roster_id);
        $this->db->bind(":userID", $this->userID);
        return $this->db->getResult();
    }

    public function getRosterByDate($date)
    {
        $this->db->query("SELECT * FROM rosters WHERE date = :date AND userID = :userID");
        $this->db->bind(":date", $date);
        $this->db->bind(":userID", $this->userID);
        return $this->db->getResult();
    }

    public function updateRoster($roster_id, $type, $remark)
    {
        $this->db->query("UPDATE rosters SET type = :type, remark = :remark WHERE id = :id AND userID = :userID");
        $this->db->bind(":id", $roster_id);
        $this->db->bind(":type", $type);
        $this->db->bind(":remark", $remark);
        $this->db->bind(":userID", $this->userID);
        return $this->db->execute();
    }

    public function deleteRosterByCycle($cycle)
    {
        $this->db->query("DELETE FROM rosters WHERE cycle = :cycle");
        $this->db->bind(":cycle", $cycle);
        return $this->db->execute();
    }
}