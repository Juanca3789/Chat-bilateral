<?php
require_once __DIR__ . "/../Libraries/Connection.php";

class ChatKey {
    public int $chat_id;
    public int $user_id;
    public string $chat_key_for_user;
    public int $version;
    public string $created_at;

    public function create(): bool {
        $conn = (new Connection())->connect();
        try {
            $sql = "INSERT INTO ChatKey (chat_id, user_id, chat_key_for_user, version)
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE chat_key_for_user = VALUES(chat_key_for_user), version = VALUES(version)";
            $stmnt = $conn->prepare($sql);
            $stmnt->bind_param("iisi",
                $this->chat_id,
                $this->user_id,
                $this->chat_key_for_user,
                $this->version
            );
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function read(int $chatId, int $userId): ?ChatKey {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare("SELECT * FROM ChatKey WHERE chat_id = ? AND user_id = ?");
            $stmnt->bind_param("ii", $chatId, $userId);
            $stmnt->execute();
            $result = $stmnt->get_result();
            return $result->fetch_object(ChatKey::class) ?: null;
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function delete(int $chatId, int $userId): bool {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare("DELETE FROM ChatKey WHERE chat_id = ? AND user_id = ?");
            $stmnt->bind_param("ii", $chatId, $userId);
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }
}
?>