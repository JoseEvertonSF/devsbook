<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;
class HomeController extends Controller {
    private $usuarioLogado;
    public function __construct(){
        $this->usuarioLogado = UserHandler::checkLogin();
        if($this->usuarioLogado == false){
            $this->redirect('/login');
        }

    }
    public function index(){
        $page = intval(filter_input(INPUT_GET, 'page'));
        
        $feed = PostHandler::getHomeFeed(
            $this->usuarioLogado->id,
            $page
        );
        
        $this->render('home', [
            'usuario' => $this->usuarioLogado,
            'feed' => $feed
        ]);
    }


}