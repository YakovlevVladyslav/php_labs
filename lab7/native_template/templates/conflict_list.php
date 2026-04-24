<?php if (!empty($conflicts)): ?>
    <div class="result">
        <h2>Stored Conflicts</h2>
        <table id="conflictTable" border="1" style="width:100%; border-collapse: collapse; text-align: left;">
            <tr style="background-color: #eee;">
                <th style="padding: 8px; cursor: pointer;" onclick="sortTable(0)">ID <span>▼</span></th>
                <th style="padding: 8px;">Title</th>
                <th style="padding: 10px; cursor: pointer;" onclick="sortTable(2)">Date <span>▼</span></th>
                <th style="padding: 8px;">Nuclear</th>
                <th style="padding: 8px;">Status</th>
            </tr>

            <?php foreach ($conflicts as $conflict): ?>
                <tr>
                    <td style="padding: 8px;"><?= htmlspecialchars((string)$conflict['id']) ?></td>
                    <td style="padding: 8px;"><?= htmlspecialchars($conflict['title']) ?></td>
                    <td style="padding: 8px;"><?= htmlspecialchars($conflict['date']) ?></td>
                    <td style="padding: 8px;"><?= htmlspecialchars($conflict['nuclear']) ?></td>
                    <td style="padding: 8px;"><?= $conflict['active'] ? 'Active' : 'Resolved' ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>

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