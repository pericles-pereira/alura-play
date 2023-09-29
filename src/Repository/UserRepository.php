<?php

declare(strict_types=1);

namespace Alura\Mvc\Repository;

use Alura\Mvc\Entity\User;
use PDO;

class UserRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function addUser(User $user): bool
    {
        $hash = password_hash($user->password, PASSWORD_ARGON2ID);

        $sql = 'INSERT INTO users (email, password) VALUES (?, ?);';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $user->email);
        $stmt->bindValue(2, $hash);
        $result = $stmt->execute();

        $id = $this->pdo->lastInsertId();

        $user->setUserId(intval($id));

        return $result;
    }

    public function removeUser(int $id): bool
    {
        $sql = 'DELETE FROM users WHERE id = ?';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $id, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function updateUser(User $user): bool
    {
        $hash = password_hash($user->password, PASSWORD_ARGON2ID);

        $sql = "UPDATE users SET email = :email, password = :password WHERE id = :id;";
        $statement = $this->pdo->prepare($sql);

        $statement->bindValue(':url', $user->email);
        $statement->bindValue(':title', $hash);
        $statement->bindValue(':id', $user->id, PDO::PARAM_INT);

        return $statement->execute();
    }

    /**
     * @return User[]
     */
    public function allUsers(): array
    {
        $userList = $this->pdo
            ->query('SELECT * FROM users;')
            ->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(
            $this->hydrateVideo(...),
            $userList
        );
    }

    public function findUserByEmail(string $email): User
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE email = ?;');
        $statement->bindValue(1, $email, \PDO::PARAM_INT);
        $statement->execute();

        return $this->hydrateVideo($statement->fetch(\PDO::FETCH_ASSOC));
    }

    public function findUserById(int $id): User
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE id = ?;');
        $statement->bindValue(1, $id, \PDO::PARAM_INT);
        $statement->execute();

        return $this->hydrateVideo($statement->fetch(\PDO::FETCH_ASSOC));
    }

    private function hydrateVideo(array $userData): User
    {
        $user = new User($userData['email'], $userData['password']);
        $user->setUserId($userData['id']);

        return $user;
    }

    public function userRehash(User $user, $password): bool
    {
        $statement = $this->pdo->prepare('UPDATE users SET password = ? WHERE id = ?;');
        $statement->bindValue(1, password_hash($password, PASSWORD_ARGON2ID));
        $statement->bindValue(2, $user->id);
        
        return $statement->execute();
    }
}
