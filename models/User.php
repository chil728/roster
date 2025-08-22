<?php

require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../config/Database.php';

class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function findUserByEmailOrUsername(string $username, string $email)
    {
        $this->db->query(
            'SELECT * FROM users 
             WHERE userName = :username 
                OR userEmail = :email'
        );
        $this->db->bind(':username', $username);
        $this->db->bind(':email', $email);
        $row = $this->db->getResult();
        return $this->db->rowCount() > 0 ? $row : false;
    }

    public function register($username, $email, $pwd)
    {
        $this->db->query(
            'INSERT INTO users (userName, userEmail, userPwd) VALUES (:username, :email, :password)'
        );
        $this->db->bind(':username', $username);
        $this->db->bind(':email', $email);
        $this->db->bind(':password', $pwd);

        return $this->db->execute();
    }

    public function login(string $login, string $pwd)
    {

        $row = $this->findUserByEmailOrUsername($login, $login);
        if (!$row) {
            return false;
        }

        if (password_verify($pwd, $row->userPwd)) {
            return $row;
        } elseif ($pwd == $row->userPwd) {
            return $row;
        }

        return false;
    }

    public static function verifyEmailCode(string $email, int $code): bool
    {
        if (empty($_SESSION['email_verification'])) {
            return false;
        }
        $v = $_SESSION['email_verification'];
        if (
            $v['email'] === $email
            && $v['code']  === $code
            && (time() - $v['ts']) < 600
        ) {
            unset($_SESSION['email_verification']);
            return true;
        }
        return false;
    }

    public function createPasswordReset(int $userID, string $token, string $expiresAt): bool
    {
        $sql = 'INSERT INTO password_resets (userID, token, expiresAt)
                VALUES (:uid, :token, :exp)';
        $this->db->query($sql);
        $this->db->bind(':uid',   $userID);
        $this->db->bind(':token', $token);
        $this->db->bind(':exp',   $expiresAt);
        return $this->db->execute();
    }

    public function getPasswordResetByToken(string $token)
    {
        $sql = 'SELECT pr.*, u.userEmail
                FROM password_resets pr
                JOIN users u ON pr.userID = u.userID
                WHERE pr.token = :token';
        $this->db->query($sql);
        $this->db->bind(':token', $token);
        return $this->db->getResult();  // returns object or false
    }

    public function deletePasswordReset(string $token): bool
    {
        $this->db->query('DELETE FROM password_resets WHERE token = :token');
        $this->db->bind(':token', $token);
        return $this->db->execute();
    }

    public function resetPassword(string $newPwdHash, string $email): bool
    {
        $this->db->query(
            'UPDATE users 
             SET userPwd = :pwd 
             WHERE userEmail = :email'
        );
        $this->db->bind(':pwd',   $newPwdHash);
        $this->db->bind(':email', $email);
        return $this->db->execute();
    }
}
