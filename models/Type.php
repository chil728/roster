<?php

require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../config/Database.php';

class Type {
    private $db;
    private $userID;

    public function __construct($userID)
    {
        $this->db = new Database;
        $this->userID = $userID;
    }

    public function getTypesByUser()
    {
        $this->db->query("SELECT type, bgColor, fontColor FROM types WHERE userID = :userID");

        $this->db->bind(':userID', $this->userID);

        return $this->db->getResults() ? : [];
    }

    public function isTypeExisted($type)
    {
        $this->db->query("SELECT type FROM types WHERE type = :type");

        $this->db->bind(":type", $type);

        return $this->db->rowCount() > 0;
    }

    public function setType($type, $bgColor, $fontColor)
    {
        $this->db->query("INSERT INTO types (userID, type, bgColor, fontColor) VALUES (:userID, :type, :bgColor, :fontColor)");

        $this->db->bind(":userID", $this->userID);
        $this->db->bind(":type", $type);
        $this->db->bind(":bgColor", $bgColor);
        $this->db->bind(":fontColor", $fontColor);

        return $this->db->execute();
    }

    public function updateType($oldType, $newType, $bgColor, $fontColor)
    {
        $this->db->query("UPDATE types SET userID = :userID, type = :newType, bgColor = :bgColor, fontColor = :fontColor WHERE type = :oldType");

        $this->db->bind(":userID", $this->userID);
        $this->db->bind(":oldType", $oldType);
        $this->db->bind(":newType", $newType);
        $this->db->bind(":bgColor", $bgColor);
        $this->db->bind(":fontColor", $fontColor);

        return $this->db->execute();
    }
}