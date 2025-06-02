<?php
    class Controller {
        public function loadModel($modelName) {
            // to do: tambahkan pemilihan model
            include_once "models/Model.php";
            include_once "models/$modelName.php";

            return new $modelName;
        }
        public function loadView($viewName, $data = []) {
            foreach ($data as $key => $value) {
                $$key = $value;
            }
            include_once "views/$viewName.php";
        }
    }