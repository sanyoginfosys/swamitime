<?php
/**
 * SWAMITIME SOLUTIONS LTD - Installation Check
 * Run this file to verify your server meets requirements
 */

// Basic check - don't reveal too much in production
$php_version = PHP_VERSION;
$required_php = '8.0.0';
$php_ok = version_compare($php_version, $required_php, '>=');

$pdo_ok = extension_loaded('pdo') && extension_loaded('pdo_mysql');
$mod_rewrite_ok = function_exists('apache_get_modules') ? in_array('mod_rewrite', apache_get_modules()) : null; // Can't always detect
$gd_ok = extension_loaded('gd');
$mbstring_ok = extension_loaded('mbstring');
$json_ok = extension_loaded('json');
$curl_ok = extension_loaded('curl');
$fileinfo_ok = extension_loaded('fileinfo');

$uploads_writable = is_writable(__DIR__ . '/uploads');
$config_exists = file_exists(__DIR__ . '/config.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWAMITIME SOLUTIONS LTD - Installation Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #F5FAFA; font-family: 'Inter', sans-serif; padding: 40px 0; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 24px; }
        .status-pass { color: #078E91; }
        .status-fail { color: #dc3545; }
        .status-warn { color: #f59e0b; }
        .header-logo { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 1.8rem; color: #004E53; }
    </style>
</head>
<body>
<div class="container" style="max-width: 800px;">
    
    <div class="text-center mb-4">
        <h1 class="header-logo">SWAMITIME SOLUTIONS LTD</h1>
        <p class="text-muted">Installation & System Check</p>
    </div>
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">System Requirements</h5>
            <table class="table table-borderless mb-0">
                <tr>
                    <td>PHP Version</td>
                    <td><?php echo $php_version; ?></td>
                    <td class="<?php echo $php_ok ? 'status-pass' : 'status-fail'; ?>">
                        <?php echo $php_ok ? '✓ Pass' : '✗ Requires ' . $required_php . '+'; ?>
                    </td>
                </tr>
                <tr>
                    <td>PDO MySQL</td>
                    <td><?php echo $pdo_ok ? 'Enabled' : 'Not Found'; ?></td>
                    <td class="<?php echo $pdo_ok ? 'status-pass' : 'status-fail'; ?>">
                        <?php echo $pdo_ok ? '✓ Pass' : '✗ Required'; ?>
                    </td>
                </tr>
                <tr>
                    <td>GD Library</td>
                    <td><?php echo $gd_ok ? 'Enabled' : 'Not Found'; ?></td>
                    <td class="<?php echo $gd_ok ? 'status-pass' : 'status-warn'; ?>">
                        <?php echo $gd_ok ? '✓ Pass' : '⚠ Recommended'; ?>
                    </td>
                </tr>
                <tr>
                    <td>cURL</td>
                    <td><?php echo $curl_ok ? 'Enabled' : 'Not Found'; ?></td>
                    <td class="<?php echo $curl_ok ? 'status-pass' : 'status-warn'; ?>">
                        <?php echo $curl_ok ? '✓ Pass' : '⚠ Recommended for AI features'; ?>
                    </td>
                </tr>
                <tr>
                    <td>MBString</td>
                    <td><?php echo $mbstring_ok ? 'Enabled' : 'Not Found'; ?></td>
                    <td class="<?php echo $mbstring_ok ? 'status-pass' : 'status-fail'; ?>"><?php echo $mbstring_ok ? '✓ Pass' : '✗ Required'; ?></td>
                </tr>
                <tr>
                    <td>JSON</td>
                    <td><?php echo $json_ok ? 'Enabled' : 'Not Found'; ?></td>
                    <td class="<?php echo $json_ok ? 'status-pass' : 'status-fail'; ?>"><?php echo $json_ok ? '✓ Pass' : '✗ Required'; ?></td>
                </tr>
                <tr>
                    <td>FileInfo</td>
                    <td><?php echo $fileinfo_ok ? 'Enabled' : 'Not Found'; ?></td>
                    <td class="<?php echo $fileinfo_ok ? 'status-pass' : 'status-warn'; ?>"><?php echo $fileinfo_ok ? '✓ Pass' : '⚠ Recommended'; ?></td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">File System</h5>
            <table class="table table-borderless mb-0">
                <tr>
                    <td>Config File</td>
                    <td><?php echo $config_exists ? 'Found' : 'Missing'; ?></td>
                    <td class="<?php echo $config_exists ? 'status-pass' : 'status-fail'; ?>">
                        <?php echo $config_exists ? '✓ config.php exists' : '✗ Create config.php'; ?>
                    </td>
                </tr>
                <tr>
                    <td>Uploads Directory</td>
                    <td><?php echo $uploads_writable ? 'Writable' : 'Not Writable'; ?></td>
                    <td class="<?php echo $uploads_writable ? 'status-pass' : 'status-fail'; ?>">
                        <?php echo $uploads_writable ? '✓ Writable' : '✗ Set permissions (755 or 777)'; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Next Steps</h5>
            <ol>
                <li>Ensure all checks above pass</li>
                <li>Create a MySQL database and import <code>database.sql</code></li>
                <li>Update database credentials in <code>config.php</code></li>
                <li>Set <code>BASE_URL</code> in <code>config.php</code> to your domain</li>
                <li>Ensure <code>uploads/</code> directory is writable</li>
                <li>Login to admin panel at <code>/admin/login.php</code></li>
                <li><strong>Delete this file (<code>install.php</code>) when setup is complete</strong></li>
            </ol>
            <div class="alert alert-warning">
                <strong>Security:</strong> Delete <code>install.php</code> after setup is complete.
            </div>
        </div>
    </div>
    
</div>
</body>
</html>
