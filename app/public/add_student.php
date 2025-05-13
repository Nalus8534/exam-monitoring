<?php
// Set PHP limits and start session
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);
session_start();

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

// Load dependencies
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/db.php';

use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;

// Check database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Allowed programs & allowed venues arrays
$programs = [
    'Ordinary Diploma In Automotive Engineering',
    'Ordinary Diploma In Auto-electrical And Electronics Engineering',
    'Ordinary Diploma In Civil And Highway Engineering',
    'Ordinary Diploma In Civil And Irrigation Engineering',
    'Ordinary Diploma In Civil Engineering',
    'Ordinary Diploma In Computer Science',
    'Ordinary Diploma In Cyber Security And Digital Forensics',
    'Ordinary Diploma In Electrical And Biomedical Engineering',
    'Ordinary Diploma In Electrical And Hydro Power Engineering',
    'Ordinary Diploma In Electrical And Solar Energy Engineering',
    'Ordinary Diploma In Electrical And Wind Energy Engineering',
    'Ordinary Diploma In Electrical Engineering',
    'Ordinary Diploma In Electronics And Telecommunication Engineering',
    'Ordinary Diploma In Heavy Duty Equipment Engineering',
    'Ordinary Diploma In Information Technology',
    'Ordinary Diploma In Laboratory Science And Technology',
    'Ordinary Diploma In Mechanical And Bio-energy Engineering',
    'Ordinary Diploma In Mechanical Engineering',
    'Ordinary Diploma In Pipe Works, Oil And Gas Engineering',
    'Ordinary Diploma In Instrumentation Engineering'
];

$allowed_venues = [
    'R12/13', 'US02', 'DH', 'G11', 'S10', 'F12', 'H/WAY', 'T10', 'T11', 'S07', 'S06', 'UG06','UG07','UF05','UF01', 'T12'
];

// Insert allowed venues if not present (capacity is randomized between 50 and 200)
foreach ($allowed_venues as $venue) {
    $capacity = rand(50, 200);
    $conn->query("INSERT IGNORE INTO venues (venue_name, capacity, assigned_students, created_at) VALUES ('$venue', '$capacity', 0, NOW())");
}


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = trim($_POST['name']);
        $admission_no = trim($_POST['admission_no']);
        $nta_level = intval($_POST['nta_level']);
        $exam_no = strtoupper(str_replace(' ', '', trim($_POST['exam_no'])));
        $program = trim($_POST['program']);
        $venue_field = trim($_POST['venue']);

        if (empty($name) || empty($admission_no) || empty($program)) {
            $message = "All fields are required.";
            $message_type = 'error';
        } else {
            $insert_stmt = $conn->prepare("INSERT INTO students (name, admission_no, nta_level, exam_no, program, venue) VALUES (?, ?, ?, ?, ?, '')");
            $insert_stmt->bind_param("ssiss", $name, $admission_no, $nta_level, $exam_no, $program);
            if ($insert_stmt->execute()) {
                $message = "Student added successfully.";
                $message_type = 'success';
            } else {
                $message = "Insert error: " . $insert_stmt->error;
                $message_type = 'error';
            }
            $insert_stmt->close();
        }
    }

// CLEAR STUDENTS: delete students and reset venue assigned count
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clear_students'])) {
    try {
        $conn->query("DELETE FROM student_venues");
        $conn->query("DELETE FROM students");
        $conn->query("UPDATE venues SET assigned_students = 0");
        $_SESSION['bulk_error'] = "All students and venue data cleared successfully!";
    } catch (Exception $e) {
        $_SESSION['bulk_error'] = "Error clearing students: " . $e->getMessage();
    }
    header("Location: add_student.php");
    exit();
}

// BULK FILE PROCESSING – ASSUMING EACH RECORD IS ON A SINGLE LINE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['bulk_file'])) {

    try {
        $allowed_types = [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        $max_size = 50 * 1024 * 1024; // 50MB
        $file = $_FILES['bulk_file'];
        
        if (!in_array($file['type'], $allowed_types) || $file['size'] > $max_size) {
            throw new Exception("Only PDF/DOCX files under 50MB allowed");
        }
        
        // Extract text from file (supports PDF or DOCX)
        $text = "";
        if ($file['type'] === 'application/pdf') {
            $parser = new Parser();
            $pdf = $parser->parseFile($file['tmp_name']);
            $text = $pdf->getText();
        } else {
            $phpWord = IOFactory::load($file['tmp_name']);
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }
        }
        
        // Split the text into lines and clean them up
        $lines = preg_split('/\R/', trim($text));
        $filtered_lines = array_filter(array_map('trim', $lines), function($line) {
            return !empty($line);
        });
        $filtered_lines = array_values($filtered_lines); // Re-index

        // Define a helper to normalize lines
        function normalize_line($line) {
            return strtolower(preg_replace('/\s+/', ' ', trim($line)));
        }
        $expected_header = 'fullname admission number nta level exam number program venue';

        // Skip header row(s)
        $records = [];
        foreach ($filtered_lines as $line) {
            if (normalize_line($line) === $expected_header) {
                continue;  // Skip header
            }
            $records[] = $line;
        }

        // Prepare allowed venues for case-insensitive comparison.
        $allowedVenuesUpper = array_map('strtoupper', $allowed_venues);
        
        $results = ['total' => 0, 'success' => 0, 'failed' => 0, 'errors' => []];
        $conn->begin_transaction();
        $student_stmt = $conn->prepare("INSERT INTO students (name, admission_no, nta_level, exam_no, program, venue) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$student_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        // Regex for each record.
        // This pattern expects: 
        //   Group 1: Name (letters and spaces)
        //   Group 2: Admission number (digits)
        //   Group 3: NTA level (digits)
        //   Group 4: Exam number (starting with T2 and then allowed characters)
        //   Group 5: Program (any text up to the last token)
        //   Group 6: Venue (the last non-space token)

        $pattern = '/^([\p{L}\s\'\-`]+)\s+(\d+)\s+(\d+)\s+(T2[\/A-Za-z0-9]+)\s+(.+?)\s+(\S+)$/u';
        foreach ($records as $line) {
            if (!preg_match($pattern, $line, $matches)) {
                $results['failed']++;
                $results['errors'][] = "Record parse error for: " . htmlspecialchars($line);
                continue;
            }
            // Extract the fields (ignoring the full match)
            list(, $fullname, $admission, $nta, $exam, $program_field, $venue_field) = $matches;
            
            // Validate that the venue exists (case-insensitive)
            if (empty($venue_field) || !in_array(strtoupper(trim($venue_field)), $allowedVenuesUpper)) {
                $results['failed']++;
                $results['errors'][] = "Missing or invalid venue for student: " . htmlspecialchars($fullname);
                continue;
            }
            
            $nta_int = (int)$nta;
           // $exam_formatted_now = strtoupper(str_replace(['/', ' '], '', $exam));
            $student_stmt->bind_param("ssisss", $fullname, $admission, $nta_int, $exam, $program_field, $venue_field);
            if (!$student_stmt->execute()) {
                $results['failed']++;
                $results['errors'][] = "Database insertion error for student " . htmlspecialchars($fullname) . ": " . $student_stmt->error;
                continue;
            }
            $results['total']++;
            $results['success']++;
        }
    
    $conn->commit();
    // Recalculate assigned_students for each venue:
    $conn->query("UPDATE venues v
                SET v.assigned_students = (
                    SELECT COUNT(*) FROM students s WHERE s.venue = v.venue_name
                )");
    $_SESSION['bulk_results'] = $results;
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['bulk_error'] = "Bulk upload failed: " . $e->getMessage();
    }
    header("Location: add_student.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Students - ATC Exam System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/images/atc_logo.png" alt="College Logo" class="logo">
            <h3>ATC Exam System</h3>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard</a></li>
                
                <li><a href="scan.php" class="<?= ($current_page == 'scan.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-barcode"></i></span> Scan Student ID</a></li>
                
                <li><a href="assign_venue.php" class="<?= ($current_page == 'assign_venue.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-map-marker-alt"></i></span> Assign Venue</a></li>
                
                <li><a href="add_student.php" class="<?= ($current_page == 'add_student.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-user-plus"></i></span> Add/Delete Student</a></li>
                
                <li><a href="upload_student_image.php" class="<?= ($current_page == 'upload_student_image.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-camera"></i></span> Upload Student Image</a></li>
                
                <li><a href="view_students.php" class="<?= ($current_page == 'view_students.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-users"></i></span> View Students</a></li>
                
                <li><a href="view_reports.php" class="<?= ($current_page == 'view_reports.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-chart-bar"></i></span> View Statistics</a></li>
                
                <li><a href="logout.php">
                    <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span> Logout</a></li>
            </ul>
        </nav>
    </aside>
    <main class="main-content">
        <header class="content-header">
            <h1 class="page-title"><i class="fas fa-user-plus"></i> Add Students</h1>
            <div class="header-actions">
                <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            </div>
        </header>
        <div class="form-container">
            <!-- Clear Students Form -->
            <form method="POST" class="clear-form">
                <button type="submit" name="clear_students" class="btn-danger" onclick="return confirm('WARNING: This will delete ALL students!')">
                    <i class="fas fa-trash-alt"></i> Clear All Students
                </button>
            </form>
            
            <!-- Display Messages if set -->
            <?php if (!empty($message)) : ?>
                <div class="<?= $message_type == 'success' ? 'success-message' : 'error-message' ?>">
                    <i class="fas fa-<?= $message_type == 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i> <?= $message ?>
                </div>
            <?php endif; ?>
            
            <!-- Single Student Form -->
            <form method="POST" action="add_student.php">
                <div class="form-group">
                    <label for="name"><i class="fas fa-user"></i> Full Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="admission_no"><i class="fas fa-id-card"></i> Admission Number:</label>
                    <input type="text" id="admission_no" name="admission_no" required>
                </div>
                <div class="form-group">
                    <label for="nta_level"><i class="fas fa-layer-group"></i> NTA Level:</label>
                    <input type="number" id="nta_level" name="nta_level" min="4" max="8" required>
                </div>
                <div class="form-group">
                    <label for="exam_no"><i class="fas fa-sort-numeric-up-alt"></i> Exam Number:</label>
                    <input type="text" id="exam_no" name="exam_no">
                </div>
                <div class="form-group">
                    <label for="program"><i class="fas fa-graduation-cap"></i> Program:</label>
                    <select id="program" name="program" required>
                        <option value="">Select Program</option>
                        <?php foreach ($programs as $prog): ?>
                            <option value="<?= htmlspecialchars($prog) ?>"><?= htmlspecialchars($prog) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="venue"><i class="fas fa-building"></i> Venue:</label>
                    <select id="venue" name="venue">
                        <option value="">Select Venue</option>
                        <?php foreach ($allowed_venues as $venue_item): ?>
                            <option value="<?= htmlspecialchars($venue_item) ?>"><?= htmlspecialchars($venue_item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-plus-circle"></i> Add Student
                </button>
            </form>
            
            <!-- Bulk Upload Section -->
            <div class="bulk-section">
                <h3><i class="fas fa-file-import"></i> Bulk Upload</h3>
                <?php if (isset($_SESSION['bulk_error'])): ?>
                    <div class="alert error">
                        <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['bulk_error'] ?>
                    </div>
                    <?php unset($_SESSION['bulk_error']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['bulk_results'])): ?>
                    <div class="bulk-results">
                        <div class="result-box">
                            <span>Processed</span> <strong><?= $_SESSION['bulk_results']['total'] ?></strong>
                        </div>
                        <div class="result-box success">
                            <span>Success</span> <strong><?= $_SESSION['bulk_results']['success'] ?></strong>
                        </div>
                        <div class="result-box error">
                            <span>Failed</span> <strong><?= $_SESSION['bulk_results']['failed'] ?></strong>
                        </div>
                    </div>
                    <?php if (!empty($_SESSION['bulk_results']['errors'])): ?>
                        <div class="error-details">
                            <h4>Error Details:</h4>
                            <?php foreach ($_SESSION['bulk_results']['errors'] as $error): ?>
                                <p class="error-message"><?= $error ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php unset($_SESSION['bulk_results']); ?>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data" class="bulk-form">
                    <div class="file-upload">
                        <input type="file" name="bulk_file" id="bulk_file" accept=".pdf, .docx" required>
                        <label for="bulk_file">
                            <i class="fas fa-file-upload"></i>
                            <span>Choose PDF/DOCX File</span>
                            <small>Max 50MB | Format: Name, AdmissionNo, NTALevel, ExamNo, Program, Venue</small>
                        </label>
                    </div>
                    <button type="submit" class="btn-bulk">
                        <i class="fas fa-upload"></i> Upload & Process
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>
<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('collapsed');
    document.querySelector('.main-content').classList.toggle('sidebar-collapsed');
}
</script>
</body>
</html>
