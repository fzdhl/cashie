<?php
    class ProfileController extends Controller {
        public function __construct() {
            session_start();
            if (!isset($_SESSION['user'])) {
                header("Location: ?c=UserController&m=loginView");
            }
        }

        public function index() {
            $model = $this->loadModel('Profile');
            $result = $model->getByUserID($_SESSION['user']->user_id);

            $this->loadView('profile');
        }
    }