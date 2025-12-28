<?php
// Use env vars for credentials; fall back to sane defaults for local dev.
$dbHost = getenv('WORKMATE_DB_HOST') ?: '127.0.0.1';
$dbUser = getenv('WORKMATE_DB_USER') ?: 'root';
$dbPass = getenv('WORKMATE_DB_PASS') ?: '11135984';
$dbName = getenv('WORKMATE_DB_NAME') ?: 'monitor';

// Dynamically set apiSecret based on current host (without port) if not in env
$rawHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$host = preg_replace('/:\d+$/', '', $rawHost); // Remove port if present
$apiSecret = getenv('WORKMATE_API_SECRET') ?: base64_encode($host);
?>