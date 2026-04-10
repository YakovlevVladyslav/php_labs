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

    <form method="POST" action="/conflictFormHandler.php">
        <div>
            <label>Title:</label>
            <input type="text" name="title" required>
        </div>
        <div>
            <label>Description:</label>
            <textarea name="description" required></textarea>
        </div>
        <div>
            <label>Starting Date:</label>
            <input type="date" name="startingDate" required>
        </div>
        <div>
            <label>Nuclear Involvement:</label>
            <select name="nuclearWeapon">
                <option value="yes">Yes</option>
                <option value="yes, but not officially">Yes, but not officially</option>
                <option value="not yet">Not yet</option>
            </select>
        </div>
        <div>
            <label>
                <input type="checkbox" name="isActive" value="1"> Active Conflict
            </label>
        </div>
        <button type="submit">Initialize Conflict Object</button>
    </form>

<?php if (isset($_GET['error'])): ?>
    <script>
        alert("<?php echo htmlspecialchars($_GET['error']); ?>");
    </script>
<?php endif; ?>

<?php
    $folder = 'conflicts';

    if (is_dir($folder)) {
        $files = glob($folder . '/*.json');
        $conflictFiles = array_filter($files, function($file) {
            return basename($file) !== 'last_id.json';
        });

        if (!empty($conflictFiles)) {
            echo '<div class="result">';
            echo '<h2>Stored Conflicts</h2>';
            // Added an ID to the table for JavaScript targeting
            echo '<table id="conflictTable" border="1" style="width:100%; border-collapse: collapse; text-align: left;">';
            echo '<tr style="background-color: #eee;">
                    <th style="padding: 8px; cursor: pointer;" onclick="sortTable(0)">ID <span>▼</span></th>
                    <th style="padding: 8px;">Title</th>
                    <th style="padding: 10px; cursor: pointer;" onclick="sortTable(2)">Date <span>▼</span></th>
                    <th style="padding: 8px;">Nuclear</th>
                    <th style="padding: 8px;">Status</th>
                  </tr>';

            foreach ($conflictFiles as $file) {
                $jsonData = file_get_contents($file);
                $conflict = json_decode($jsonData, true);

                if ($conflict) {
                    $status = $conflict['active'] ? 'Active' : 'Resolved';
                    echo '<tr>';
                    echo '<td style="padding: 8px;">' . htmlspecialchars((string)$conflict['id']) . '</td>';
                    echo '<td style="padding: 8px;">' . htmlspecialchars($conflict['title']) . '</td>';
                    echo '<td style="padding: 8px;">' . htmlspecialchars($conflict['date']) . '</td>';
                    echo '<td style="padding: 8px;">' . htmlspecialchars($conflict['nuclear']) . '</td>';
                    echo '<td style="padding: 8px;">' . $status . '</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
            echo '</div>';
        }
    }
?>

<script>
function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("conflictTable");
  switching = true;
  dir = "asc"; 
  
  while (switching) {
    switching = false;
    rows = table.rows;
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      
      // Check if we are sorting by ID (number) or Date (string)
      var xValue = n === 0 ? Number(x.innerHTML) : x.innerHTML.toLowerCase();
      var yValue = n === 0 ? Number(y.innerHTML) : y.innerHTML.toLowerCase();

      if (dir == "asc") {
        if (xValue > yValue) {
          shouldSwitch = true;
          break;
        }
      } else if (dir == "desc") {
        if (xValue < yValue) {
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchcount ++;      
    } else {
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
</script>

</body>
</html>
