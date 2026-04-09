<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Support\Facades\DB;

echo "<h2>Database Structure Check</h2>";

try {
    DB::connection()->getPdo();
    echo "<p style='color:green'>✅ Database connected</p>";
    
    $tables = ['student_info', 'employers', 'job_postings', 'applications', 'announcements', 'admin'];
    
    echo "<h3>Tables:</h3><ul>";
    foreach ($tables as $table) {
        if (DB::getSchemaBuilder()->hasTable($table)) {
            $count = DB::table($table)->count();
            echo "<li>✅ $table - $count records</li>";
            
            // Show columns for applications table
            if ($table == 'applications') {
                $columns = DB::select("DESCRIBE applications");
                echo "<ul>";
                foreach ($columns as $col) {
                    echo "<li>{$col->Field} - {$col->Type}</li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<li>❌ $table - MISSING</li>";
        }
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
?>