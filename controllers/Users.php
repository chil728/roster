<?php

require_once '../helpers/Mailer.php';
require_once '../helpers/SessionHelper.php';
require_once '../models/User.php';

class Users {
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User;
    }

    public function register()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email'    => trim($_POST['email']    ?? ''),
            'password' => trim($_POST['password'] ?? ''),
            'repeatPwd' => trim($_POST['repeatPwd'] ?? '')
        ];

        if (in_array('', $data, true)) {
            flash('register-error', 'Please fill out all fields.', 'form-message error');
            redirect('../register');
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $data['username'])) {
            flash('register-error', 'Invalid username.', 'form-message error');
            redirect('../register');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            flash('register-error', 'Invalid email address.', 'form-message error');
            redirect('../register');
        }

        if (strlen($data['password']) < 8) {
            flash('register-error', 'Password must be at least 8 characters.', 'form-message error');
            redirect('../register');
        }

        if ($data['password'] !== $data['repeatPwd']) {
            flash('register-error', 'Passwords do not match.', 'form-message error');
            redirect('../register');
        }

        if ($this->userModel->findUserByEmailOrUsername($data['email'], $data['username'])) {
            flash('register-error', 'Username or email already taken.', 'form-message error');
            redirect('../register');
        }

        $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
        $code   = random_int(100000, 999999);

        $_SESSION['temp_user'] = [
            'username' => $data['username'],
            'email'   => $data['email'],
            'password' => $hashed
        ];

        $_SESSION['email_verification'] = ['email' => $data['email'], 'code' => $code, 'ts' => time()];

        if (!Mailer::sendRegisterEmail($data['email'], $code)) {
            flash('register', 'Failed to send verification email. Please try again.', 'form-message error');
            redirect('../register');
        }

        flash('verify', 'Verification Code has been sent to your email', 'form-message normal');
        redirect('../verify');
    }

    public function verify()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

        $entered = intval($_POST['code'] ?? 0);
        $temp    = $_SESSION['temp_user'] ?? null;

        if (!$temp || !User::verifyEmailCode($temp['email'], $entered)) {
            flash('verify-error', 'Invalid or expired code. Please try again.', 'form-message error');
            redirect('../verify');
        }

        // Persist
        $this->userModel->register(
            $temp['username'],
            $temp['email'],
            $temp['password']
        );

        unset($_SESSION['temp_user'], $_SESSION['email_verification']);

        flash('register-success', 'Registration successfully! Please log in.', 'form-message success');
        redirect('../login');
    }

    public function login()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
        $login    = trim($_POST['username-email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($login === '' || $password === '') {
            flash('login-error', 'Please fill out all fields.', 'form-message error');
            redirect("../login");
        }

        $user = $this->userModel->login($login, $password);

        if ($user) {
            flash('login-success', 'Login Success', 'form-message success');
            $_SESSION['userID'] = $user->id;
            $_SESSION['username'] = $user->userName;
            $_SESSION['email'] = $user->userEmail;
            redirect('../home');
        } else {
            flash('login-error', 'Invalid username/email or password.', 'form-message error');
            redirect('../login');
        }
    }

    public function forget()
    {

    }

    public function reset()
    {

    }

    public function logout()
    {
        session_unset();
        session_destroy();
        redirect('../home');
    }
}

$init = new Users;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    switch ($_POST['action']) {
        case 'register':
            $init->register();
            break;
        case 'verify':
            $init->verify();
            break;
        case 'login':
            $init->login();
            break;
        case 'forget':
            $init->forget();
            break;
        case 'reset':
            $init->reset();
            break;
        case 'logout':
            $init->logout();
            break;
        default:
            redirect('../index');
            break;
    }
}