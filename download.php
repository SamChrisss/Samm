<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?error=1");
    exit;
}

$schoolId     = $_POST['school_id'] ?? "";
$pendampingId = $_POST['pendamping_id'] ?? "";
$kodeInput    = $_POST['kode_verifikasi'] ?? "";

if (!preg_match('/^[0-9]{4}$/', $kodeInput)) {
    header("Location: index.php?error=1");
    exit;
}

$data = require __DIR__ . "/secure-data/data_sekolah_secure.php";

if (!isset($data[$schoolId])) {
    header("Location: index.php?error=1");
    exit;
}

if (!isset($data[$schoolId]['pendamping'][$pendampingId])) {
    header("Location: index.php?error=1");
    exit;
}

$entry = $data[$schoolId]['pendamping'][$pendampingId];

if (!hash_equals($entry['kode_verifikasi'], $kodeInput)) {
    header("Location: index.php?error=1");
    exit;
}

$pdf  = $entry['pdf_file'];
$path = __DIR__ . "/pdf_files/" . $pdf;

if (!is_file($path)) {
    header("Location: index.php?error=1");
    exit;
}

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"" . basename($pdf) . "\"");
header("Content-Length: " . filesize($path));

readfile($path);
exit;
