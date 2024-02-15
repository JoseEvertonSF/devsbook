<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;
class ProfileController extends Controller {
    private $usuarioLogado;
    public function __construct(){
        $this->usuarioLogado = UserHandler::checkLogin();
        if($this->usuarioLogado == false){
            $this->redirect('/login');
        }

    }
    public function index($atts = []){
        $page = intval(filter_input(INPUT_GET, 'page'));

        //Detectando o usuario acessado
        $id = $this->usuarioLogado->id;
        if(!empty($atts['id'])){
            $id = $atts['id'];
        }

        //Pegando informações do user
        $user = UserHandler::getUser($id, true);
        if(!$user){
            $this->redirect('/');
        }

        $dataNascimento = new \DateTime($user->birthdate);
        $dataHoje = new \DateTime('today');
        $user->idade = $dataNascimento->diff($dataHoje)->y;

        // Pegando o feed do usuario
        $feed = PostHandler::getUserfeed($id, $page, $this->usuarioLogado->id);

        // Verificar se eu sigo o usuario
        $isFollowing = false;
        if($user->id != $this->usuarioLogado->id){
            $isFollowing = UserHandler::isFollowing($this->usuarioLogado->id , $user->id);
        }   

        $this->render('profile', [
            'usuario' => $this->usuarioLogado,
            'user' => $user,
            'feed' => $feed,
            'isFollowing' => $isFollowing
        ]);
    }

    public function follow($atts)
    {
        $to = intval($atts['id']);
        $exists = UserHandler::idExists($to);

        if($exists){
            if(UserHandler::isFollowing($this->usuarioLogado->id, $to)){
                // Deixar de seguir
                UserHandler::unfollow($this->usuarioLogado->id, $to);
            }else{
                // Seguir
                UserHandler::follow($this->usuarioLogado->id, $to);
            }   
            
        }
        
        $this->redirect("/perfil/$to");
        
    }

    public function friends($atts = [])
    {
        //Detectando o usuario acessado
         $id = $this->usuarioLogado->id;
        if(!empty($atts['id'])){
            $id = $atts['id'];
        }

        //Pegando informações do user
        $user = UserHandler::getUser($id, true);
        if(!$user){
            $this->redirect('/');
        }

        $isFollowing = false;
        if($user->id != $this->usuarioLogado->id){
            $isFollowing = UserHandler::isFollowing($this->usuarioLogado->id , $user->id);
        }   

        $this->render('profile_friends', [
            'usuario' => $this->usuarioLogado,
            'user' => $user,
            'isFollowing' => $isFollowing
        ]);
    }

    public function photos($atts = []){
        //Detectando o usuario acessado
        $id = $this->usuarioLogado->id;
        if(!empty($atts['id'])){
            $id = $atts['id'];
        }

        //Pegando informações do user
        $user = UserHandler::getUser($id, true);
        if(!$user){
            $this->redirect('/');
        }

        $isFollowing = false;
        if($user->id != $this->usuarioLogado->id){
            $isFollowing = UserHandler::isFollowing($this->usuarioLogado->id , $user->id);
        }   

        $this->render('profile_photos', [
            'usuario' => $this->usuarioLogado,
            'user' => $user,
            'isFollowing' => $isFollowing
        ]);
    }   

}