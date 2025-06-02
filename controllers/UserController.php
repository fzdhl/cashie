<?php
    class UserController extends Controller {
        public function __construct() {
            session_start();
            if (isset($_SESSION['user'])) {
                header("Location: ?c=DashboardController&m=index");
            }
        }

        public function loginView() {
            $this->loadView("login", ['error' => '']);
        }

        public function loginProcess() {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $model = $this->loadModel("User");
            $result = $model->getByUsername($username);

            if ($result && password_verify($password, $result->password)) {
                $_SESSION['user'] = $result;
                header("Location: ?c=DashboardController&m=index");
            } else {
                $this->loadView("login", ['error' => 'Wrong Password.']);
            }
        }

        public function registerView() {
            $this->loadView("register", ['error' => '']);
        }

        public function registerProcess() {
            $user = $_POST['username'];
            $pass = $_POST['password'];
            $email = $_POST['email'];
            $confirm = $_POST['confirm'];

            if ($pass != $confirm) {
                $this->loadView("register", ['error' => 'Passwords do not match.']);
                return;
            }

            $model = $this->loadModel("User");
            $result = $model->getByUsername($user);
            
            if ($result) {
                $this->loadView("register", ['error' => 'Username already exists.']);
            } else {
                $result = $model->createUser($user, $pass, $email);
                header("Location: ?c=UserController&m=loginView");
            }
        }
    }