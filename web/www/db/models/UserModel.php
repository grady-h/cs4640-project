<?php
class UserModel {
    public static function create($db, $name, $email, $password) {
        try {
            // check if jaunt already exists
            $existing = $db->query("SELECT id FROM users WHERE email = $1", $email);

            if ($existing && count($existing) > 0) {
                return ['success' => false, 'message' => 'Email already registered'];
            }

            // has the jaunt
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // insert
            $result = $db->query(
                "INSERT INTO users (name, email, password_hash)
                 VALUES ($1, $2, $3)
                 RETURNING id",
                $name, $email, $hash
            );

            if (!$result) {
                return ['success' => false, 'message' => 'Database insert failed'];
            }

            $id = $result[0]['id'];

            // save in session
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;

            return [
                'success' => true,
                'message' => 'Account created successfully',
                'user_id' => $id
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
?>
