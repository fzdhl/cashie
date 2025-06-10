<? php
class ArsipController extends Controller {
    public function __construct() {
        session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: ?c=UserController&m=loginView");
            exit;
        }
    }

    public function index() {
        $model = $this->loadModel("Arsip");
        $arsipList = $model->getByUser($_SESSION['user']->user_id);
        $this->loadView("arsip", ['arsipList' => $arsipList]);
    }

    public function upload() {
        if ($_FILES['struk']['error'] === 0) {
            $fileName = uniqid('arsip_') . '_' . basename($_FILES['struk']['name']);
            $targetPath = "uploads/arsip/" . $fileName;
            move_uploaded_file($_FILES['struk']['tmp_name'], $targetPath);
            $desc = $_POST['description'];
            $model = $this->loadModel("Arsip");
            $model->insert($_SESSION['user']->user_id, $targetPath, $desc);
        }
        header("Location: ?c=ArsipController&m=index");
    }

    public function update() {
        $id = $_POST['id'];
        $desc = $_POST['description'];
        $model = $this->loadModel("Arsip");
        $model->updateDescription($id, $desc, $_SESSION['user']->user_id);
        echo "success";
    }

    public function delete() {
        $id = $_GET['id'];
        $model = $this->loadModel("Arsip");
        $arsip = $model->getById($id, $_SESSION['user']->user_id);
        if ($arsip && file_exists($arsip->file_path)) {
            unlink($arsip->file_path);
        }
        $model->delete($id, $_SESSION['user']->user_id);
        header("Location: ?c=ArsipController&m=index");
    }
}