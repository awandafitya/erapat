<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);
$input_text = escapeshellarg($data["text"]);

// Jalankan skrip Python dengan IndoBART atau T5
$command = "python3 summarize.py " . $input_text;
$summary = shell_exec($command);

echo json_encode(["summary" => trim($summary)]);

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
