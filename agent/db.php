<?php
// Use env vars for credentials; fall back to sane defaults for local dev.
$dbHost = getenv('WORKMATE_DB_HOST') ?: 'localhost';
$dbUser = getenv('WORKMATE_DB_USER') ?: 'workmate_admn';
$dbPass = getenv('WORKMATE_DB_PASS') ?: ']H}gX{)XGnAf';
$dbName = getenv('WORKMATE_DB_NAME') ?: 'workmatedb';

// Dynamically set apiSecret based on current host (without port) if not in env
$rawHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$host = preg_replace('/:\d+$/', '', $rawHost); // Remove port if present
$apiSecret = getenv('WORKMATE_API_SECRET') ?: base64_encode($host);
?>