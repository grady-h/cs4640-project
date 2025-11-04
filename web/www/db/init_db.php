<?php
require_once(__DIR__ . "/Config.php");
require_once(__DIR__ . "/Database.php");

try {
    $db = new Database();

    $schema = Config::$db['schema'] ?? Config::$db['user'];
    $schema = preg_replace('/[^A-Za-z0-9_]/', '', $schema);

    $db->query("DROP TABLE IF EXISTS {$schema}.applications CASCADE;");
    $db->query("DROP TABLE IF EXISTS {$schema}.oas CASCADE;");
    $db->query("DROP TABLE IF EXISTS {$schema}.interviews CASCADE;");
    $db->query("DROP TABLE IF EXISTS {$schema}.problems CASCADE;");
    $db->query("DROP TABLE IF EXISTS {$schema}.users CASCADE;");

    $db->query("
        CREATE TABLE IF NOT EXISTS {$schema}.users (
            id SERIAL PRIMARY KEY,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL
        );
    ");

    $db->query("
        CREATE TABLE IF NOT EXISTS {$schema}.problems (
            id SERIAL PRIMARY KEY,
            user_id INT REFERENCES {$schema}.users(id) ON DELETE CASCADE,
            title TEXT NOT NULL,
            topic TEXT,
            difficulty TEXT CHECK (difficulty IN ('Easy','Medium','Hard')),
            status TEXT CHECK (status IN ('Unsolved','Solved','Review')) DEFAULT 'Unsolved',
            notes TEXT
        );
    ");

    $db->query("
        CREATE TABLE IF NOT EXISTS {$schema}.applications (
            id SERIAL PRIMARY KEY,
            user_id INT REFERENCES {$schema}.users(id) ON DELETE CASCADE,
            company TEXT NOT NULL,
            role TEXT NOT NULL,
            date_applied DATE,
            status TEXT CHECK (status IN ('Submitted','In Review','Interview','Offer','Rejected')) DEFAULT 'Submitted'
        );
    ");

    $db->query("
        CREATE TABLE IF NOT EXISTS {$schema}.oas (
            id SERIAL PRIMARY KEY,
            user_id INT REFERENCES {$schema}.users(id) ON DELETE CASCADE,
            company TEXT NOT NULL,
            date_received DATE,
            status TEXT CHECK (status IN ('Pending','Completed','Passed','Failed')) DEFAULT 'Pending'
        );
    ");

    $db->query("
        CREATE TABLE IF NOT EXISTS {$schema}.interviews (
            id SERIAL PRIMARY KEY,
            user_id INT REFERENCES {$schema}.users(id) ON DELETE CASCADE,
            company TEXT NOT NULL,
            stage TEXT CHECK (stage IN ('Phone Screen','Technical Round','Onsite')),
            date DATE,
            result TEXT CHECK (result IN ('Pending','Pass','Fail')) DEFAULT 'Pending'
        );
    ");

    echo "<p>Database tables created successfully</p>";
} catch (Exception $e) {
    echo "<p>Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
}
