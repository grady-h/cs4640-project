<?php
class ApplicationModel {
    private static $allowedStatuses = ['Submitted','In Review','Interview','Offer','Rejected'];

    private static function validate($company, $role, $date, $status) {
        $nameRe = "/^[A-Za-z0-9 .,&'()\\-]{2,80}$/";
        if (!preg_match($nameRe, $company)) return "Invalid company name.";
        if (!preg_match($nameRe, $role)) return "Invalid role.";

        if ($date !== '' && $date !== null) {
            if (!preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $date)) return "Date must be YYYY-MM-DD.";
            [$y,$m,$d] = array_map('intval', explode('-', $date));
            if (!checkdate($m,$d,$y)) return "Invalid calendar date.";
        }
        if (!in_array($status, self::$allowedStatuses, true)) return "Invalid status.";
        return null;
    }

    public static function listByUser($db, $userId) {
        return $db->query("SELECT id, company, role, date_applied, status
                           FROM applications
                           WHERE user_id = $1
                           ORDER BY date_applied NULLS LAST, id DESC", $userId) ?? [];
    }

    public static function create($db, $userId, $company, $role, $date, $status) {
        $err = self::validate($company, $role, $date, $status);
        if ($err) return ['success' => false, 'message' => $err];

        $res = $db->query(
            "INSERT INTO applications (user_id, company, role, date_applied, status)
             VALUES ($1,$2,$3,$4,$5)
             RETURNING id, company, role, date_applied, status",
            $userId, $company, $role, ($date ?: null), $status
        );
        if (!$res) return ['success' => false, 'message' => 'Database insert failed'];
        return ['success' => true, 'row' => $res[0]];
    }

    public static function delete($db, $userId, $id) {
        $res = $db->query("DELETE FROM applications WHERE id = $1 AND user_id = $2 RETURNING id", $id, $userId);
        if (!$res || count($res) === 0) return ['success' => false, 'message' => 'Not found or not allowed'];
        return ['success' => true];
    }
    
    public static function updateStatus($db, $userId, $id, $status) {
        if (!in_array($status, self::$allowedStatuses, true)) {
            return ['success' => false, 'message' => 'Invalid status.'];
        }
        $res = $db->query(
            "UPDATE applications SET status = $1 WHERE id = $2 AND user_id = $3
            RETURNING id, company, role, date_applied, status",
            $status, $id, $userId
        );
        if (!$res || count($res) === 0) return ['success' => false, 'message' => 'Not found or not allowed'];
        return ['success' => true, 'row' => $res[0]];
    }

}
