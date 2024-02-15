<?php
namespace src\handlers;

use \src\models\User;
use \src\models\UserRelation;
use \src\handlers\PostHandler;

class UserHandler
{
    public static function checkLogin()
    {
        if (!empty($_SESSION['token'])) {
            $token = $_SESSION['token'];
            $data = User::select()->where('token', $token)->one();
            if (count($data) > 0) {
                $usuarioLogado = new User();
                $usuarioLogado->id = $data['id'];
                $usuarioLogado->email = $data['email'];
                $usuarioLogado->name = $data['name'];
                $usuarioLogado->avatar = $data['avatar'];
                $usuarioLogado->birthdate = $data['birthdate'];
                return $usuarioLogado;
            }
        }
        return false;
    }

    public static function verifyLogin($email, $senha)
    {
        $user = User::select()->where('email', $email)->one();
        if ($user) {
            if (password_verify($senha, $user['password'])) {
                $token = md5(time() . rand(0, 9999) . time());
                User::update()
                    ->set('token', $token)
                    ->where('email', $email)
                    ->execute();
                return $token;
            }
        }
        return false;
    }

    public static function idExists($id)
    {
        $user = User::select()->where('id', $id)->one();
        return $user ? true : false;
    }
    public static function emailExists($email)
    {
        $user = User::select()->where('email', $email)->one();
        return $user ? true : false;
    }

    public static function getUser($id, $full = false)
    {
        $data = User::select()->where('id', $id)->one();

        if ($data) {
            $user = new User();
            $user->id = $data['id'];
            $user->name = $data['name'];
            $user->birthdate = $data['birthdate'];
            $user->city = $data['city'];
            $user->work = $data['work'];
            $user->avatar = $data['avatar'];
            $user->cover = $data['cover'];
            $user->email = $data['email'];

            if ($full) {
                $user->followers = [];
                $user->following = [];
                $user->photos = [];

                //followers
                $followers = UserRelation::select()->where('user_to', $id)->get();
                foreach ($followers as $follower) {
                    $userData = User::select()->where('id', $follower['user_from'])->one();
                    $newUser = new User();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->followers[] = $newUser;
                }

                //following
                $followings = UserRelation::select()->where('user_from', $id)->get();
                foreach ($followings as $following) {
                    $userData = User::select()->where('id', $following['user_to'])->one();
                    $newUser = new User();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->following[] = $newUser;
                }

                //Fotos
                $user->photos = PostHandler::getPhotosFrom($id);

            }
            return $user;

        }
        return false;
    }

    public static function addUser($nome, $email, $senha, $dt_nascimento)
    {
        $token = md5(time() . rand(0, 9999) . time());
        $senha = password_hash($senha, PASSWORD_DEFAULT);
        User::insert([
            'name' => $nome,
            'email' => $email,
            'password' => $senha,
            'birthdate' => $dt_nascimento,
            'token' => $token
        ])->execute();

        return $token;
    }

    public static function isFollowing($from, $to)
    {
        $data = UserRelation::select()
                        ->where('user_from', $from)
                        ->where('user_to', $to)
                    ->one();

        if($data)
        {
            return true;
        }else{
            return false;
        }
    }

    public static function follow($from, $to)
    {
        UserRelation::insert([
            'user_from' => $from,
            'user_to' => $to
        ])->execute();
    }

    public static function unfollow($from, $to)
    {
        UserRelation::delete()
            ->where('user_from', $from)
            ->where('user_to', $to)
        ->execute();
    }

    public static function searchUser($term)
    {
        $data = User::select()->where('name', 'like',  "%$term%")->get();
        $users = [];

        if($data){
            foreach($data as $user)
            {
                $newUser = new User();
                $newUser->id = $user['id'];
                $newUser->name = $user['name'];
                $newUser->avatar = $user['avatar'];

                $users[] = $newUser;
            }
        }

        return $users;
    }

    public static function updateUser($idUser, $dados = []){
        if(isset($dados['password'])){
            $senha = password_hash($dados['password'], PASSWORD_DEFAULT);
            $dados['password'] = $senha;
        }

        User::update()
            ->set($dados)
            ->where('id', $idUser)
        ->execute();

        $info = User::select()
                    ->where('id', $idUser)
                ->one();
        
        $userInfo = [];
        $newUser = new User();
        $newUser->id = $info['id'];
        $newUser->email = $info['email'];
        $newUser->name = $info['name'];
        $newUser->birthdate = $info['birthdate'];
        $newUser->city = $info['city'];
        $newUser->work = $info['work'];
        $newUser->avatar = $info['avatar'];
        $newUser->cover = $info['cover'];

        $userInfo [] = $newUser;

        return $newUser;
        
            
    }

}