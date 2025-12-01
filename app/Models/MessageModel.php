<?php
require_once __DIR__ . "/../Libraries/Connection.php";

class Message {
    public int $message_id;
    public int $chat_id;
    public int $user_id;
    public ?string $content;
    public ?string $attachment_uri;
    public string $created_at;
    public string $updated_at;

    public function create(): bool {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare(
                "INSERT INTO Message (
                    chat_id,
                    user_id,
                    content,
                    attachment_uri
                ) VALUES (?, ?, ?, ?)"
            );
            $stmnt->bind_param(
                "iiss",
                $this->chat_id,
                $this->user_id,
                $this->content,
                $this->attachment_uri
            );
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function read(int $id): ?Message {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare("SELECT * FROM Message WHERE message_id = ?");
            $stmnt->bind_param("i", $id);
            $stmnt->execute();
            $result = $stmnt->get_result();
            return $result->fetch_object(Message::class) ?: null;
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function update(int $id): bool {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare(
                "UPDATE Message SET
                    content = ?,
                    attachment_uri = ?
                WHERE message_id = ?"
            );
            $stmnt->bind_param(
                "ssi",
                $this->content,
                $this->attachment_uri,
                $id
            );
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function delete(int $id): bool {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare("DELETE FROM Message WHERE message_id = ?");
            $stmnt->bind_param("i", $id);
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function readByChat(int $chatId): array {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare("SELECT * FROM Message WHERE chat_id = ? ORDER BY created_at ASC");
            $stmnt->bind_param("i", $chatId);
            $stmnt->execute();
            $result = $stmnt->get_result();

            $messages = [];
            while ($msg = $result->fetch_object(Message::class)) {
                $messages[] = $msg;
            }
            return $messages;
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }
}
?>