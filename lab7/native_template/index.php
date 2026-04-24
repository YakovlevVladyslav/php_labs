<?php
/**
 * Main dashboard for the Conflict Manager.
 * Displays the entry form and a sortable table of stored conflict data.
 */
declare(strict_types=1);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Conflict Tracker</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; background: #f4f4f9; }
        form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        div { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="checkbox"] { width: auto; }
        button { background: #2ecc71; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; }
        .result { background: #e8f4fd; padding: 15px; border-left: 5px solid #3498db; margin-top: 20px; }
    </style>
</head>
<body>

    <h1>Conflict Data Entry</h1>
    <?php include 'templates/conflictInputForm.php'; ?>
    

<?php if (isset($_GET['error'])): ?>
    <script>
        alert("<?php echo htmlspecialchars($_GET['error']); ?>");
    </script>
<?php endif; ?>

<?php
$conflicts = [];
$folder = 'conflicts';
if (is_dir($folder)) {
    $files = glob($folder . '/*.json');
    foreach ($files as $file) {
        if (basename($file) === 'last_id.json') continue;
        
        $data = json_decode(file_get_contents($file), true);
        if ($data) {
            $conflicts[] = $data;
        }
    }
}

// Теперь подключаем шаблон
include 'templates/conflict_list.php';
?>


</body>
</html>
