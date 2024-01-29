<?php

require_once '../Models/AuthModel.php';

class Auth{
    private $model;

    public function __construct()
    {
        $this->model = new modelAuth();
    }

    public function Login($data){
        $this->model->Login($data);
    }
}