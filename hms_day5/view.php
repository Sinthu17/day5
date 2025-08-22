<?php
include 'config.php';

// --- Search Handling ---
$search = isset($_GET['search']) ? $_GET['search'] : '';

// --- Pagination Setup ---
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// --- Total Records ---
$total_sql = "SELECT COUNT(*) as total FROM entries 
              WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// --- Fetch Records ---
$sql = "SELECT * FROM entries 
        WHERE name LIKE '%$search%' OR email LIKE '%$search%' 
        ORDER BY id DESC 
        LIMIT $start, $limit";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Entries - Your Project</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>All Entries</h2>

    <!-- Success Alert -->
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">Entry submitted successfully!</div>
    <?php endif; ?>


    <!-- Search Form -->
    <form method="GET" class="mb-3 d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Search by Name or Email" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <!-- Entries Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm" id="entriesTable">
            <thead class="table-dark">
                <tr>
                    <th onclick="sortTable(0)">ID</th>
                    <th onclick="sortTable(1)">Name</th>
                    <th onclick="sortTable(2)">Email</th>
                    <th onclick="sortTable(3)">Phone</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr onmouseover="this.style.backgroundColor='#f2f2f2'" onmouseout="this.style.backgroundColor=''">
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $row['id']; ?>">Delete</button>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                      </div>
                                      <div class="modal-body">
                                        Are you sure you want to delete this entry?
                                      </div>
                                      <div class="modal-footer">
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Yes, Delete</a>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No entries found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav class="mt-3">
        <ul class="pagination">
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?search=<?php echo $search; ?>&page=<?php echo $page-1; ?>">Previous</a>
            </li>

            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <li class="page-item <?php echo ($i==$page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?search=<?php echo $search; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?search=<?php echo $search; ?>&page=<?php echo $page+1; ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>

<!-- Add Entry Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="add.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Entry</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input type="text" id="name" name="name" class="form-control" required oninput="this.value = this.value.toUpperCase()">
          <small class="text-muted">Min 3 letters</small>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="phone" class="form-label">Phone</label>
          <input type="text" id="phone" name="phone" class="form-control" required oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Add Entry</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Table Sorting
function sortTable(n) {
    let table, rows, switching, i, x, y, shouldSwitch;
    table = document.getElementById("entriesTable");
    switching = true;
    while (switching) {
        switching = false;
        rows = table.rows;
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                shouldSwitch = true;
                break;
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
        }
    }
}

// Real-time validation for Name (optional extra)
document.getElementById("name").addEventListener("input", function() {
    let name = this.value;
    if(name.length < 3) {
        this.style.borderColor = "red";
    } else {
        this.style.borderColor = "green";
    }
});
</script>
</body>
</html>
