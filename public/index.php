<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use Siler\Route;
use Siler\Twig;
use Siler\Http\Request;
use Siler\Http\Response;

Response\header('Access-Control-Allow-Methods', 'GET, POST');

Twig\init(dirname(__DIR__) . '/templates');


Route\get('/', function (array $routeParams) {
    $get_params = Request\get();
    $name = $get_params['name'];
    $data = ['your_name' => $name];
    Response\html(Twig\render('index.twig', $data));
});

Route\post('/', function (array $routeParams) {
    $post_params = Request\post();

    $filename = '';
    $filepath = '';
    Response\header('Content-Type', 'application/octet-stream');
    Response\header('Content-Length', filesize($filepath));
    Response\header('Content-Disposition', "attachment;filename=\"{$filename}\"");
    readfile($filepath);
});
