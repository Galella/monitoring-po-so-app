<?php

/**
 * Deployment Helper Script
 * Upload this file to your 'public_html' folder.
 * Access it via browser: http://your-domain.com/deploy_helper.php?key=YOUR_SECRET_KEY
 */

// Define a secret key to prevent unauthorized access
define('SECRET_KEY', 'rahasia123'); // GANTI DENGAN KUNCI YANG LEBIH SULIT!

if (!isset($_GET['key']) || $_GET['key'] !== SECRET_KEY) {
    die('Akses ditolak. Key salah.');
}

// Function to run artisan commands
function run_artisan($command) {
    echo "<h2>Running: php artisan $command</h2>";
    echo "<pre>";
    try {
        // Adjust the path to artisan based on new structure (one level up from public_html)
        // If your structure is different, adjust this path!
        $artisanPath = __DIR__ . '/../monitoring_app/artisan';
        
        if (!file_exists($artisanPath)) {
            throw new Exception("File artisan tidak ditemukan di: $artisanPath");
        }

        // Capture output
        $output = shell_exec("php $artisanPath $command 2>&1");
        echo htmlspecialchars($output);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    echo "</pre><hr>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deployment Helper</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f0f0f0; }
        pre { background: #333; color: #fff; padding: 15px; overflow-x: auto; border-radius: 5px; }
        h2 { color: #333; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Deployment Helper</h1>
    
    <p>
        <a href="?key=<?php echo SECRET_KEY; ?>&cmd=optimize" class="btn">Optimize (Cache All)</a>
        <a href="?key=<?php echo SECRET_KEY; ?>&cmd=storage" class="btn">Link Storage</a>
        <a href="?key=<?php echo SECRET_KEY; ?>&cmd=migrate" class="btn" onclick="return confirm('Yakin mau migrate database?');">Migrate Database</a>
        <a href="?key=<?php echo SECRET_KEY; ?>&cmd=down" class="btn">Maintenance Mode</a>
        <a href="?key=<?php echo SECRET_KEY; ?>&cmd=up" class="btn">Live Mode</a>
    </p>

    <?php
    if (isset($_GET['cmd'])) {
        switch ($_GET['cmd']) {
            case 'optimize':
                run_artisan('config:cache');
                run_artisan('route:cache');
                run_artisan('view:cache');
                run_artisan('event:cache');
                break;
            case 'storage':
                run_artisan('storage:link');
                break;
            case 'migrate':
                run_artisan('migrate --force');
                break;
            case 'down':
                run_artisan('down --secret=bypass123');
                echo "Bypass link: http://your-domain.com/bypass123";
                break;
            case 'up':
                run_artisan('up');
                break;
            default:
                echo "Command unknown.";
        }
    }
    ?>
    
    <p style="margin-top: 50px; color: red;">PENTING: Hapus file ini setelah selesai digunakan!</p>
</body>
</html>
