<?php
echo "<h3>INFORMASI PHP SESSION</h3>";

echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Session save handler: " . ini_get('session.save_handler') . "<br>";
echo "Session save path: " . ini_get('session.save_path') . "<br>";
echo "Session auto start: " . ini_get('session.auto_start') . "<br>";
echo "Session name: " . session_name() . "<br>";
?>