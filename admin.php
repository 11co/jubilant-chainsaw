<?php
session_start();

$ADMIN_USER = "petrov";
$ADMIN_PASS = "viosbaba31";

$DATA_FILE = __DIR__ . "/keys.json";

// JSON oku
function load_keys() {
    global $DATA_FILE;
    if (!file_exists($DATA_FILE)) {
        file_put_contents($DATA_FILE, json_encode([]));
    }
    return json_decode(file_get_contents($DATA_FILE), true);
}

// JSON kaydet
function save_keys($keys) {
    global $DATA_FILE;
    file_put_contents($DATA_FILE, json_encode($keys, JSON_PRETTY_PRINT));
}

// Giriş kontrolü
if (!isset($_SESSION['logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['username'] === $ADMIN_USER && $_POST['password'] === $ADMIN_PASS) {
            $_SESSION['logged_in'] = true;
            header("Location: admin.php");
            exit;
        } else {
            $error = "Yanlış kullanıcı adı veya şifre!";
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Giriş</title>
        <style>
            body { font-family: Arial; background:#111; color:#fff; display:flex; justify-content:center; align-items:center; height:100vh; }
            .login { background:#222; padding:20px; border-radius:12px; width:300px; text-align:center; }
            input { width:90%; padding:10px; margin:10px 0; border:none; border-radius:8px; }
            button { background:#0d6efd; color:#fff; padding:10px 20px; border:none; border-radius:8px; cursor:pointer; }
            button:hover { background:#0b5ed7; }
        </style>
    </head>
    <body>
        <div class="login">
            <h2>🔐 Admin Giriş</h2>
            <?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Kullanıcı Adı" required><br>
                <input type="password" name="password" placeholder="Şifre" required><br>
                <button type="submit">Giriş</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Key silme
if (isset($_GET['delete'])) {
    $delKey = $_GET['delete'];
    $keys = load_keys();
    unset($keys[$delKey]);
    save_keys($keys);
    header("Location: admin.php");
    exit;
}

// Key oluşturma
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $amount = (int)$_POST['amount'];
    $mask = $_POST['mask'];
    $duration_type = $_POST['duration_type'];
    $duration_value = $_POST['duration_value'];

    $keys = load_keys();

    for ($i = 0; $i < $amount; $i++) {
        $key = generateKey($mask);
        $keys[$key] = [
            "status" => "ok",
            "created" => time(),
            "duration_type" => $duration_type,
            "duration_value" => $duration_value,
            "expires" => null,
            "ips" => []
        ];
    }
    save_keys($keys);
}

// Key üretme fonksiyonu
function generateKey($mask) {
    $out = "";
    foreach (str_split($mask) as $ch) {
        if ($ch === "*") {
            $out .= strtoupper(substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 1));
        } else {
            $out .= $ch;
        }
    }
    return $out;
}

$keys = load_keys();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial; background:#111; color:#fff; padding:20px; }
        .box { background:#222; padding:20px; border-radius:12px; margin-bottom:20px; }
        input, select { padding:8px; margin:5px; border:none; border-radius:6px; }
        button { background:#0d6efd; color:#fff; padding:8px 15px; border:none; border-radius:6px; cursor:pointer; }
        button:hover { background:#0b5ed7; }
        table { width:100%; border-collapse:collapse; margin-top:15px; }
        th, td { padding:10px; text-align:left; border-bottom:1px solid #444; }
        .delete { background:#dc3545; }
        .delete:hover { background:#bb2d3b; }
    </style>
</head>
<body>

<h1>📊 Admin Panel</h1>

<div class="box">
    <h3>Yeni Key Oluştur</h3>
    <form method="POST">
        <input type="hidden" name="create" value="1">
        <label>Adet:</label><input type="number" name="amount" value="1" min="1"><br>
        <label>Mask:</label><input type="text" name="mask" value="****-****-****"><br>
        <label>Süre Türü:</label>
        <select name="duration_type">
            <option value="lifetime">Lifetime</option>
            <option value="years">Yıl</option>
            <option value="months">Ay</option>
            <option value="days">Gün</option>
            <option value="hours">Saat</option>
            <option value="minutes">Dakika</option>
            <option value="seconds">Saniye</option>
        </select>
        <input type="number" name="duration_value" value="1" min="1"><br>
        <button type="submit">Key Oluştur</button>
    </form>
</div>

<div class="box">
    <h3>Mevcut Keyler</h3>
    <table>
        <tr><th>Key</th><th>Durum</th><th>Süre</th><th>Oluşturma</th><th>İşlem</th></tr>
        <?php foreach ($keys as $k => $v): ?>
            <tr>
                <td><?= htmlspecialchars($k) ?></td>
                <td><?= $v['status'] ?></td>
                <td><?= $v['duration_type'] ?> <?= $v['duration_value'] ?></td>
                <td><?= date("Y-m-d H:i", $v['created']) ?></td>
                <td><a href="?delete=<?= urlencode($k) ?>"><button class="delete">Sil</button></a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
