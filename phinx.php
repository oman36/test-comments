<?php
$lines = file(__DIR__ . "/.env");
$env = [];
foreach ($lines as $line) {
    if (empty($line)) {
        continue;
    }
    if (false === strpos($line,"=")) {
        continue;
    }
    list($key,$value) = explode("=",$line);
    $env[$key] = trim($value);
}
$return = [
    "paths" => [
        "migrations" => __DIR__ . "/db/migrations",
        "seeds" => __DIR__ . "/db/seeds",
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database" => "dev",
        "dev" => [
            "adapter" => "mysql",
            "host" => $env["MYSQL_HOST"],
            "name" => $env["MYSQL_DATABASE"],
            "user" => $env["MYSQL_USER"],
            "pass" => $env["MYSQL_PASS"],
            "charset" => "utf8",
            "port" => $env["MYSQL_PORT"],
        ]
    ]
];
foreach ($return['paths'] as $path) {
    if (!file_exists($path)) {
        mkdir($path,0777,1);
    }
}
return $return;
