<?php
header('Content-Type: application/json');

// اسم ملف قاعدة بيانات SQLite
$dbFile = 'judgments.db';

try {
    // الاتصال بقاعدة البيانات باستخدام PDO
    $db = new PDO("sqlite:" . __DIR__ . "/" . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // قراءة الفلاتر من الطلب
    $filters = [];
    $params = [];

    if (!empty($_GET['case_type'])) {
        $filters[] = 'case_type = :case_type';
        $params[':case_type'] = $_GET['case_type'];
    }

    if (!empty($_GET['court'])) {
        $filters[] = 'court = :court';
        $params[':court'] = $_GET['court'];
    }

    if (!empty($_GET['governorate'])) {
        $filters[] = 'governorate = :governorate';
        $params[':governorate'] = $_GET['governorate'];
    }

    if (!empty($_GET['session_date'])) {
        $filters[] = 'session_date = :session_date';
        $params[':session_date'] = $_GET['session_date'];
    }

    // إنشاء الاستعلام
    $sql = "SELECT * FROM judgments";
    if (!empty($filters)) {
        $sql .= " WHERE " . implode(" AND ", $filters);
    }

    // تحضير وتنفيذ الاستعلام
    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
