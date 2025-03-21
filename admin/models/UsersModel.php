<?php

namespace Pandao\Admin\Models;

class UsersModel
{
    protected $pms_db;

    /**
     * UsersModel constructor. Initializes the UsersModel with the database connection.
     *
     * @param Database $db Database connection.
     */
    public function __construct($db)
    {
        $this->pms_db = $db;
    }

    /**
     * Validate user login credentials.
     *
     * @param string $username Username provided by the user.
     * @param string $password Password provided by the user.
     *
     * @return array|false User data if valid, false otherwise.
     */
    public function validateLogin($username, $password)
    {
        if($this->pms_db !== false){
            $stmt = $this->pms_db->prepare('SELECT * FROM solutionsCMS_user WHERE login = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['pass'])) {
                return $user;
            }
        }
        return false;
    }
    
    /**
     * Validate email credentials.
     *
     * @param string $email Email provided by the user.
     *
     * @return array|false User data if valid, false otherwise.
     */
    public function validateEmail($email)
    {
        if($this->pms_db !== false){
            $stmt = $this->pms_db->prepare('SELECT * FROM solutionsCMS_user WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) return $user;
        }
        return false;
    }
}
