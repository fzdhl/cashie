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
    }