<?php
use core\Router;

$router = new Router();

$router->get('/', 'HomeController@index');

$router->get('/login', 'LoginController@signin');
$router->post('/login', 'LoginController@signinAction');

$router->get('/cadastro', 'LoginController@signup');
$router->post('/cadastro', 'LoginController@signupAction');

$router->post('/post/new', 'PostController@new');
$router->get('/post/{id}/delete', 'PostController@delete');

$router->get('/perfil/{id}', 'ProfileController@index');
$router->get('/perfil', 'ProfileController@index');

$router->get('/perfil/{id}/amigos', 'ProfileController@friends');
$router->get('/amigos', 'ProfileController@friends');

$router->get('/perfil/{id}/follow', 'ProfileController@follow');
$router->get('/sair', 'LoginController@logout');

$router->get('/perfil/{id}/fotos', 'ProfileController@photos');
$router->get('/fotos', 'ProfileController@photos');

$router->get('/pesquisa', 'SearchController@index');

$router->post('/config/up', 'ConfigController@update');
$router->get('/config', 'ConfigController@index');

$router->get('/ajax/like/{id}','AjaxController@like');
$router->post('/ajax/comment', 'AjaxController@comment');
$router->post('/ajax/upload', 'AjaxController@upload');

