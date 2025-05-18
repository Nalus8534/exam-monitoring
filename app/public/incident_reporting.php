<?php
session_start();
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'invigilator') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../config/db.php';

$error_message = "";
$success_message = "";
$student_info = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reported_by = trim($_POST['reported_by']);
    $incident_type = trim($_POST['incident_type']);
    $admission_no = trim($_POST['admission_no'] ?? '');
    $details = trim($_POST['details']);

    if (empty($reported_by) || empty($incident_type) || empty($details)) {
        $error_message = "All fields are required.";
    } else {
        $student_id = null;
        $full_details = "";

        if ($incident_type === "student" && !empty($admission_no)) {
            $stmt = $conn->prepare("SELECT id, name, admission_no FROM students WHERE admission_no = ?");
            $stmt->bind_param("s", $admission_no);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $student_info = $result->fetch_assoc();
                $student_id = $student_info['id'];
                $full_details = "The student with admission number {$student_info['admission_no']} ({$student_info['name']}) has done the following: {$details}";
            } else {
                $error_message = "No student found with admission number '{$admission_no}'.";
            }
            $stmt->close();
        } else {
            $full_details = "Issue reported: {$details}";
        }

        if (empty($error_message)) {
            $stmt = $conn->prepare("INSERT INTO incidents (reported_by, incident_type, student_id, admission_no, details) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiss", $reported_by, $incident_type, $student_id, $admission_no, $full_details);
            if ($stmt->execute()) {
                $success_message = "Incident reported successfully.";
            } else {
                $error_message = "Database error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report an Incident - Invigilator Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f0f5f9;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }
        .container {
            width: 85%;
            max-width: 900px;
            background: #ffffff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
            text-align: center;
        }
        .header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
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
            margin-top: 15px;
            width: 250px;
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
        .form-container {
            text-align: left;
            margin-top: 20px;
        }
        label {
            font-weight: bold;
            font-size: 16px;
            display: block;
            margin-top: 15px;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            display: block;
        }
        textarea {
            height: 120px;
        }
        .btn-submit {
            background: #5c67f2;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            margin-top: 20px;
            width: 100%;
            transition: 0.3s;
            font-weight: bold;
        }
        .btn-submit:hover {
            background: #4a54e1;
        }
        .message {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
        }
        .error-message {
            color: #e74c3c;
        }
        .success-message {
            color: #27ae60;
        }
    </style>
    <script>
        function toggleStudentField() {
            let type = document.getElementById('incident_type').value;
            let studentFields = document.getElementById('student-fields');
            studentFields.style.display = type === 'student' ? 'block' : 'none';
        }
    </script>
</head>
<body>

<div class="container">
    <div class="header">Report an Incident</div>

    <a href="invigilator_dashboard.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>

    <div class="form-container">
        <form method="POST" action="">
            <label for="reported_by">Your Name:</label>
            <input type="text" name="reported_by" required>

            <label for="incident_type">Incident Type:</label>
            <select name="incident_type" id="incident_type" required onchange="toggleStudentField()">
                <option value="student">--select issue--</option>
                <option value="student">Student</option>
                <option value="other">Other Issue</option>
            </select>

            <div id="student-fields" style="display: none;">
                <label for="admission_no">Student Admission No:</label>
                <input type="text" name="admission_no">
            </div>

            <label for="details">Incident Details:</label>
            <textarea name="details" required></textarea>

            <button type="submit" class="btn-submit">Submit Report</button>
        </form>
    </div>

    <?php if (!empty($error_message)) echo "<p class='message error-message'>$error_message</p>"; ?>
    <?php if (!empty($success_message)) echo "<p class='message success-message'>$success_message</p>"; ?>
</div>

</body>
</html>
