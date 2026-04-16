<?php
require 'connection/connection.php';
$stmt = $pdo->query("SELECT NOW() as db_time");
$db_time = $stmt->fetch()['db_time'];
echo "PHP Time: " . date('Y-m-d H:i:s') . "\n";
echo "DB Time:  " . $db_time . "\n";
echo "Timezone: " . date_default_timezone_get() . "\n";
?>
