<?php
header('Content-Type: application/json');

$dbFile = 'judgments.db';

try {
    $db = new PDO("sqlite:" . __DIR__ . "/" . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // الحقول المسموح بها
    $allowedFields = ['governorate', 'court', 'case_type', 'subtype', 'status', 'substatus'];

    // تعيين قيم افتراضية للحقول إذا لم يتم إرسالها
    $field1 ='governorate';
    $field2 =  'court';
    $field3 =  'case_type';
    $field4 =  'subtype';

    // التأكد من أن القيم المدخلة في المتغيرات هي قيم مسموح بها
    foreach ([$field1, $field2, $field3, $field4] as $field) {
        if (!in_array($field, $allowedFields)) {
            // إذا كانت القيمة المدخلة غير صالحة، سيتم استخدام القيم الافتراضية
            $field = 'governorate'; // أو أي قيمة افتراضية أخرى تريدها
        }
    }

    $sql = "SELECT $field1, $field2, $field3, $field4 
            FROM judgments 
            WHERE $field1 IS NOT NULL AND $field1 != ''
              AND $field2 IS NOT NULL AND $field2 != ''
              AND $field3 IS NOT NULL AND $field3 != ''
              AND $field4 IS NOT NULL AND $field4 != ''";

    $stmt = $db->query($sql);
    $tree = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $val1 = $row[$field1];
        $val2 = $row[$field2];
        $val3 = $row[$field3];
        $val4 = $row[$field4];

        // البحث عن العنصر الأعلى (field1)
        $level1 = null;
        foreach ($tree as &$item1) {
            if ($item1[$field1] === $val1) {
                $level1 = &$item1;
                break;
            }
        }
        if (!$level1) {
            $tree[] = [$field1 => $val1, "children" => []];
            $level1 = &$tree[array_key_last($tree)];
        }

        // المستوى الثاني
        $level2 = null;
        foreach ($level1["children"] as &$item2) {
            if ($item2[$field2] === $val2) {
                $level2 = &$item2;
                break;
            }
        }
        if (!$level2) {
            $level1["children"][] = [$field2 => $val2, "children" => []];
            $level2 = &$level1["children"][array_key_last($level1["children"])];
        }

        // المستوى الثالث
        $level3 = null;
        foreach ($level2["children"] as &$item3) {
            if ($item3[$field3] === $val3) {
                $level3 = &$item3;
                break;
            }
        }
        if (!$level3) {
            $level2["children"][] = [$field3 => $val3, "children" => []];
            $level3 = &$level2["children"][array_key_last($level2["children"])];
        }

        // المستوى الأخير (مصفوفة عادية)
        if (!in_array($val4, $level3["children"])) {
            $level3["children"][] = $val4;
        }
    }

    echo json_encode($tree, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
