<?php
session_start();
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'admission_office') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../config/db.php';

$filter_status = $_GET['status'] ?? 'pending';
$stmt = $conn->prepare("SELECT * FROM incidents WHERE reviewed_status = ? ORDER BY reported_at DESC");
$stmt->bind_param("s", $filter_status);
$stmt->execute();
$result = $stmt->get_result();

// Handle review update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['incident_id'])) {
    $incident_id = $_POST['incident_id'];
    $stmtUpdate = $conn->prepare("UPDATE incidents SET reviewed_status = 'reviewed' WHERE id = ?");
    $stmtUpdate->bind_param("i", $incident_id);
    $stmtUpdate->execute();
    $stmtUpdate->close();
    header("Location: view_incidents.php?status=pending");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incident Reports - Admission Office</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 90%;
            margin: auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #34495e;
            color: white;
            padding: 15px;
            border-radius: 8px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
        }
        .back-link {
            text-decoration: none;
            color: white;
            font-size: 18px;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .filter-box {
            margin-top: 15px;
            text-align: center;
        }
        .filter-box select {
            padding: 8px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .incident-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .incident-card {
            background: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .incident-card:hover {
            transform: scale(1.03);
        }
        .incident-title {
            font-size: 18px;
            font-weight: bold;
            color: #2980b9;
        }
        .incident-info {
            margin-top: 10px;
        }
        .incident-info p {
            font-size: 14px;
            margin: 5px 0;
            color: #2c3e50;
        }
        .btn-review {
            background: #27ae60;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        .btn-review:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Incident Reports</h1>
            <a href="admission_dashboard.php" class="back-link">⬅ Back to Dashboard</a>
        </div>
        <div class="filter-box">
            <form method="GET" action="">
                <label for="status">Filter by Status:</label>
                <select name="status" id="status" onchange="this.form.submit()">
                    <option value="pending" <?= $filter_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="reviewed" <?= $filter_status == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                </select>
            </form>
        </div>

        <div class="incident-cards">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="incident-card">
                    <div class="incident-title"><?= ucfirst($row['incident_type']); ?> Incident</div>
                    <div class="incident-info">
                        <p><strong>Reported By:</strong> <?= htmlspecialchars($row['reported_by']); ?></p>
                        <?php if ($row['student_id']): ?>
                            <p><strong>Student Reported:</strong> Admission No: <?= htmlspecialchars($row['admission_no']); ?></p>
                        <?php endif; ?>
                        <p><strong>Details:</strong> <?= htmlspecialchars($row['details']); ?></p>
                        <p><strong>Date:</strong> <?= $row['reported_at']; ?></p>
                    </div>
                    <?php if ($filter_status == 'pending'): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="incident_id" value="<?= $row['id']; ?>">
                            <button type="submit" class="btn-review">Mark as Reviewed</button>
                        </form>
                    <?php else: ?>
                        <span style="color: green;">Reviewed</span>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
