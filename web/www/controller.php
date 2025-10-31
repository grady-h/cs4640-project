<?php

require_once(__DIR__ . "/db/Config.php");
require_once(__DIR__ . "/db/Database.php");
require_once(__DIR__ . "/db/models/UserModel.php");
// require_once(__DIR__ . "/db/models/ProblemModel.php");
// require_once(__DIR__ . "/db/models/ApplicationModel.php");
// require_once(__DIR__ . "/db/models/OAModel.php");
// require_once(__DIR__ . "/db/models/InterviewModel.php");


class Controller {
    private $db;

    // do we need parameter(s)?
    public function __construct() {
        session_start();
        $this->db = new Database();
    }

    public function handleRequest() {
        // get the request action
        $action = $_GET['action'] ?? $_POST['action'] ?? null;

        switch ($action) {
            case 'login':
                $this->login();
                break;
            case 'signup':
                $this->signup();
                break;
            default:
                echo json_encode(['error' => 'Invalid or missing action']);
        }
    }

    // will add methods as needed
    private function signup() {
        header('Content-Type: application/json');

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($name === '' || $email === '' || $password === '') {
            echo json_encode(['success' => false, 'message' => 'All fields required']);
            return;
        }

        // do all input validation in the model class
        $result = UserModel::create($this->db, $name, $email, $password);

        // return the jaunt
        echo json_encode($result);
    }

    private function login() {
        header('Content-Type: application/json');

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            echo json_encode(['success' => false, 'message' => 'Email and password are required']);
            return;
        }

        $result = UserModel::login($this->db, $email, $password);

        echo json_encode($result);
    }

}

$controller = new Controller();
$controller->handleRequest();
