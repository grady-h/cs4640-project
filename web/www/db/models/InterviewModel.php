<?php
class InterviewModel {
    private static $allowedStages = ['Phone Screen','Technical Round','Onsite'];
    private static $allowedResults = ['Pending','Pass','Fail'];

    private static function validate($company, $stage, $date, $result) {
        if (!preg_match("/^[A-Za-z0-9 .,&'()\\-]{2,80}$/", $company)) return "Invalid company name.";
        if (!in_array($stage, self::$allowedStages, true)) return "Invalid stage.";
        if ($date !== '' && $date !== null) {
            if (!preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $date)) return "Date must be YYYY-MM-DD.";
            [$y,$m,$d] = array_map('intval', explode('-', $date));
            if (!checkdate($m,$d,$y)) return "Invalid calendar date.";
        }
        if (!in_array($result, self::$allowedResults, true)) return "Invalid result.";
        return null;
    }

    public static function listByUser($db, $userId) {
        return $db->query("SELECT id, company, stage, date, result
                           FROM interviews
                           WHERE user_id = $1
                           ORDER BY date NULLS LAST, id DESC", $userId) ?? [];
    }

    public static function create($db, $userId, $company, $stage, $date, $result) {
        $err = self::validate($company, $stage, $date, $result);
        if ($err) return ['success' => false, 'message' => $err];

        $res = $db->query(
            "INSERT INTO interviews (user_id, company, stage, date, result)
             VALUES ($1,$2,$3,$4,$5)
             RETURNING id, company, stage, date, result",
            $userId, $company, $stage, ($date ?: null), $result
        );
        if (!$res) return ['success' => false, 'message' => 'Database insert failed'];
        return ['success' => true, 'row' => $res[0]];
    }

    public static function delete($db, $userId, $id) {
        $res = $db->query("DELETE FROM interviews WHERE id = $1 AND user_id = $2 RETURNING id", $id, $userId);
        if (!$res || count($res) === 0) return ['success' => false, 'message' => 'Not found or not allowed'];
        return ['success' => true];
    }

    public static function updateResult($db, $userId, $id, $result) {
        if (!in_array($result, self::$allowedResults, true)) {
            return ['success' => false, 'message' => 'Invalid result.'];
        }
        $res = $db->query(
            "UPDATE interviews SET result = $1 WHERE id = $2 AND user_id = $3
            RETURNING id, company, stage, date, result",
            $result, $id, $userId
        );
        if (!$res || count($res) === 0) return ['success' => false, 'message' => 'Not found or not allowed'];
        return ['success' => true, 'row' => $res[0]];
    }

}
