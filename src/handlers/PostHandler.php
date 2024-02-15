<?php

namespace src\handlers;
use \src\models\Post;
use src\models\PostComment;
use \src\models\PostLike;
use \src\models\User;
use \src\models\UserRelation;

class PostHandler {

    public static function addPost($idUser, $type, $body){
        if(!empty($idUser) && !empty($body)){

            Post::insert([
                'id_user' => $idUser,
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'body' => $body
            ])->execute();

        }
    }

    public static function _postListToObject($postList, $usuarioLogadoid)
    {
        // Transformar os resultados em objetos dos models  
        $posts = [];
        foreach($postList as $postItem){
            $post = new Post();
            $post->id = $postItem['id'];
            $post->type = $postItem['type'];
            $post->created_at = $postItem['created_at'];
            $post->body = $postItem['body'];
            $post->mine = false;

            if($postItem['id_user'] == $usuarioLogadoid){
                $post->mine = true;
            }
            // Preencher informações adicionais no post
            $user = User::select()->where('id', $postItem['id_user'])->one();
            $post->user = new User();
            $post->user->id = $user['id'];
            $post->user->name = $user['name'];
            $post->user->avatar = $user['avatar'];

            // Preencher likes
            $likes = PostLike::select()->where('id_post', $postItem['id'])->get();
            $post->likeCount = count($likes);

            $liked = PostLike::select()
                ->where('id_post', $postItem['id'])
                ->where('id_user', $usuarioLogadoid)
                ->get();

            $post->liked = count($liked) > 0 ? true : false;

            // preencher comentarios
            $post->comments = PostComment::select()->where('id_post', $postItem['id'])->get();
            foreach($post->comments as $key => $comentarios){
                $post->comments[$key]['user'] =  User::select()->where('id', $comentarios['id_user'])->one();
            }

            $posts[] = $post;
        }
        return $posts;
    }

    public static function isLiked($id, $idUser){
        $liked = PostLike::select()
                    ->where('id_post', $id)
                    ->where('id_user', $idUser)
                ->get();

        if(count($liked) > 0){
            return true;
        }else{
            return false;
        }
    }
    public static function getUserfeed($idUser, $page, $usuarioLogadoid)
    {
        $porPage = 2;

        // Pegar os POSTS dessa galera ordenado pela data
        $postList = Post::select()
                        ->where('id_user', $idUser)
                        ->orderBy('created_at', 'desc')
                        ->page($page, $porPage)
                    ->get();

        $totalPost = Post::select()
                        ->where('id_user', $idUser)
                        ->count();

        $totalPags = ceil($totalPost / $porPage);

        // Transformar os resultados em objetos dos models
        $posts = self::_postListToObject($postList, $usuarioLogadoid);
    
        // Retornar o resultado
        return [
            'posts' => $posts,
            'totalPags' => $totalPags,
            'pagAtual' => $page
        ];
    }
    public static function getHomeFeed($idUser, $page){

        $porPage = 2;
        // Lista dos usuarios que meu usuário segue
        $userListaux = UserRelation::select('user_to')->where('user_from', $idUser)->get();

        $userList = [];
        $array = array_walk_recursive($userListaux, function($valor, $key) use(&$userList){
            $userList[] = $valor;
        });
        $userList[] = $idUser;
        // Pegar os POSTS dessa galera ordenado pela data
        $postList = Post::select()  
                        ->where('id_user', 'in' , $userList)
                        ->orderBy('created_at', 'desc')
                        ->page($page, $porPage)
                    ->get();

        $totalPost = Post::select()
                    ->where('id_user', 'in' ,$userList)
                    ->count();

        $totalPags = ceil($totalPost / $porPage);
        // Transformar os resultados em objetos dos models
        $posts = self::_postListToObject($postList, $idUser);
    
        // Retornar o resultado
        return [
            'posts' => $posts,
            'totalPags' => $totalPags,
            'pagAtual' => $page
        ];
    }

    public static function getPhotosFrom($idUser){
        $photosData = Post::select()
                    ->where('id_user', $idUser)
                    ->where('type','photo')
                    ->orderBy('created_at', 'desc')
                    ->get();
        $photos = [];
        foreach($photosData as $photo){
            $post = new Post();
            $post->id = $photo['id'];
            $post->type = $photo['type'];
            $post->created_at = $photo['created_at'];
            $post->body = $photo['body'];

            $photos[] = $post;
        }

        return $photos;
    }

    public static function deleteLike($id, $idUser){
        PostLike::delete()
        ->where('id_post', $id)
        ->where('id_user', $idUser)
        ->execute();
    }

    public static function addLike($id, $idUser){
        $hoje = date('Y-m-d');
        PostLike::insert([
            'id_post' => $id,
            'id_user' => $idUser,
            'created_at' => $hoje
            ])
            ->execute();
    }

    public static function addComment($id, $txt, $usuarioLogado){
        PostComment::insert([
            'id_post' => $id,
            'id_user' => $usuarioLogado,
            'created_at' => date('Y-m-d H:i:s'),
            'body' => $txt
        ])->execute();
    }

    public static function delete($id, $usuarioLogadoid){
        // Verificar se o post existe e se é seu
        $post = Post::select()
                    ->where('id', $id)
                    ->where('id_user', $usuarioLogadoid)
                ->get();
        
        if(count($post) > 0){
            $post = $post[0];
            // Deletar os likes e comments
            PostLike::delete()->where('id_post', $id)->execute();
            PostComment::delete()->where('id_post', $id)->execute();
            // Se o post for foto deletar o arquivo
            if($post['type'] == 'photo'){
                $img = 'media/uploads/'.$post['body'];
                if(file_exists($img)){
                    unlink($img);
                }
            }
            // Deletar o post
            Post::delete()->where('id', $id)->execute();
        }
        
        
        
    }

}