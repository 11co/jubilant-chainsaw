<?php
header("Content-Type: application/json");

$keysFile = __DIR__ . "/keys.json";
if (!file_exists($keysFile)) {
    echo json_encode([
        "status" => "error",
        "msg" => "❌ keys.json bulunamadı"
    ]);
    exit;
}

$keys = json_decode(file_get_contents($keysFile), true);
if (!is_array($keys)) {
    echo json_encode([
        "status" => "error",
        "msg" => "❌ keys.json okunamadı"
    ]);
    exit;
}

$key = $_GET['key'] ?? '';
if (empty($key)) {
    echo json_encode([
        "status" => "error",
        "msg" => "❌ Lisans anahtarı belirtilmedi"
    ]);
    exit;
}

// Key kontrolü
if (!isset($keys[$key])) {
    echo json_encode([
        "status" => "error",
        "msg" => "❌ Geçersiz lisans anahtarı"
    ]);
    exit;
}

$entry = $keys[$key];

// Süre kontrolü
if (!empty($entry['expires']) && time() > $entry['expires']) {
    echo json_encode([
        "status" => "expired",
        "msg" => "⏰ Lisans süresi dolmuş"
    ]);
    exit;
}

// Statü kontrolü
if ($entry['status'] === "banned") {
    echo json_encode([
        "status" => "banned",
        "msg" => "🚫 Lisans banlanmış"
    ]);
    exit;
}
if ($entry['status'] === "frozen") {
    echo json_encode([
        "status" => "frozen",
        "msg" => "🧊 Lisans dondurulmuş"
    ]);
    exit;
}

// Başarılı
echo json_encode([
    "status" => "ok",
    "msg" => "✅ Lisans geçerli"
]);
exit;
