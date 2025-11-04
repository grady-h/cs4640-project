<?php
class UserModel {
    private static function validateEmail($email) {
        if (!preg_match('/^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/', $email)) return false;
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    private static function validatePassword($pw) {
        return preg_match('/^(?=.*[A-Za-z])(?=.*\\d)[A-Za-z\\d!@#$%^&*()_\\-=+]{8,}$/', $pw);
    }

    public static function create($db, $name, $email, $password) {
        try {
            $name = trim($name);
            $email = trim(strtolower($email));
            if ($name === '' || !$email || !$password) return ['success'=>false,'message'=>'All fields required'];
            if (!self::validateEmail($email)) return ['success'=>false,'message'=>'Invalid email format'];
            if (!self::validatePassword($password)) return ['success'=>false,'message'=>'Weak password'];

            $existing = $db->query("SELECT id FROM users WHERE email = $1", $email);
            if ($existing && count($existing) > 0) return ['success'=>false,'message'=>'Email already registered'];

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $result = $db->query(
                "INSERT INTO users (name, email, password_hash) VALUES ($1,$2,$3) RETURNING id, name",
                $name, $email, $hash
            );
            if (!$result) return ['success'=>false,'message'=>'Database insert failed'];

            $_SESSION['user_id'] = $result[0]['id'];
            $_SESSION['user_name'] = $result[0]['name'];

            return ['success'=>true,'message'=>'Account created','user_id'=>$result[0]['id']];
        } catch (Exception $e) {
            return ['success'=>false,'message'=>$e->getMessage()];
        }
    }

    public static function login($db, $email, $password) {
        try {
            $email = trim(strtolower($email));
            $user = $db->query("SELECT id, name, password_hash FROM users WHERE email = $1", $email);
            if (!$user || count($user) === 0) return ['success'=>false,'message'=>'Invalid email or password'];

            $userData = $user[0];
            if (!password_verify($password, $userData['password_hash'])) return ['success'=>false,'message'=>'Invalid email or password'];

            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['user_name'] = $userData['name'];

            return ['success'=>true, 'message'=>'Login successful', 'user_name'=>$userData['name']];
        } catch (Exception $e) {
            return ['success'=>false,'message'=>$e->getMessage()];
        }
    }
}
