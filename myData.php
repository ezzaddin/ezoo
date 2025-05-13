<?php
header("Content-Type: application/json");

$dbFile = "judgments.db";

try {
    $db = new PDO("sqlite:" . __DIR__ . "/" . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // استقبال البيانات
    $governorate = isset($_GET['governorate']) ? $_GET['governorate'] : '';
    $court = isset($_GET['court']) ? $_GET['court'] : '';
    $case_type = isset($_GET['case_type']) ? $_GET['case_type'] : '';
    $subtype = isset($_GET['subtype']) ? $_GET['subtype'] : '';
    $case_number = isset($_GET['case_number']) ? $_GET['case_number'] : '';
    $case_year = isset($_GET['case_year']) ? $_GET['case_year'] : '';

    // دمج الرقم مع السنة
    $combined_case_number = $case_number . '/' . $case_year ;

    // الاستعلام
    $sql = "SELECT * FROM judgments 
            WHERE governorate = :governorate 
              AND court = :court 
              AND case_type = :case_type 
              AND subtype = :subtype 
              AND case_number = :case_number
            LIMIT 1";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':governorate' => $governorate,
        ':court' => $court,
        ':case_type' => $case_type,
        ':subtype' => $subtype,
        ':case_number' => $combined_case_number
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(['status' => 'success', 'data' => $result]);
    } else {
        echo json_encode(['status' => 'not_found', 'message' => 'No matching case found.']);
    }

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
