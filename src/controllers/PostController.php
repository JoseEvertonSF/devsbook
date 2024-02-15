<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;
class PostController extends Controller {
    private $usuarioLogado;
    public function __construct(){
        $this->usuarioLogado = UserHandler::checkLogin();
        if($this->usuarioLogado == false){
            $this->redirect('/login');
        }

    }
    public function new(){
        $body = filter_input(INPUT_POST, 'body');
        if($body){
            PostHandler::addPost(
                $this->usuarioLogado->id, 
                'text',
                $body);
        }
        $this->redirect('/');
    }

    public function delete($atts = []){
        if(!empty($atts['id'])){
            $idPost = $atts['id'];
            PostHandler::delete($idPost, $this->usuarioLogado->id);
        }
        $this->redirect('/');
    }
}