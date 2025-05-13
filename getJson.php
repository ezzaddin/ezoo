<?php
header('Content-Type: application/json');

$dbFile = 'judgments.db';

try {
    $db = new PDO("sqlite:" . __DIR__ . "/" . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // استعلام لإحضار البيانات المطلوبة
    $sql = "SELECT governorate, court, case_type, subtype 
            FROM judgments 
            WHERE governorate IS NOT NULL AND governorate != ''
              AND court IS NOT NULL AND court != ''
              AND case_type IS NOT NULL AND case_type != ''
              AND subtype IS NOT NULL AND subtype != ''";

    $stmt = $db->query($sql);
    $result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $gov = $row['governorate'];
        $court = $row['court'];
        $caseType = $row['case_type'];
        $subType = $row['subtype'];

        // بناء الهيكل الشجري
        if (!isset($result[$gov])) {
            $result[$gov] = [];
        }

        if (!isset($result[$gov][$court])) {
            $result[$gov][$court] = [];
        }

        if (!isset($result[$gov][$court][$caseType])) {
            $result[$gov][$court][$caseType] = [];
        }

        if (!in_array($subType, $result[$gov][$court][$caseType])) {
            $result[$gov][$court][$caseType][] = $subType;
        }
    }

    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
