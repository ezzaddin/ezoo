<?php
header('Content-Type: application/json');

$dbFile = 'judgments.db';

try {
    $db = new PDO("sqlite:" . __DIR__ . "/" . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    function getDistinctValues($db, $column) {
        $sql = "SELECT DISTINCT $column FROM judgments WHERE $column IS NOT NULL AND $column != ''";
        $stmt = $db->query($sql);
        $values = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $values[] = $row[$column];
        }
        return $values;
    }

    // إرجاع أسماء المحافظات فقط
    $governorates = getDistinctValues($db, 'governorate');

    // إرجاع البيانات بتنسيق JSON
    echo json_encode(['governorates' => $governorates]);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
