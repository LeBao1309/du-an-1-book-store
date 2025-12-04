<?php
// Clear PHP opcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ Opcache cleared!<br>";
} else {
    echo "ℹ️ Opcache not enabled<br>";
}

// Clear realpath cache
clearstatcache(true);
echo "✅ Realpath cache cleared!<br>";

echo "<br><a href='index.php'>← Back to homepage</a>";
