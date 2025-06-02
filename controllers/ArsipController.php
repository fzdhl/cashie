<?php
class ArsipController {
    private $model;
    private $view;

    public function __construct($model, $view) {
        $this->model = $model;
        $this->view = $view;
    }

    public function index() {
        $filters = $_GET;
        $arsip = $this->model->getFiltered($filters);
        $this->view->renderIndex($arsip);
    }

    public function create() {
        $this->view->renderCreate();
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $targetDir = "public/uploads/";
            $fileName = basename($_FILES["file"]["name"]);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                $data = [
                    'transaksi_id' => $_POST['transaksi_id'],
                    'nama_file' => $fileName,
                    'path_file' => $targetFile
                ];
                $this->model->create($data);
                header("Location: /arsip");
            }
        }
    }

    public function edit($id) {
        $arsip = $this->model->getById($id);
        $this->view->renderEdit($arsip);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'transaksi_id' => $_POST['transaksi_id'],
                'nama_file' => $_POST['nama_file'],
                'path_file' => $_POST['old_path']
            ];

            if (!empty($_FILES['file']['name'])) {
                $targetDir = "public/uploads/";
                $fileName = basename($_FILES["file"]["name"]);
                $targetFile = $targetDir . $fileName;

                if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                    if (file_exists($_POST['old_path'])) {
                        unlink($_POST['old_path']);
                    }
                    $data['nama_file'] = $fileName;
                    $data['path_file'] = $targetFile;
                }
            }

            $this->model->update($id, $data);
            header("Location: /arsip");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->delete($id);
            header("Location: /arsip");
        }
    }
}
?>