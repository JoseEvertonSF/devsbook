<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
class ConfigController extends Controller {
    private $usuarioLogado;
    public function __construct(){
        $this->usuarioLogado = UserHandler::checkLogin();
        if($this->usuarioLogado == false){
            $this->redirect('/login');
        }

    }
    public function index()
    {   
        $user = UserHandler::getUser($this->usuarioLogado->id);
        $this->render('config', [
            'usuario' => $user
        ]);
    
    }

    public function update(){
        $user = UserHandler::getUser($this->usuarioLogado->id);

        $nome = filter_input(INPUT_POST, 'name');
        $dtNascimento = filter_input(INPUT_POST, 'dt_nascimento');
        $email = filter_input(INPUT_POST, 'email');
        $cidade = filter_input(INPUT_POST, 'cidade');
        $trabalho = filter_input(INPUT_POST, 'trabalho');
        $senha = filter_input(INPUT_POST, 'senha');
        $confSenha = filter_input(INPUT_POST, 'conf_senha');
        $verifyEmail = UserHandler::emailExists($email);
        if($verifyEmail && $email != $user->email){
            $_SESSION['flash'] = 'Email já existente';
            $this->redirect('/config');
        }

        if($senha !== $confSenha){
            $_SESSION['flash'] = 'Confirmação de senha incorreta.';
            $this->redirect('/config');
        }

        $avatarName = '';
        if(isset($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])){
            $newAvatar = $_FILES['avatar'];
            if(in_array($newAvatar['type'], ['image/jpeg', 'image/jpg', 'image/png'])){
                $avatarName = $this->cutImage($newAvatar, 200, 200, 'media/avatars');
            }
        }

        $coverName = '';
        if(isset($_FILES['cover']) && !empty($_FILES['cover']['tmp_name'])){
            $newCover = $_FILES['cover'];
            if(in_array($newCover['type'], ['image/jpeg', 'image/jpg', 'image/png'])){
                $coverName = $this->cutImage($newCover, 850, 310, 'media/covers');
            }
        }

        $arrayUser = [
            'email' => $email,
            'password' => $senha,
            'name' => $nome,
            'birthdate' => $dtNascimento,
            'city' => $cidade,
            'work' => $trabalho,
            'avatar' => $avatarName,
            'cover' => $coverName          
        ];

        $arrayUser = array_filter($arrayUser);
        $atualizaInfo = UserHandler::updateUser($user->id, $arrayUser);
        $this->render('config', [
            'usuario' => $atualizaInfo
        ]);

    }

    private function cutImage($file, $w, $h, $folder){
        list($widthOrig, $heightOrig) = getimagesize($file['tmp_name']);
        $ratio = $widthOrig / $heightOrig;

        $newWidth = $w;
        $newHeight = $newWidth / $ratio;

        if($newHeight < $h){
            $newHeight = $h;
            $newWidth = $newHeight * $ratio;
        }

        $x = $w - $newWidth;
        $y = $h - $newHeight;
        $x = $x < 0 ? $x /2 : $x;
        $y = $y < 0 ? $y / 2 : $y;

        $finalImage = imagecreatetruecolor($w, $h);
        switch($file['type']){
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/png':
                $image = imagecreatefrompng($file['tmp_name']);
                break;
        }

        imagecopyresampled(
            $finalImage, $image,
            $x, $y, 0, 0,
            $newWidth, $newHeight, $widthOrig, $heightOrig
        );

        $fileName = md5(time().rand(0,9999)).'.jpg';

        imagejpeg($finalImage, $folder.'/'.$fileName);

        return $fileName;

    }

}