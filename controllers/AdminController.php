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
            $users = $model->getAllUser();
            $this->loadView('Admin', ['user' => $model]);
        }
    }