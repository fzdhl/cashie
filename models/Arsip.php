<?php
class Arsip {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM arsip_transaksi")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFiltered($filters) {
        $query = "SELECT * FROM arsip_transaksi WHERE 1";
        $params = [];

        if (!empty($filters['transaksi_id'])) {
            $query .= " AND transaksi_id = :transaksi_id";
            $params['transaksi_id'] = $filters['transaksi_id'];
        }
        if (!empty($filters['nama_file'])) {
            $query .= " AND nama_file LIKE :nama_file";
            $params['nama_file'] = '%'.$filters['nama_file'].'%';
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO arsip_transaksi 
            (transaksi_id, nama_file, path_file, tanggal_upload) 
            VALUES (:transaksi_id, :nama_file, :path_file, NOW())");
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $data['arsip_id'] = $id;
        $stmt = $this->db->prepare("UPDATE arsip_transaksi SET 
            transaksi_id = :transaksi_id, 
            nama_file = :nama_file, 
            path_file = :path_file 
            WHERE arsip_id = :arsip_id");
        return $stmt->execute($data);
    }

    public function delete($id) {
        $arsip = $this->getById($id);
        if ($arsip && file_exists($arsip['path_file'])) {
            unlink($arsip['path_file']);
        }
        $stmt = $this->db->prepare("DELETE FROM arsip_transaksi WHERE arsip_id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM arsip_transaksi WHERE arsip_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>