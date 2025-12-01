<?php
require_once __DIR__."/../Libraries/Connection.php";
class UserChat {
    public int $user_id;
    public int $chat_id;
    public string $role;
    public string $joined_at;

    public function create() {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare(
                "INSERT INTO UserChat (
                    user_id,
                    chat_id, 
                    role
                    ) VALUES (?, ?, ?)"
                );
            $stmnt->bind_param(
                "iis",
                $this->user_id,
                $this->chat_id,
                $this->role
            );
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function readUsersByChat(int $chatId): array {
        $conn = (new Connection())->connect();
        try {
            $sql = "
                SELECT u.*
                FROM UserChat uc
                INNER JOIN User u ON u.user_id = uc.user_id
                WHERE uc.chat_id = ?
            ";
            $stmnt = $conn->prepare($sql);
            $stmnt->bind_param("i", $chatId);
            $stmnt->execute();
            $result = $stmnt->get_result();

            $users = [];
            while ($user = $result->fetch_object(User::class)) {
                $users[] = $user;
            }
            return $users;
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function readChatsByUser(int $userId): array {
        $conn = (new Connection())->connect();
        try {
            $sql = "
                SELECT c.*
                FROM UserChat uc
                INNER JOIN Chat c ON c.chat_id = uc.chat_id
                WHERE uc.user_id = ?
            ";
            $stmnt = $conn->prepare($sql);
            $stmnt->bind_param("i", $userId);
            $stmnt->execute();
            $result = $stmnt->get_result();

            $chats = [];
            while ($chat = $result->fetch_object(Chat::class)) {
                $chats[] = $chat; // cada elemento es un objeto Chat
            }
            return $chats;
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function read(int $userId, int $chatId): ?UserChat {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare(
                "SELECT * FROM UserChat WHERE user_id = ? AND chat_id = ?"
            );
            $stmnt->bind_param("ii", $userId, $chatId);
            $stmnt->execute();
            $result = $stmnt->get_result();

            return $result->fetch_object(UserChat::class) ?: null;
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function update(int $userId, int $chatId) {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare(
                "UPDATE UserChat SET
                    role = ?
                WHERE user_id = ? AND chat_id = ?"
            );
            $stmnt->bind_param(
                "sii",
                $this->role,
                $userId,
                $chatId
            );
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function delete(int $userId, int $chatId): bool {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare("DELETE FROM UserChat WHERE user_id = ? AND chat_id = ?");
            $stmnt->bind_param("ii", $userId, $chatId);
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }
}
?>