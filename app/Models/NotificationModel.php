<?php
require_once __DIR__ . "/../Libraries/Connection.php";

class Notification {
    public int $notification_id;
    public int $chat_id;
    public int $user_id;
    public bool $status;
    public string $created_at;

    public function create(): bool {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare(
                "INSERT INTO Notification (
                    chat_id,
                    user_id,
                    status
                ) VALUES (?, ?, ?)"
            );
            $stmnt->bind_param(
                "iii",
                $this->chat_id,
                $this->user_id,
                $this->status
            );
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function read(int $id): ?Notification {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare("SELECT * FROM Notification WHERE notification_id = ?");
            $stmnt->bind_param("i", $id);
            $stmnt->execute();
            $result = $stmnt->get_result();
            return $result->fetch_object(Notification::class) ?: null;
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function readByUser(int $userId): array {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare("SELECT * FROM Notification WHERE user_id = ? ORDER BY created_at DESC");
            $stmnt->bind_param("i", $userId);
            $stmnt->execute();
            $result = $stmnt->get_result();

            $notifications = [];
            while ($notif = $result->fetch_object(Notification::class)) {
                $notifications[] = $notif;
            }
            return $notifications;
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function updateStatus(int $id): bool {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare("UPDATE Notification SET status = ? WHERE notification_id = ?");
            $stmnt->bind_param("ii", $this->status, $id);
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function delete(int $id): bool {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare("DELETE FROM Notification WHERE notification_id = ?");
            $stmnt->bind_param("i", $id);
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }
}
?>