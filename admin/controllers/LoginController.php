<?php

namespace Pandao\Admin\Controllers;

use Pandao\Admin\Models\UsersModel;
use Pandao\Common\Services\AuthHandler;
use Pandao\Common\Services\Csrf;
use Pandao\Common\Utils\AuthUtils;
use Pandao\Common\Utils\UrlUtils;
use Pandao\Common\Utils\MailUtils;

class LoginController extends Controller
{
    protected $usersModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->usersModel = new UsersModel($db);
    }

    /**
     * Display the login page.
     *
     */
    public function index($action, $display)
    {
        // Action to perform
        if ($action == 'login' && isset($_POST['login'])) $this->login();
        if ($action == 'reset' && isset($_POST['email'])) $this->reset();
        if ($action == 'logout') $this->logout();

        // Redirection to dashboard if already logged 
        if (AuthHandler::isAuthenticated()) {
            header('Location: ' . DOCBASE . PMS_ADMIN_FOLDER . '/module=dashboard');
            exit;
        }

        $this->viewData['csrf_token'] = Csrf::generateToken();
        $this->viewData['display'] = !empty($display) ? $display : 'login';

        $this->render('login', 'system');
    }

    /**
     * Handle the login form submission.
     *
     */
    public function login()
    {
        if (Csrf::verifyToken('post')) {
            $username = $_POST['username'] ?? null;
            $password = $_POST['password'] ?? null;

            $user = $this->usersModel->validateLogin($username, $password);
            
            if ($user) {
                AuthHandler::login($user);
                header('Location: ' . DOCBASE . PMS_ADMIN_FOLDER . '/module=dashboard');
                exit;
            } else 
                $_SESSION['msg_error'][] = $this->adminContext->texts['LOGIN_FAILED'];
        } else {
            $_SESSION['msg_error'][] = $this->adminContext->texts['BAD_TOKEN2'];
        }
    }

    /**
     * Handle the reset form submission.
     *
     */
    public function reset()
    {
        $email = htmlentities($_POST['email']);

        $user = $this->usersModel->validateEmail($email);
        
        if ($user) {
            $url = UrlUtils::getUrl(true) . '/' . PMS_ADMIN_FOLDER;
            $new_pass = AuthUtils::genPass(8);
            
            $mail = MailUtils::getMail($this->pms_db, 'RESET_PASSWORD', [
                '{pass}' => $new_pass,
                '{name}' => $user['firstname'] . ' ' . $user['lastname'],
                '{login}' => $user['login'],
                '{url}' => $url
            ]);

            if(MailUtils::sendMail($email, $user['firstname'], $mail['subject'], $mail['content']) !== false) {

                $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);

                $sql = "UPDATE solutionsCMS_user SET pass = :pass WHERE email = :email";
                $stmt = $this->pms_db->prepare($sql);
                $stmt->bindParam(':pass', $hashed_pass);
                $stmt->bindParam(':email', $email);
                
                $stmt->execute();
            }
        }
        $_SESSION['msg_success'][] = $this->adminContext->texts['RESET_PASS_SUCCESS'];
    }

    /**
     * Handle the logout action
     *
     */
    public function logout()
    {
        if(isset($_SESSION['user'])) unset($_SESSION['user']);
    }
}
