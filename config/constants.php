<?php

$defaultPath = public_path() . '/uploads/';
$defaultPath1 = storage_path() . '/app/uploads/';
$viewPath = '/uploads/';
// $viewPath = $_SERVER['APP_URL'] . '/uploads/';
$viewPath1 = storage_path() . '/app/uploads/';
$perPage = 20;
return [
    'pagination' => ['perPage' => '15'],
    'paginationapi' => ['perPage' => '15'],
    'uploadFilePath' => [
        'product' => ['default' => $defaultPath . 'product/', 'view' => $viewPath . 'product/'],
            'categories' => ['default' => $defaultPath1 . 'categories/', 'view' => $viewPath1],
    ], 
    
];
