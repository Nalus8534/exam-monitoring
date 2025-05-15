<?php
session_start();

// Restrict access only to authorized users
if ($_SESSION['admin_role'] !== 'invigilator' && $_SESSION['admin_role'] !== 'admission_office') {
    header("Location: unauthorized.php");
    exit();
}
require_once __DIR__ . '/../../config/db.php';

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["admission_no"]) && isset($_FILES["student_image"])) {
$admission_no = isset($_POST['admission_no']) ? trim($_POST['admission_no']) : '';
    $image = $_FILES["student_image"];


    if (empty($admission_no) || empty($image["name"])) {
        $message = "Admission number and image are required.";
        $message_type = "error";
    } else {
        // Validate student existence
        $stmt_check = $conn->prepare("SELECT id FROM students WHERE admission_no = ?");
        $stmt_check->bind_param("s", $admission_no);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $student = $result_check->fetch_assoc();
            $student_id = $student['id'];

            // Ensure correct upload directory
            $upload_dir = __DIR__ . '/../../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Allowed file types
            $image_filetype = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
            $allowed_types = ["jpg", "jpeg", "png"];
            if (!in_array($image_filetype, $allowed_types)) {
                $message = "Only JPG, JPEG, and PNG files are allowed.";
                $message_type = "error";
            } else {
                // Create filename using admission number
                $filename = "student_" . $admission_no . "." . $image_filetype;
                $absolute_target_file = $upload_dir . $filename;
                $relative_image_path = "uploads/" . $filename;

                $stmt_dup = $conn->prepare("SELECT admission_no FROM students WHERE image_path = ?");
                $stmt_dup->bind_param("s", $relative_image_path);
                $stmt_dup->execute();
                $dup_result = $stmt_dup->get_result();

                                // Check if rows exist and ensure fetching works
                if ($dup_result->num_rows > 0) {
                    $dup_student = $dup_result->fetch_assoc();
                    
                    if (isset($dup_student['admission_no'])) {
                        $message = "This image is already assigned to Admission No: " . htmlspecialchars($dup_student['admission_no']);
                    } else {
                        $message = "This image is already assigned, but admission number could not be retrieved.";
                    }
                    
                    $message_type = "error";
                } else {
                    if (move_uploaded_file($image["tmp_name"], $absolute_target_file)) {
                        $stmt_update = $conn->prepare("UPDATE students SET image_path = ? WHERE id = ?");
                        $stmt_update->bind_param("si", $relative_image_path, $student_id);
                        
                        if ($stmt_update->execute()) {
                            $message = "Image successfully uploaded and assigned to student.";
                            $message_type = "success";
                        } else {
                            $message = "Failed to update student record.";
                            $message_type = "error";
                        }
                        
                        $stmt_update->close();
                    } else {
                        $message = "Error uploading file.";
                        $message_type = "error";
                    }
                }


                $stmt_dup->close();
            }
        } else {
            $message = "Student not found.";
            $message_type = "error";
        }
        $stmt_check->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Student Image</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Upload Container */
        .upload-container {
            max-width: 500px;
            margin: 30px auto;
            padding: 25px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .upload-container h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        .upload-container form label {
            font-size: 16px;
            margin-bottom: 8px;
            color: #555;
        }
        .upload-container form select,
        .upload-container form input[type="text"] {
            padding: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
            width: 100%;
        }
        .upload-container form button {
            background-color: #3498db;
            color: #fff;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .upload-container form button:hover {
            background-color: #2980b9;
        }
        .message {
            text-align: center;
            font-weight: bold;
            margin-top: 15px;
        }
        .message.error {
            color: #e74c3c;
        }
        .message.success {
            color: #27ae60;
        }


        /* choose file */
.image-upload input[type="file"] {
    display: none;
}

.image-upload label {
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #2c3e50;
}

.image-upload i {
    font-size: 2.5rem;
    color: #3498db;
    margin-bottom: 1rem;
}
.image-upload span {
    font-size: 1.1rem;
    font-weight: 500;
}
.image-upload {
    border: 2px dashed #3498db;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    margin: 1.5rem 0;
    transition: border-color 0.3s;
}

.image-upload:hover {
    border-color: #2980b9;
}
    </style>

</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/images/atc_logo.png" alt="Exam System" class="logo">
        <h3>Dashboard</h3>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <?php if ($_SESSION['admin_role'] === 'invigilator') { ?>
                <li><a href="view_students.php" class="<?= ($current_page == 'view_students.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-users"></i></span> View Students</a></li>

                <li><a href="scan.php" class="<?= ($current_page == 'scan.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-barcode"></i></span> Scan Student ID</a></li>

                <li><a href="view_reports.php" class="<?= ($current_page == 'view_reports.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-chart-bar"></i></span> View Statistics</a></li>
                <li><a href="logout.php">
                    <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span> Logout</a></li>

        <?php } elseif ($_SESSION['admin_role'] === 'admission_office') { ?>

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
            <?php } ?>
        </ul>
    </nav>
</aside>
        <!-- Main Content -->
        <main class="main-content" >
            <header class="content-header">
                <h1>Upload Student Image</h1>
            </header>
            <div class="upload-container">
                <h2>Upload and Assign Image to Student</h2>
                <?php if (!empty($message)): ?>
                    <p class="message <?= $message_type; ?>"><?= htmlspecialchars($message); ?></p>
                <?php endif; ?>

              <form method="POST" action="upload_student_image.php" enctype="multipart/form-data">
                    <label for="admission_no">Admission Number:</label>
                    <input type="text" id="admission_no" name="admission_no" required>
               <div class="image-upload">
                    <input type="file" id="student_image" name="student_image" accept="image/*" required>
                    <label for="student_image">
                      <i class="fas fa-file-upload"></i>
                      <span>Upload Image:</span>
                    </label>
               </div>
                    <button type="submit">Upload & Assign Image</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
