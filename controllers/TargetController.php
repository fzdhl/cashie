<?php
    class TargetController extends Controller {
        public function __construct() {
            session_start();
            if (!isset($_SESSION['user'])) {
                header("Location: ?c=UserController&m=loginView");
                exit;
            }   
        }

        public function index() {
            $model = $this->loadModel('Target');
            $targets = $model->getByUserId($_SESSION['user']->user_id);
            
            $this->loadView('target', $targets);
        }

        public function createProcess() {
            $user_id = $_SESSION['user']->user_id;
            $target = $_POST['target'];
            $amount = $_POST['amount'];

            $model = $this->loadModel('Target');
            $status = $model->insert($user_id, $target, $amount);
            
            echo $status;
        }


    }