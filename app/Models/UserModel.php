<?php
require_once __DIR__ . "/../Libraries/Connection.php";

class User {
    public int $user_id;
    public string $email;
    public string $name;
    public string $password;
    public ?string $photo;
    public ?string $description;
    public ?string $username;
    public ?string $phone;
    public ?string $status;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $last_login;

    public function create(): ?int {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare(
                "INSERT INTO User (
                    email,
                    name, 
                    password,
                    photo,
                    description,
                    username,
                    phone
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
            $stmnt->bind_param(
                "sssssss",
                $this->email,
                $this->name,
                $this->password,
                $this->photo,
                $this->description,
                $this->username,
                $this->phone
            );
            if($stmnt->execute()){
                return $conn->insert_id;
            }
            return null;
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function read(int $id): ?User {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare("SELECT * FROM User WHERE user_id = ?");
            $stmnt->bind_param("i", $id);
            $stmnt->execute();
            $result = $stmnt->get_result();
            return $result->fetch_object(User::class) ?: null;
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function update(int $id) {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare(
                "UPDATE User SET
                    email = ?,
                    name = ?,
                    password = ?,
                    photo = ?,
                    description = ?,
                    username = ?,
                    phone = ?,
                    status = ?
                WHERE user_id = ?"
            );
            $stmnt->bind_param(
                "ssssssssi",
                $this->email,
                $this->name,
                $this->password,
                $this->photo,
                $this->description,
                $this->username,
                $this->phone,
                $this->status,
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
            $stmnt = $conn->prepare("DELETE FROM User WHERE user_id = ?");
            $stmnt->bind_param("i", $id);
            return $stmnt->execute();
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }

    public function readByUserAndPassword(): ?User {
        $conn = (new Connection())->connect();
        try {
            $stmnt = $conn->prepare(
                "SELECT * FROM User WHERE (email = ?)"
            );
            $stmnt->bind_param(
                "s",
                $this->email
            );
            $result = $stmnt->get_result();
            return $result->fetch_object(User::class) ?: null;
        } finally {
            $stmnt->close();
            $conn->close();
        }
    }
}
