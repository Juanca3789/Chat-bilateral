<?php
require_once __DIR__."/../Libraries/Connection.php";

class Chat {
    public int $chat_id;
    public string $title;
    public string $type;
    public string $created_at;
    public int $created_by;

    public function create() {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare(
                "INSERT INTO Chat (
                    title,
                    type, 
                    created_at,
                    created_by
                    ) VALUES (?, ?, ?, ?)"
                );
            $stmnt->bind_param(
                "sssi",
                $this->title,
                $this->type,
                $this->created_at,
                $this->created_by,
            );
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function read(int $id): ?Chat {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare("SELECT * FROM Chat WHERE chat_id = ?");
            $stmnt->bind_param("i", $id);
            $stmnt->execute();
            $result = $stmnt->get_result();
            return $result->fetch_object(Chat::class) ?: null;
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function update(int $id) {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare(
                "UPDATE Chat SET
                    title = ?,
                    type = ?
                WHERE chat_id = ?"
            );
            $stmnt->bind_param(
                "ssi",
                $this->title,
                $this->type,
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
            $stmnt = $conn->prepare("DELETE FROM Chat WHERE chat_id = ?");
            $stmnt->bind_param("i", $id);
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }
}
?>