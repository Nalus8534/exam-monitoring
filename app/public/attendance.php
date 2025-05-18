<?php
session_start();

if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'invigilator') {
    header("Location: /exam_monitoring/app/public/login.php");
    exit();
}

date_default_timezone_set("Africa/Nairobi"); // Nairobi shares the same timezone as Dar es Salaam


require_once __DIR__ . '/../../config/db.php';

// Fetch student data correctly
$stmt = $conn->prepare("SELECT id, admission_no, name, nta_level, exam_no, program, venue, IF(attended=1, 1, 0) AS attended FROM students ORDER BY venue ASC");
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$stmt->close();

// Fetch venue names dynamically from the database
$stmt = $conn->prepare("SELECT DISTINCT venue_name FROM venues ORDER BY venue_name ASC");
$stmt->execute();
$result = $stmt->get_result();

$venues = [];
while ($row = $result->fetch_assoc()) {
    $venues[] = $row['venue_name'];
}

echo "PHP Timezone: " . date_default_timezone_get();
echo "Current Time: " . date("Y-m-d H:i:s");


$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance - Invigilator Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
/* General body styling */
body {
    font-family: Arial, sans-serif;
    background: rgb(213, 252, 237);
    color: #333;
    padding: 20px;
}

/* Container adjustments for better spacing */
.container {
    max-width: 95%; /* Expands table while keeping space at edges */
    margin: auto;
    padding: 20px;
}

/* Table styling */
table {
    width: 100%; /* Uses full available space */
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
}

/* Table headers and data cells */
th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #2980b9;
    color: #fff;
    font-weight: bold;
}

tr:hover {
    background-color: #f1f1f1;
}

/* Venue Selection Dropdown */
.filter-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
}

#venue-select {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    background: white;
    transition: 0.3s;
}

#venue-select:hover {
    border-color: #2980b9;
}

.filter-btn {
    padding: 10px 15px;
    background: #2980b9;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

.filter-btn:hover {
    background: #1e5f89;
}

/* Checkbox Enhancements - Toggle Switch Style */
.attendance-checkbox {
    appearance: none;
    width: 40px;
    height: 20px;
    background: #ccc;
    border-radius: 20px;
    position: relative;
    cursor: pointer;
    transition: background 0.3s;
}

.attendance-checkbox::before {
    content: "";
    position: absolute;
    width: 18px;
    height: 18px;
    background: #fff;
    border-radius: 50%;
    top: 1px;
    left: 1px;
    transition: transform 0.3s;
}

/* Checked state */
.attendance-checkbox:checked {
    background: #28a745;
}

.attendance-checkbox:checked::before {
    transform: translateX(20px);
}

/* Action Button Styling */
.action-btn {
    background: #28a745;
    color: #fff;
    width: 100%;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s;
}

.action-btn:hover {
    background: #218838;
}
.back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #3498db;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            margin-left: 50rem;
            width: 180px;
            transition: 0.3s;
            font-weight: bold;
        }
        .back-link i {
            margin-right: 10px;
            font-size: 18px;
        }
        .back-link:hover {
            background: #2980b9;
        }

  </style>
</head>
<body>
    <div class="container">
        <h1>Attendance</h1>

        <div class="filter-container">
        <label for="venue-select"><strong>Filter by Venue:</strong></label>
        <select id="venue-select" onchange="updateVenue(this.value)">
            <option value="">Select Venue</option>
            <?php foreach ($venues as $venue): ?>
                <option value="<?= htmlspecialchars($venue); ?>"><?= htmlspecialchars($venue); ?></option>
            <?php endforeach; ?>
        </select>
            <a href="invigilator_dashboard.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Admission No</th>
                    <th>Name</th>
                    <th>NTA Level</th>
                    <th>Exam No</th>
                    <th>Program</th>
                    <th>Venue</th>
                    <th>Present?</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr data-venue="<?= htmlspecialchars($student['venue'] ?? 'Unknown Venue'); ?>">
                        <td><?= htmlspecialchars($student['admission_no']); ?></td>
                        <td><?= htmlspecialchars($student['name']); ?></td>
                        <td><?= htmlspecialchars($student['nta_level'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($student['exam_no']); ?></td>
                        <td><?= htmlspecialchars($student['program']); ?></td>
                        <td><?= htmlspecialchars($student['venue'] ?? 'Not Assigned'); ?></td>
                        <td>
                            <input type="checkbox" class="attendance-checkbox" data-student-id="<?= $student['id']; ?>" <?= $student['attended'] ? 'checked' : ''; ?>>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table><br>
        <button class="action-btn" onclick="saveAttendance();">Save Attendance</button>
    </div>
</body>
  <script>
    function updateVenue(selectedVenue) {
        const rows = document.querySelectorAll("tbody tr");
        rows.forEach(row => {
            const studentVenue = row.getAttribute("data-venue");
            row.style.display = (selectedVenue === "" || studentVenue === selectedVenue) ? "" : "none";
        });
    }

function saveAttendance() {
    const checkboxes = document.querySelectorAll(".attendance-checkbox:checked");
    const attendanceData = [];
    const selectedVenue = document.getElementById("venue-select").value; // Get selected venue

    checkboxes.forEach(checkbox => {
        const row = checkbox.closest("tr");
        const studentId = checkbox.getAttribute("data-student-id");
        const admissionNo = row.cells[0].textContent.trim();
        const examNo = row.cells[3].textContent.trim();
        const venue = row.cells[5].textContent.trim();  // Read venue from table

        attendanceData.push({ 
            student_id: studentId, 
            admission_no: admissionNo, 
            exam_no: examNo,
            venue: selectedVenue || venue, // Prioritize the selected venue over table venue
            attendance_status: "present" 
        });
    });

    fetch("/exam_monitoring/app/public/process_attendance.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ attendance: attendanceData })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? "Attendance saved successfully!" : "Error saving attendance: " + data.message);
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Network or server error while saving attendance.");
    });
}
  </script>
</html>
