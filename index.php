<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
$host = 'localhost';
$db   = 'nevnapok';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["hiba" => "adatbázis kapcsolat sikertelen"]);
    exit;
}

// Ha nincs semmilyen paraméter
if (!isset($_GET['nap']) && !isset($_GET['nev'])) {
    echo json_encode([
        "minta1" => "/?nap=12-31",
        "minta2" => "/?nev=Szilveszter"
    ]);
    $conn->close();
    exit;
}

// ===== DÁTUM alapú keresés =====
if (isset($_GET['nap'])) {
    if (!preg_match('/^\d{1,2}-\d{1,2}$/', $_GET['nap'])) {
        http_response_code(400);
        echo json_encode(["hiba" => "hibás dátumformátum, használat: nap=4-30"]);
        $conn->close();
        exit;
    }

    list($ho, $nap) = explode('-', $_GET['nap']);
    $ho = (int)$ho;
    $nap = (int)$nap;

    $stmt = $conn->prepare("SELECT * FROM nevnap WHERE ho = ? AND nap = ?");
    $stmt->bind_param("ii", $ho, $nap);
    $stmt->execute();
    $result = $stmt->get_result();

    $nevnapok = [];
    while ($row = $result->fetch_assoc()) {
        $nevnapok[] = $row;
    }

    if (empty($nevnapok)) {
        echo json_encode(["hiba" => "nincs találat"]);
    } else {
        echo json_encode($nevnapok);
    }

    $conn->close();
    exit;
}

// ===== NÉV alapú keresés =====
if (isset($_GET['nev'])) {
    $nev = trim($_GET['nev']);

    if ($nev === "") {
        echo json_encode(["hiba" => "üres névparaméter"]);
        $conn->close();
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM nevnap WHERE nev1 = ? OR nev2 = ? LIMIT 1");
    $stmt->bind_param("ss", $nev, $nev);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([$row]);
    } else {
        echo json_encode(["hiba" => "nincs találat"]);
    }

    $conn->close();
    exit;
}
?>
