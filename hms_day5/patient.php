<?php
include 'config.php';
$sql = "SELECT * FROM entries ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Patients List</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #0d1b2a; color: #fff; }
.table-container { max-width: 800px; margin: auto; background: #1b263b; padding: 20px; border-radius: 12px; box-shadow: 0px 4px 15px rgba(0,0,0,0.5);}
h2 { color: #fca311; text-align: center; }
table { background: #fff; color: #000; border-radius: 8px; overflow: hidden; }
.btn-custom { background-color: #fca311; border: none; color: #000; font-weight: bold; }
.btn-custom:hover { background-color: #ffba08; color: #000; }
</style>
</head>
<body>

<div class="container mt-5">
  <div class="table-container">
    <h2>📋 Registered Patients</h2>
    <table class="table table-bordered table-striped text-center">
      <thead>
        <tr>
          <th>ID</th><th>Name</th><th>Email</th><th>Phone</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['phone']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>⚠️ No patients registered yet.</td></tr>";
        }
        ?>
      </tbody>
    </table>
    <div class="text-center mt-3">
      <a href="form.html" class="btn btn-custom">➕ Register New Patient</a>
    </div>
  </div>
</div>

</body>
</html>
<?php $conn->close(); ?>
