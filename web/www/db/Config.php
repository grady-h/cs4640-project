<?php
/**
 * Configuration information
 * CS4640 Spring 2024
 *
 * Contains connection information for the local Docker
 * PostgresSQL database.  When uploading your code to the
 * CS4640 server, you can replace this file with another
 * configuration containing connection information found
 * on our course Canvas site.
 */

// docker env config:

class Config {
    public static $db = [
        "host" => "db",
        "port" => 5432,
        "user" => "localuser",
        "pass" => "cs4640LocalUser!",
        "database" => "example"
    ];
}


// class server config:

// TODO: replace with .env variables!! don't put your pass on the github
// class Config {
//     public static $db = [
//         "host" => "localhost",
//         "port" => 5432,
//         "user" => "computing_id",
//         "pass" => "db_password (from canvas)",
//         "database" => "cumputing_id"
//     ];
// }
