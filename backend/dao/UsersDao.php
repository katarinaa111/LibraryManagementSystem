<?php
require_once 'BaseDao.php';

class UserDao extends BaseDao {
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "users";
        parent::__construct($this->table_name);
    }

    public function getByEmail($email) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getUsersByRole($role) {
        $query = "SELECT * FROM users WHERE role = :role";
        return $this->query($query, ['role' => $role]);
    }

    public function getUserById($id) {
        $id = intval($id);
        if ($id <= 0) return null;
        $stmt = $this->connection->prepare("SELECT id, username, email, firstName, lastName, role FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();
        if ($user) {
            return $user;
        }
        return null;
    }
}
?>
