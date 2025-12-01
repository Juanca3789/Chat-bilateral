<?php
require_once __DIR__."/../../Libraries/third-party/php-jwt/src/JWT.php";
require_once __DIR__."/../../Libraries/third-party/php-jwt/src/Key.php";
require_once __DIR__."/../../Config/Secrets.php";
require_once __DIR__."/../../Models/UserModel.php";

use Firebase\JWT\JWT;

class LoginController {
    public function handle(array $post): array {
        if (
            empty($post['email']) ||
            empty($post['password'])
        ) {
            return [
                "status" => 400,
                "error"  => "Campos requeridos faltantes"
            ];
        }
        $user = new User();
        $user->email = $post['email'];
        $user->password = $post['password'];
        $loggedUser = $user->readByUserAndPassword();
        if(!$loggedUser){
            return [
                "status" => 404,
                "error"  => "El usuario ingresado no existe"
            ];
        }
        if(!password_verify($user->password, $loggedUser->password)){
            return [
                "status" => 401,
                "error"  => "La contraseña ingresada es incorrecta"
            ];
        }
        $accessPayLoad = [
            "iat" => time(),
            "exp" => time() + 900,
            "user_id" => $loggedUser->user_id,
            "type" => "access"
        ];
        $refreshPayLoad = [
            "iat" => time(),
            "exp" => time() + (15 * 24 * 60 * 60),
            "user_id" => $loggedUser->user_id,
            "type" => "refresh"
        ];
        $accessToken = JWT::encode($accessPayLoad, SECRET_KEY, 'HS256');
        $refreshToken = JWT::encode($refreshPayLoad, SECRET_KEY, 'HS256');
        return [
            "status" => 200,
            "data" => [
                "message" => "Sesión iniciada correctamente",
                "user_id" => $loggedUser->user_id,
                "email" => $loggedUser->email,
                "name" => $loggedUser->name,
                "photo" => $loggedUser->photo,
                "description" => $loggedUser->description,
                "username" => $loggedUser->username,
                "status" => $loggedUser->status,
                "last_login" => $loggedUser->last_login,
                "access_token" => $accessToken,
                "refresh_token" => $refreshToken
            ]
        ];
    }
}
?>