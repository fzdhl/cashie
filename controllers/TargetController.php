<?php
    class TargetController extends Controller {
        public function __construct() {
            session_start();
            if (!isset($_SESSION['user'])) {
                header("Location: ?c=UserController&m=loginView");
                exit;
            }
            if ($_SESSION['user']->privilege == 'admin') {
                header("Location: ?c=AdminTargetController&m=index");
                exit;
            }
        }

        private function getProcessedTargets() {
            $userId = $_SESSION['user']->user_id;
            $targetModel = $this->loadModel('Target');
            $transactionModel = $this->loadModel('Transaction');

            $targets = $targetModel->getByUserId($userId, 50); // Get all targets for the user

            foreach ($targets as &$target) {
                $targetId = $target['target_id'];
                $targetAmount = $target['jumlah'];
                
                $achievedAmount = $transactionModel->getSumAmountForTarget($targetId);
                
                $target['achieved_amount'] = (float)$achievedAmount;
                $target['remaining_amount'] = max(0, $targetAmount - $achievedAmount); // Ensure non-negative
                
                if ($targetAmount > 0) {
                    $progressPercentage = min(100, ($achievedAmount / $targetAmount) * 100);
                } else {
                    $progressPercentage = 0;
                }
                $target['progress_percentage'] = round($progressPercentage); // Round for display
            }
            unset($target); // Unset the reference
            return $targets;
        }

        public function index() {
            $targets = $this->getProcessedTargets();
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
            $targets = $this->getProcessedTargets(); // Call the new private method
            include "views/targetCards.php"; // to be injected with AJAX in target.js
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