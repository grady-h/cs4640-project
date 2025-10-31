<?php

// class server paths
require_once(__DIR__ . "/Config.php");
require_once(__DIR__ . "/Database.php");

// TODO: design db tables

try {
    $db = new Database();

    // Drop existing tables
    $db->query("DROP TABLE IF EXISTS users CASCADE;");
    $db->query("DROP TABLE IF EXISTS problems CASCADE;");
    $db->query("DROP TABLE IF EXISTS applications CASCADE;");
    $db->query("DROP TABLE IF EXISTS oas CASCADE;");
    $db->query("DROP TABLE IF EXISTS interviews CASCADE;");

    // users table
    $db->query("
        CREATE TABLE users (
            id SERIAL PRIMARY KEY,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL
        );
    ");

    // leetcode problems table
    $db->query("
        CREATE TABLE problems (
            id SERIAL PRIMARY KEY,
            user_id INT REFERENCES users(id) ON DELETE CASCADE,
            title TEXT NOT NULL,
            topic TEXT,
            difficulty TEXT CHECK (difficulty IN ('Easy','Medium','Hard')),
            status TEXT CHECK (status IN ('Unsolved','Solved','Review')) DEFAULT 'unsolved',
            notes TEXT
        );
    ");

    // applicatinos table
    $db->query("
        CREATE TABLE applications (
            id SERIAL PRIMARY KEY,
            user_id INT REFERENCES users(id) ON DELETE CASCADE,
            company TEXT NOT NULL,
            role TEXT NOT NULL,
            date_applied DATE,
            status TEXT CHECK (status IN ('Submitted','Interview','Offer','Rejected')) DEFAULT 'Submitted'
        );
    ");

    // OAs table
    $db->query("
        CREATE TABLE oas (
            id SERIAL PRIMARY KEY,
            user_id INT REFERENCES users(id) ON DELETE CASCADE,
            company TEXT NOT NULL,
            date_received DATE,
            status TEXT CHECK (status IN ('Pending','Completed','Passed','Failed')) DEFAULT 'Pending'
        );
    ");

    // Interviews table
    $db->query("
        CREATE TABLE interviews (
            id SERIAL PRIMARY KEY,
            user_id INT REFERENCES users(id) ON DELETE CASCADE,
            company TEXT NOT NULL,
            stage TEXT CHECK (stage IN ('Phone Screen','Technical Round','Onsite')),
            date DATE,
            result TEXT CHECK (result IN ('Pending','Pass','Fail')) DEFAULT 'Pending'
        );
    ");

    echo "<p>Database tables created successfully</p>";

} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
