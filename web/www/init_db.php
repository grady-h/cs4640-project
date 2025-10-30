<?php

// class server paths
require_once(__DIR__ . "/Config.php");
require_once(__DIR__ . "/Database.php");

// docker paths
// require_once("/opt/src/example/Config.php");
// require_once("/opt/src/example/Database.php");

try {
    $db = new Database();

    // Drop existing tables
    $db->query("DROP TABLE IF EXISTS hw3_words CASCADE;");
    $db->query("DROP TABLE IF EXISTS hw3_users CASCADE;");

    // users table TODO: add timestamp?
    $db->query("
        CREATE TABLE users (
            id SERIAL PRIMARY KEY,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL
        );
    ");

    // words table (add timestamp?)
    $db->query("
        CREATE TABLE hw3_words (
            id SERIAL PRIMARY KEY,
            word TEXT NOT NULL,
            user_id INTEGER REFERENCES hw3_users(id) ON DELETE CASCADE,
            score INTEGER DEFAULT 0,
            won BOOLEAN DEFAULT FALSE
        );
    ");

    echo "<p>Database tables created successfully!</p>";

} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
