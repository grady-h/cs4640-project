<?php
require_once(__DIR__ . "/db/Config.php");
require_once(__DIR__ . "/db/Database.php");
require_once(__DIR__ . "/db/models/UserModel.php");
require_once(__DIR__ . "/db/models/ApplicationModel.php");
require_once(__DIR__ . "/db/models/OAModel.php");
require_once(__DIR__ . "/db/models/InterviewModel.php");

class Controller {
    private $db;

    public function __construct() {
        session_start();
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }
        $this->db = new Database();
        header('Content-Type: application/json');
    }

    private function requireLogin() {
        if (empty($_SESSION['user_id'])) {
            echo json_encode(['success'=>false,'message'=>'Authentication required']);
            exit;
        }
        return (int)$_SESSION['user_id'];
    }

    private function checkCsrfIfPost() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf'] ?? '';
            if (!$token || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
                echo json_encode(['success'=>false,'message'=>'Invalid CSRF token']);
                exit;
            }
        }
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? $_POST['action'] ?? null;
        $this->checkCsrfIfPost();

        switch ($action) {
            case 'signup': return $this->signup();
            case 'login':  return $this->login();
            case 'logout': return $this->logout();
            case 'whoami': return $this->whoami();
            case 'csrf':   return $this->csrf();

            case 'listApps':   return $this->listApps();
            case 'addApp':     return $this->addApp();
            case 'deleteApp':  return $this->deleteApp();

            case 'listOAs':    return $this->listOAs();
            case 'addOA':      return $this->addOA();
            case 'deleteOA':   return $this->deleteOA();

            case 'listInterviews':   return $this->listInterviews();
            case 'addInterview':     return $this->addInterview();
            case 'deleteInterview':  return $this->deleteInterview();

            case 'updateApp':        return $this->updateApp();
            case 'updateOA':         return $this->updateOA();
            case 'updateInterview':  return $this->updateInterview();

            default:
                echo json_encode(['success'=>false,'message'=>'Invalid or missing action']);
        }
    }

    private function signup() {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        echo json_encode(UserModel::create($this->db, $name, $email, $password));
    }
    private function login() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        echo json_encode(UserModel::login($this->db, $email, $password));
    }
    private function logout() {
        session_destroy();
        session_start();
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
        echo json_encode(['success'=>true]);
    }
    private function whoami() {
        if (empty($_SESSION['user_id'])) {
            echo json_encode(['loggedIn'=>false]);
        } else {
            echo json_encode(['loggedIn'=>true, 'name'=>$_SESSION['user_name'], 'user_id'=>$_SESSION['user_id']]);
        }
    }
    private function csrf() { echo json_encode(['csrf'=>$_SESSION['csrf'] ?? '']); }

    private function listApps() {
        $userId = $this->requireLogin();
        $rows = ApplicationModel::listByUser($this->db, $userId);
        echo json_encode(['success'=>true,'rows'=>$rows]);
    }
    private function addApp() {
        $userId = $this->requireLogin();
        $company = trim($_POST['company'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $date = trim($_POST['date'] ?? '');
        $status = trim($_POST['status'] ?? 'Submitted');
        $out = ApplicationModel::create($this->db, $userId, $company, $role, $date, $status);
        if (!headers_sent() && !empty($company) && !empty($role)) {
            setcookie('lastCompany', $company, time()+60*60*24*30, '/', '', false, true);
            setcookie('lastRole', $role, time()+60*60*24*30, '/', '', false, true);
        }
        echo json_encode($out);
    }
    private function deleteApp() {
        $userId = $this->requireLogin();
        $id = (int)($_POST['id'] ?? 0);
        echo json_encode(ApplicationModel::delete($this->db, $userId, $id));
    }

    private function listOAs() {
        $userId = $this->requireLogin();
        $rows = OAModel::listByUser($this->db, $userId);
        echo json_encode(['success'=>true,'rows'=>$rows]);
    }
    private function addOA() {
        $userId = $this->requireLogin();
        $company = trim($_POST['company'] ?? '');
        $date = trim($_POST['received'] ?? ''); 
        $status = trim($_POST['status'] ?? 'Pending');
        echo json_encode(OAModel::create($this->db, $userId, $company, $date, $status));
    }
    private function deleteOA() {
        $userId = $this->requireLogin();
        $id = (int)($_POST['id'] ?? 0);
        echo json_encode(OAModel::delete($this->db, $userId, $id));
    }

    private function listInterviews() {
        $userId = $this->requireLogin();
        $rows = InterviewModel::listByUser($this->db, $userId);
        echo json_encode(['success'=>true,'rows'=>$rows]);
    }
    private function addInterview() {
        $userId = $this->requireLogin();
        $company = trim($_POST['company'] ?? '');
        $stage = trim($_POST['stage'] ?? 'Phone Screen');
        $date = trim($_POST['date'] ?? '');
        $result = trim($_POST['result'] ?? 'Pending');
        echo json_encode(InterviewModel::create($this->db, $userId, $company, $stage, $date, $result));
    }
    private function deleteInterview() {
        $userId = $this->requireLogin();
        $id = (int)($_POST['id'] ?? 0);
        echo json_encode(InterviewModel::delete($this->db, $userId, $id));
    }

    private function updateApp() {
        $userId = $this->requireLogin();
        $id = (int)($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        echo json_encode(ApplicationModel::updateStatus($this->db, $userId, $id, $status));
    }

    private function updateOA() {
        $userId = $this->requireLogin();
        $id = (int)($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        echo json_encode(OAModel::updateStatus($this->db, $userId, $id, $status));
    }

    private function updateInterview() {
        $userId = $this->requireLogin();
        $id = (int)($_POST['id'] ?? 0);
        $result = trim($_POST['result'] ?? '');
        echo json_encode(InterviewModel::updateResult($this->db, $userId, $id, $result));
    }
}

$controller = new Controller();
$controller->handleRequest();