<?php

require_once __DIR__ . '/../models/TourCategoryModel.php';

class TourCategoryController {
    protected $model;
    public function __construct(){ $this->model = new TourCategoryModel(); }

    public function index(){
        $items = $this->model->all();
        require __DIR__ . '/../views/tour_categories/index.php';
    }

    public function create(){
        $item = null;
        require __DIR__ . '/../views/tour_categories/form.php';
    }

    public function store(){
        $this->model->create($_POST);
        header('Location: /?r=tour_categories');
        exit;
    }

    public function edit($id){
        $item = $this->model->find($id);
        require __DIR__ . '/../views/tour_categories/form.php';
    }

    public function update($id){
        $this->model->update($id, $_POST);
        header('Location: /?r=tour_categories');
        exit;
    }

    public function delete($id){
        $this->model->delete($id);
        header('Location: /?r=tour_categories');
        exit;
    }
}