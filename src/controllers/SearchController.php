<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
class SearchController extends Controller {
    private $usuarioLogado;
    public function __construct(){
        $this->usuarioLogado = UserHandler::checkLogin();
        if($this->usuarioLogado == false){
            $this->redirect('/login');
        }

    }
    public function index()
    {
        $searchTerm = filter_input(INPUT_GET, 's');

        if(empty($searchTerm))
        {
            $this->redirect('/');
        }

        $users = UserHandler::searchUser($searchTerm);
        $this->render('search',[
            'usuario' => $this->usuarioLogado,
            'searchTerm' => $searchTerm,
            'users' => $users
        ]);
    }


}