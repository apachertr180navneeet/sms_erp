<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// 检查中央数据库的表
echo "=== 中央数据库 (sms_erp_central) 所有表 ===\n";
$tables = DB::connection('central')->select('SHOW TABLES');
foreach ($tables as $table) {
    $tableArray = (array) $table;
    echo reset($tableArray) . "\n";
}

// 要删除的表（这些只应该存在于租户数据库）
$unwantedTables = ['students', 'teachers', 'school_admins', 'classes', 'sections'];

echo "\n=== 删除不该有的表 ===\n";
foreach ($unwantedTables as $table) {
    try {
        DB::connection('central')->statement("DROP TABLE IF EXISTS $table");
        echo "已删除表: $table\n";
    } catch (\Exception $e) {
        echo "删除表 $table 失败: " . $e->getMessage() . "\n";
    }
}

// 再次检查
echo "\n=== 删除后的表 ===\n";
$tables = DB::connection('central')->select('SHOW TABLES');
foreach ($tables as $table) {
    $tableArray = (array) $table;
    echo reset($tableArray) . "\n";
}
