<?php
require_once __DIR__ . "/../Libraries/Connection.php";

class UserKey {
    public int $user_id;
    public string $public_key;
    public string $private_key_blob;
    public string $kdf_salt;
    public array $kdf_params;
    public int $version;
    public string $updated_at;

    public function create(): bool {
        $conn = (new Connection())->connect();
        try {
            $sql = "REPLACE INTO UserKey (user_id, public_key, private_key_blob, kdf_salt, kdf_params, version)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmnt = $conn->prepare($sql);
            $paramsJson = json_encode($this->kdf_params, JSON_UNESCAPED_SLASHES);
            $stmnt->bind_param("issssi",
                $this->user_id,
                $this->public_key,
                $this->private_key_blob,
                $this->kdf_salt,
                $paramsJson,
                $this->version
            );
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function read(int $userId): ?UserKey {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare("SELECT * FROM UserKey WHERE user_id = ?");
            $stmnt->bind_param("i", $userId);
            $stmnt->execute();
            $result = $stmnt->get_result();
            $obj = $result->fetch_object(UserKey::class);
            if ($obj) {
                $obj->kdf_params = json_decode($obj->kdf_params, true);
            }
            return $obj ?: null;
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function delete(int $userId): bool {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare("DELETE FROM UserKey WHERE user_id = ?");
            $stmnt->bind_param("i", $userId);
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }
}
?>