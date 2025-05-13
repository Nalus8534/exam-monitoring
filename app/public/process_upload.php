<?php
session_start();
require '../vendor/autoload.php';
require_once __DIR__ . '/../../config/db.php';

// Security checks
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// File validation
$allowed_types = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$max_size = 5 * 1024 * 1024; // 5MB

try {
    // Validate file
    if (!isset($_FILES['student_file']) || 
        !in_array($_FILES['student_file']['type'], $allowed_types) || 
        $_FILES['student_file']['size'] > $max_size) {
        throw new Exception("Invalid file type or size (max 5MB)");
    }

    // Parse file
    $parser = new \Smalot\PdfParser\Parser();
    $text = '';
    
    if ($_FILES['student_file']['type'] === 'application/pdf') {
        $pdf = $parser->parseFile($_FILES['student_file']['tmp_name']);
        $text = $pdf->getText();
    } else {
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($_FILES['student_file']['tmp_name']);
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    foreach ($element->getElements() as $textElement) {
                        $text .= $textElement->getText() . ' ';
                    }
                }
            }
        }
    }

    // Process text
    $lines = preg_split('/\n|\r\n?/', $text);
    $students = [];
    
    foreach ($lines as $line) {
        if (preg_match('/^(.+?)\s+([A-Z0-9\/]+)\s+(\d+)\s+([A-Z0-9]*)\s+(.+?)\s+(.+)$/', $line, $matches)) {
            $students[] = [
                'name' => trim($matches[1]),
                'admission_no' => trim($matches[2]),
                'nta_level' => intval($matches[3]),
                'exam_no' => trim($matches[4]),
                'program' => trim($matches[5]),
                'venue_name' => trim($matches[6])
            ];
        }
    }

    if (empty($students)) throw new Exception("No valid student records found");

    // Database operations
    $conn->begin_transaction();

    // Student insert
    $student_stmt = $conn->prepare("
        INSERT INTO students (name, admission_no, nta_level, exam_no, program)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            nta_level = VALUES(nta_level),
            exam_no = VALUES(exam_no),
            program = VALUES(program)
    ");

    // Venue assignment
    $venue_assign_stmt = $conn->prepare("
        INSERT INTO student_venues (student_id, venue_id)
        SELECT ?, id FROM venues 
        WHERE venue_name = ? AND assigned_students < capacity
    ");

    // Update venue counter
    $venue_update_stmt = $conn->prepare("
        UPDATE venues 
        SET assigned_students = assigned_students + 1 
        WHERE venue_name = ? AND assigned_students < capacity
    ");

    $success_count = 0;
    $venue_assignments = 0;

    foreach ($students as $student) {
        // Insert/Update student
        $student_stmt->bind_param("ssiss", 
            $student['name'],
            $student['admission_no'],
            $student['nta_level'],
            $student['exam_no'],
            $student['program']
        );
        
        if ($student_stmt->execute()) {
            $success_count++;
            $student_id = $student_stmt->insert_id ?: $conn->insert_id;

            // Handle venue assignment
            if (!empty($student['venue_name'])) {
                $venue_assign_stmt->bind_param("is", $student_id, $student['venue_name']);
                if ($venue_assign_stmt->execute() && $venue_assign_stmt->affected_rows > 0) {
                    $venue_update_stmt->bind_param("s", $student['venue_name']);
                    $venue_update_stmt->execute();
                    $venue_assignments++;
                }
            }
        }
    }

    $conn->commit();

    $_SESSION['import_result'] = [
        'total' => count($students),
        'success' => $success_count,
        'venue_assignments' => $venue_assignments,
        'failed' => count($students) - $success_count
    ];

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['import_error'] = "Error: " . $e->getMessage();
}

header("Location: add_student.php");
exit();
?>