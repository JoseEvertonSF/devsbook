<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
class LoginController extends Controller {
    
    public function signin(){
        $flash = '';
        if(!empty($_SESSION['flash'])){
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
    $this->render('/login', 
        ['flash' => $flash]);
    }

    public function signinAction(){
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $senha = filter_input(INPUT_POST, 'password');

        if($email && $senha){
            $token = UserHandler::verifyLogin($email, $senha);
            if($token != false){
                $_SESSION['token'] =  $token;
                $this->redirect('/');
            }else{
                $_SESSION['flash'] = 'Usuario inexistente';
                $this->redirect('/login');
            }
        }else{
            $this->redirect('/login');
        }
    }

    public function signup(){
        $flash = '';
        if(!empty($_SESSION['flash'])){
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('/cadastro',[
           'flash' => $flash
        ]
    );
    }

    
    public function signupAction(){
        $nome = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $senha = filter_input(INPUT_POST, 'password');
        $dt_nascimento = filter_input(INPUT_POST, 'dt_nascimento');

        if($nome && $email && $senha && $dt_nascimento){
            $dt_nascimento = implode('-', array_reverse(explode("/", $dt_nascimento)));
            if(strtotime($dt_nascimento) === false){
                $_SESSION['flash'] = 'Data de nascimento invalida';
                $this->redirect('/cadastro');
            }
            if(UserHandler::emailExists($email) === false){
                $token = UserHandler::addUser($nome, $email, $senha, $dt_nascimento);
                $_SESSION['token'] = $token;
                $this->redirect('/');
            }else{
                $_SESSION['flash'] = 'Email existente!';
                $this->redirect('/cadastro');
            }
        }else{
            $this->redirect('/cadastro');
        }
    }

    public function logout(){
        $_SESSION['token'] = '';
        $this->redirect('/login');
    }

}
