<?php
require_once __DIR__."/../../Models/UserModel.php";
require_once __DIR__."/../../Models/UserKeyModel.php";
class RegisterController {
    public function handle(array $post): array {
        if (
            empty($post['email']) || 
            empty($post['name']) ||
            empty($post['password']) || 
            empty($post['public_key']) || 
            empty($post['private_key_blob']) ||
            empty($post['kdf_salt']) ||
            empty($post['kdf_params'])
        ) {
            return [
                "status" => 400,
                "error"  => "Campos requeridos faltantes"
            ];
        }
        $user = new User();
        $password_hash = password_hash($post['password'], PASSWORD_ARGON2ID);
        $user->email = $post['email'];
        $user->name = $post['name'];
        $user->password = $password_hash;
        $user->photo = empty($post['photo']) ? null : $post['photo'];
        $user->description = empty($post['description']) ? null : $post['description'];
        $user->username = empty($post['username']) ? null : $post['username'];
        $user->phone = empty($post['phone']) ? null : $post['phone'];
        $userId = $user->create();
        if (!$userId) {
            return [
                "status" => 500,
                "error"  => "No se pudo crear el usuario"
            ];
        }
        $userKey = new UserKey();
        $userKey->user_id = $userId;
        $userKey->public_key = $post['public_key'];
        $userKey->private_key_blob = $post['private_key_blob'];
        $userKey->kdf_salt = $post['kdf_salt'];
        $userKey->kdf_params = json_decode($post['kdf_params'], true);
        $userKey->version = $post['version'] ?? 1;
        $key_result = $userKey->create();
        if(!$key_result) {
            $user->delete($userId);
            return [
                "status" => 500,
                "error"  => "No se pudo crear el usuario"
            ];
        }
        return [
            "status" => 200,
            "data"   => [
                "user_id" => 123,
                "message" => "Usuario creado correctamente"
            ]
        ];
    }
}
?>