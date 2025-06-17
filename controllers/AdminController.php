<?php
    class AdminController extends Controller {
        public function __construct() {
            session_start();
            if ($_SESSION['user']->privilege != 'admin') {
                header('Location: ?c=DashboardController&m=index');
            }
        }

        public function index() {
            $model = $this->loadModel('User');
            $users = $model->getAll();
            $this->loadView('admin', ['users' => $users]);
        }

        public function edit() {
            $user_id = $_GET['user_id'];

            $model = $this->loadModel('User');
            $user = $model->getById($user_id);


            $this->loadView('admin-edit', ['user' => $user]);
        }

        public function update() {
            $user_id = $_GET['user_id'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $username = $_POST['username'];

            $model = $this->loadModel('User');
            $user = $model->updateUser($user_id, $username, $password, $email);

            echo json_encode(['status' => 'success']);
            header('Location: ?c=AdminController&m=index');
        }

        public function delete(){
            $user_id = $_GET['user_id'];

            $model = $this->loadModel('User');
            $user = $model->deleteUser($user_id);
            header('Location: ?c=AdminController&m=index');
        }
    }