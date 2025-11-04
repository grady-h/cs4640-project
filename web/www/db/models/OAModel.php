<?php
class OAModel {
    private static $allowed = ['Pending','Completed','Passed','Failed'];

    private static function normalizeStatus($s) {
        $s = trim($s);
        $map = [
            'Completed - Pass' => 'Passed',
            'Completed – Pass' => 'Passed',
            'Completed - Fail' => 'Failed',
            'Completed – Fail' => 'Failed',
        ];
        return $map[$s] ?? $s;
    }


    private static function validate($company, $date, $status) {
        if (!preg_match("/^[A-Za-z0-9 .,&'()\\-]{2,80}$/", $company)) return "Invalid company name.";
        if ($date !== '' && $date !== null) {
            if (!preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $date)) return "Date must be YYYY-MM-DD.";
            [$y,$m,$d] = array_map('intval', explode('-', $date));
            if (!checkdate($m,$d,$y)) return "Invalid calendar date.";
        }
        if (!in_array(self::normalizeStatus($status), self::$allowed, true)) return "Invalid status.";
        return null;
    }

    public static function listByUser($db, $userId) {
        return $db->query("SELECT id, company, date_received, status
                           FROM oas
                           WHERE user_id = $1
                           ORDER BY date_received NULLS LAST, id DESC", $userId) ?? [];
    }

    public static function create($db, $userId, $company, $date, $status) {
        $status = self::normalizeStatus($status);
        $err = self::validate($company, $date, $status);
        if ($err) return ['success' => false, 'message' => $err];

        $res = $db->query(
            "INSERT INTO oas (user_id, company, date_received, status)
             VALUES ($1,$2,$3,$4)
             RETURNING id, company, date_received, status",
            $userId, $company, ($date ?: null), $status
        );
        if (!$res) return ['success' => false, 'message' => 'Database insert failed'];
        return ['success' => true, 'row' => $res[0]];
    }

    public static function delete($db, $userId, $id) {
        $res = $db->query("DELETE FROM oas WHERE id = $1 AND user_id = $2 RETURNING id", $id, $userId);
        if (!$res || count($res) === 0) return ['success' => false, 'message' => 'Not found or not allowed'];
        return ['success' => true];
    }

    public static function updateStatus($db, $userId, $id, $status) {
        $status = self::normalizeStatus($status);
        if (!in_array($status, self::$allowed, true)) {
            return ['success' => false, 'message' => 'Invalid status.'];
        }
        $res = $db->query(
            "UPDATE oas SET status = $1 WHERE id = $2 AND user_id = $3
            RETURNING id, company, date_received, status",
            $status, $id, $userId
        );
        if (!$res || count($res) === 0) return ['success' => false, 'message' => 'Not found or not allowed'];
        return ['success' => true, 'row' => $res[0]];
    }

}
