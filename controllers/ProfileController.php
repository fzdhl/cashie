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

            $this->loadView('profile', ['profile' => $result]);
        }

        public function updateProcess() {
            $photo = $_FILES['photo'] ?? NULL;
            $phoneNo = $_POST['phone_no'] ?? NULL;
            $model = $this->loadModel('Profile');

            if ($photo != NULL && $photo['error'] == 0) {
                // menghapus foto lama (selain foto default)
                $profile = $model->getByUserID($_SESSION['user']->user_id);
                $oldPhotoDir= $profile->photo_dir;
                // pengecekan apakah foto deffault atau bukan
                if ($oldPhotoDir != "resources/default-avatar.jpg") {
                    unlink($oldPhotoDir);
                }

                // pembuatan nama file
                $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
                $newName = uniqid('profile_', true) . '.' . $ext;
                $photoDir = 'resources/' . $newName;

                // pemindahan file
                move_uploaded_file($photo['tmp_name'], $photoDir);

                // update database
                $model->updatePhoto($_SESSION['user']->user_id, $photoDir);
            }
            
            $model->updatePhone($_SESSION['user']->user_id, $phoneNo);
            
            header('Location: ?c=ProfileController&m=index');
        }
    }