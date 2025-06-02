<?php
    class DashboardController extends Controller {
        public function __construct() {
            session_start();
            if (!isset($_SESSION['user'])) {
                header("Location: ?c=UserController&m=loginView");
            }
        }

        public function index() {
            $this->loadView("dashboard");
        }

        public function calendar() {
            $this->loadView("calendar");
        }

        public function catatanKeuangan() {
            $this->loadView("catatanKeuangan");
        }

        public function profile() {
            $this->loadView("profile");
        }

        public function goals() {
            $this->loadView("goals");
        }

        public function report() {
            $this->loadView("report");
        }

        public function kategori() {
            $this->loadView("kategori");
        }

        public function arsip() {
            $this->loadView("arsip");
        }

        public function logout() {  
            session_destroy();
            header("Location: ?c=UserController&m=loginView");
        }

        
    }