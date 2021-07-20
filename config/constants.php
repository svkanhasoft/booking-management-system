<?php

$defaultPath = public_path() . '/uploads/';
$defaultPath1 = storage_path() . '/app/public/uploads/';
$viewPath = $_SERVER['APP_URL'] . '/uploads/';
$viewPath1 = storage_path() . '/app/public/uploads/';
return [
    'pagination' => ['perPage' => '15'],
    'paginationapi' => ['perPage' => '15'],
    'uploadFilePath' => [
        'product' => ['default' => $defaultPath . 'product/', 'view' => $viewPath . 'product/'],
            'categories' => ['default' => $defaultPath1 . 'categories/', 'view' => $viewPath1],
    ], 
    
];
