<?php
header('Content-Type: application/json');

// اسم ملف قاعدة بيانات SQLite
$dbFile = 'judgments.db'; // تأكد من أن هذا هو اسم ملف قاعدة البيانات الفعلي

try {
    // الاتصال بقاعدة بيانات SQLite
    $db = new PDO("sqlite:" . __DIR__ . "/" . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // تنفيذ الاستعلام
    $stmt = $db->query("SELECT * FROM judgments");

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>