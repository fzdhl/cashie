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
            $targets = $model->getByUserId($_SESSION['user']->user_id, 50);
            $this->loadView('target', ['targets' => $targets]);
        }

        public function createProcess() {
            $user_id = $_SESSION['user']->user_id;
            $target = $_POST['target'];
            $amount = $_POST['amount'];

            $model = $this->loadModel('Target');
            $status = $model->insert($user_id, $target, $amount);

            echo $status;
        }

        public function getTargetCards() {
            $model = $this->loadModel('Target');
            $targets = $model->getByUserId($_SESSION['user']->user_id, 50);
            include "views/targetCards.php"; // untuk di inject dengan AJAX pada target.js
        }

        public function updateProcess() {
            $target_id = $_POST['target_id'];
            $target = $_POST['target'];
            $amount = $_POST['amount'];

            $model = $this->loadModel('Target');
            $status = $model->update($target_id, $target, $amount);

            header('Content-Type: application/json');
            echo json_encode(['success' => $status]);
        }

        public function deleteProcess() {
            $target_id = $_GET['target_id'];

            $model = $this->loadModel('Target');
            $status = $model->delete($target_id);

            echo $status;
        }
    }