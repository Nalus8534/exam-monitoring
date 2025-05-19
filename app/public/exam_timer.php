<?php
session_start();
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'invigilator') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Exam Scheduler & Multi-Timer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    /* CSS Variables for Dark Mode */
    :root {
      --bg-color: #1e1e2f;
      --container-bg: #2b2b3d;
      --accent-color: #4285f4;
      --accent-hover: #357ae8;
      --text-color: #e2e2e2;
      --shadow-color: rgba(0, 0, 0, 0.5);
      --section-bg: #3a3a4b;
      --exam-card-bg: #44465a;
      --running-exam-bg: #44465a;
      --warning30: #fff4cc;
      --warning15: #ffe0b3;
      --warning5:  #ffcccc;
      --input-bg: #2b2b3d;
    }
    /* CSS Variables for Light Mode */
    .light-mode {
      --bg-color: #f0f0f0;
      --container-bg: #ffffff;
      --accent-color: #4285f4;
      --accent-hover: #357ae8;
      --text-color: #333333;
      --shadow-color: rgba(200, 200, 200, 0.3);
      --section-bg: #e8e8e8;
      --exam-card-bg: #f9f9f9;
      --running-exam-bg: #f9f9f9;
      --warning30: #fff4cc;
      --warning15: #ffe0b3;
      --warning5:  #ffcccc;
      --input-bg: #ffffff;
    }
    /* Global Styles */
    body {
      background: var(--bg-color);
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: var(--text-color);
      transition: background 0.3s, color 0.3s;
    }
    /* Fixed Header for Persistent Theme Toggle */
    header {
      position: fixed;
      top: 10px;
      right: 10px;
      z-index: 1000;
    }
    #themeToggle {
      background-color: var(--accent-color);
      color: #fff;
      border: none;
      border-radius: 4px;
      padding: 8px 16px;
      cursor: pointer;
      transition: background 0.3s;
    }
    #themeToggle:hover {
      background-color: var(--accent-hover);
    }
    .container {
      max-width: 1100px;
      margin: 70px auto 40px; /* add top margin to avoid header overlap */
      background: var(--container-bg);
      padding: 15px 20px;
      border-radius: 10px;
      box-shadow: 0 8px 20px var(--shadow-color);
      transition: background 0.3s;
    }
    h1, h2 {
      text-align: center;
      margin-bottom: 15px;
      transition: color 0.3s;
    }
    /* Section Layout */
    .section {
      margin-bottom: 20px;
      padding: 15px;
      background: var(--section-bg);
      border-radius: 8px;
      transition: background 0.3s;
    }
    .form-group {
      text-align: center;
      margin-bottom: 15px;
    }
    label {
      font-weight: bold;
      margin-right: 8px;
    }
    select, input[type="text"] {
      padding: 10px;
      width: 260px;
      border: 1px solid #555;
      border-radius: 6px;
      margin: 8px;
      background: var(--input-bg);
      color: var(--text-color);
      transition: background 0.3s, color 0.3s;
    }
    button {
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      background-color: var(--accent-color);
      color: #fff;
      cursor: pointer;
      margin: 10px;
      font-size: 16px;
      transition: background 0.3s;
    }
    button:hover {
      background-color: var(--accent-hover);
    }
    /* Scheduled Exam Card */
    .exam-card {
      background: var(--exam-card-bg);
      padding: 10px 14px;
      border-radius: 6px;
      margin: 8px 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 8px var(--shadow-color);
      transition: background 0.3s;
    }
    /* Enhanced exam title styling for visibility */
    .exam-card span {
      font-size: 20px;
      font-weight: bold;
      color: var(--accent-color);
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
    }
    /* Toggle Switch (Rounded) */
    .switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 26px;
    }
    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: 0.4s;
      border-radius: 26px;
    }
    .slider:before {
      position: absolute;
      content: "";
      height: 20px;
      width: 20px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: 0.4s;
      border-radius: 50%;
    }
    input:checked + .slider {
      background-color: #0f9d58;
    }
    input:checked + .slider:before {
      transform: translateX(24px);
    }
    /* Running Exams: Flex Container for Block Layout */
    #runningExamList {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }
    .running-exam {
      background: var(--running-exam-bg);
      padding: 12px 16px;
      border-radius: 6px;
      margin: 8px 0;
      box-shadow: 0 4px 8px var(--shadow-color);
      transition: background 0.3s;
      width: 320px;
    }
    /* Enhanced running exam title styling */
    .running-exam h3 {
      margin: 4px 0;
      font-size: 22px;
      font-weight: bold;
      color: var(--accent-color);
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
    }
    .running-exam p {
      margin: 4px 0;
      color: var(--text-color);
      font-size: 15px;
    }
    /* Time Display Styling */
    .time-display {
      font-size: 24px;
      font-weight: bold;
      color: var(--accent-color);
      background: rgba(66, 133, 244, 0.1);
      padding: 6px 12px;
      border: 2px solid var(--accent-color);
      border-radius: 12px;
      min-width: 120px;
      text-align: center;
      margin-top: 8px;
      display: inline-block;
    }
    /* Warning Classes */
    .warning30 {
      background-color: var(--warning30) !important;
    }
    .warning15 {
      background-color: var(--warning15) !important;
    }
    .warning5 {
      background-color: var(--warning5) !important;
    }
    /* Start Button */
    .start-btn {
      display: block;
      margin: 15px auto;
      background-color: #0f9d58;
    }
    .start-btn:hover {
      background-color: #0b7e44;
    }
    /* Control Section for additional buttons */
    .control-section {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <header>
    <button id="themeToggle">Toggle Theme</button>
  </header>
  
  <div class="container">
    <h1>Exam Scheduler & Multi-Timer</h1>
    
    <!-- Select Level Section -->
    <div class="section">
      <h2>Select Level</h2>
      <div class="form-group">
        <label for="levelSelect">Choose Level:</label>
        <select id="levelSelect" onchange="showExamInput()">
          <option value="">--Select Level--</option>
          <option value="4">Level 4</option>
          <option value="5">Level 5</option>
          <option value="6">Level 6</option>
          <option value="7-1">Level 7-1</option>
          <option value="7-2">Level 7-2</option>
          <option value="8">Level 8</option>
        </select>
      </div>
    </div>
    
    <!-- Exam Input Section -->
    <div class="section" id="examInputSection" style="display:none;">
      <h2>Add Exam Details</h2>
      <div class="form-group">
        <input type="text" id="examName" placeholder="Exam Name" required>
        <select id="examDuration">
          <option value="60">1 Hour</option>
          <option value="90">1 Hour 30 Minutes</option>
          <option value="105">1 Hour 45 Minutes</option>
          <option value="120">2 Hours</option>
          <option value="150">2 Hours 30 Minutes</option>
          <option value="165">2 Hours 45 Minutes</option>
        </select>
        <button onclick="addExam()">Add Exam</button>
      </div>
    </div>
    
    <!-- Scheduled Exams Section -->
    <div class="section" id="scheduledExamsSection" style="display:none;">
      <h2>Scheduled Exams</h2>
      <div id="scheduledExamList"></div>
      <div class="form-group">
        <button class="start-btn" onclick="startSelectedExams()">Start Selected Exams</button>
      </div>
    </div>
    
    <!-- Running Exams Section -->
    <div class="section" id="runningExamsSection" style="display:none;">
      <h2>Running Exams</h2>
      <div id="runningExamList"></div>
    </div>
    
    <!-- Control Section -->
    <div class="section control-section">
      <button onclick="resetExamTimer()">Reset Timer</button>
      <button onclick="goToDashboard()">Back to Dashboard</button>
    </div>
  </div>
  
  <script>
    // Global arrays for scheduled and running exams.
    var scheduledExams = [];
    var runningExams = [];

    // Save state to localStorage so that progress persists.
    function saveState() {
      localStorage.setItem('runningExams', JSON.stringify(runningExams));
      localStorage.setItem('scheduledExams', JSON.stringify(scheduledExams));
    }

    // Restore state from localStorage on page load.
    window.onload = function() {
      var storedRunning = localStorage.getItem('runningExams');
      var storedScheduled = localStorage.getItem('scheduledExams');
      if (storedRunning) {
        runningExams = JSON.parse(storedRunning).map(function(exam) {
          exam.startTime = new Date(exam.startTime);
          exam.endTime = new Date(exam.endTime);
          exam.timerInterval = null;
          return exam;
        });
        displayRunningExams();
      }
      if (storedScheduled) {
        scheduledExams = JSON.parse(storedScheduled);
        updateScheduledExamDisplay();
      }
    };

    // Prevent refresh via F5 or Ctrl+R.
    document.addEventListener('keydown', function(e) {
      if(e.keyCode === 116 || (e.ctrlKey && e.keyCode === 82)) {
        e.preventDefault();
      }
    });

    // Warn before closing or refreshing.
    window.onbeforeunload = function(e) {
      e.preventDefault();
      e.returnValue = '';
    };

    // Show the exam input section once a level is selected.
    function showExamInput() {
      var level = document.getElementById("levelSelect").value;
      document.getElementById("examInputSection").style.display = level ? "block" : "none";
    }

    // Add exam details to the scheduledExams array.
    function addExam() {
      var examName = document.getElementById("examName").value.trim();
      var duration = document.getElementById("examDuration").value;
      if (!examName) {
        alert("Enter exam name.");
        return;
      }
      scheduledExams.push({ name: examName, duration: parseInt(duration), selected: false });
      document.getElementById("examName").value = "";
      updateScheduledExamDisplay();
      saveState();
    }

    // Update the scheduled exams display with toggle switches.
    function updateScheduledExamDisplay() {
      var scheduledDiv = document.getElementById("scheduledExamList");
      scheduledDiv.innerHTML = "";
      document.getElementById("scheduledExamsSection").style.display = scheduledExams.length > 0 ? "block" : "none";
      
      scheduledExams.forEach(function(exam, index) {
        var card = document.createElement("div");
        card.className = "exam-card";
        card.innerHTML = `
          <span><strong>${exam.name}</strong> - ${exam.duration} min</span>
          <label class="switch">
            <input type="checkbox" onchange="toggleExamSelection(${index})" ${exam.selected ? "checked" : ""}>
            <span class="slider"></span>
          </label>
        `;
        scheduledDiv.appendChild(card);
      });
      saveState();
    }

    // Toggle the 'selected' state of an exam.
    function toggleExamSelection(index) {
      scheduledExams[index].selected = !scheduledExams[index].selected;
      saveState();
    }

    // Start all selected exams.
    function startSelectedExams() {
      var selectedExams = scheduledExams.filter(exam => exam.selected);
      if (selectedExams.length === 0) {
        alert("Please select at least one exam to start.");
        return;
      }
      // Remove the selected exams from the scheduled list.
      scheduledExams = scheduledExams.filter(exam => !exam.selected);
      var now = new Date();
      selectedExams.forEach(function(exam) {
        // Start immediately.
        var startTime = new Date(now);
        var endTime = new Date(startTime.getTime() + exam.duration * 60000);
        runningExams.push({ name: exam.name, duration: exam.duration, startTime: startTime, endTime: endTime, timerInterval: null });
      });
      updateScheduledExamDisplay();
      displayRunningExams();
      saveState();
    }

    // Display running exams with live countdown.
    function displayRunningExams() {
      var runningDiv = document.getElementById("runningExamList");
      runningDiv.innerHTML = "";
      document.getElementById("runningExamsSection").style.display = runningExams.length > 0 ? "block" : "none";
      
      runningExams.forEach(function(exam, index) {
        var examDiv = document.createElement("div");
        examDiv.className = "running-exam";
        examDiv.id = "runningExam_" + index;
        examDiv.innerHTML = `
          <h3>${exam.name}</h3>
          <p><strong>Duration:</strong> ${exam.duration} min</p>
          <p><strong>Start:</strong> ${exam.startTime.toLocaleTimeString()}</p>
          <p><strong>End:</strong> ${exam.endTime.toLocaleTimeString()}</p>
          <p><strong>Remaining:</strong> <span id="remaining_${index}" class="time-display"></span></p>
        `;
        runningDiv.appendChild(examDiv);
      });
      startTimers();
      saveState();
    }

    // Start countdown timers for each running exam.
    function startTimers() {
      runningExams.forEach(function(exam, index) {
        if (!exam.timerInterval) {
          exam.timerInterval = setInterval(function() { updateTimer(index); }, 1000);
        }
      });
    }

    // Update the countdown timer for a given running exam.
    function updateTimer(index) {
      var exam = runningExams[index];
      var now = new Date();
      var remainingElement = document.getElementById(`remaining_${index}`);
      var examDiv = document.getElementById(`runningExam_${index}`);
      
      examDiv.classList.remove("warning30", "warning15", "warning5");
      var diff = exam.endTime - now;
      if (diff <= 0) {
        remainingElement.innerText = "Exam ended.";
        clearInterval(exam.timerInterval);
        exam.timerInterval = null;
      } else {
        var minutes = Math.floor(diff / 60000);
        var seconds = Math.floor((diff % 60000) / 1000);
        remainingElement.innerText = `${minutes} min ${seconds} sec`;
        var minutesLeft = diff / 60000;
        if (minutesLeft < 5) {
          examDiv.classList.add("warning5");
        } else if (minutesLeft < 15) {
          examDiv.classList.add("warning15");
        } else if (minutesLeft < 30) {
          examDiv.classList.add("warning30");
        }
      }
      saveState();
    }

    // Reset exam timer: stop all timers and clear state.
    function resetExamTimer() {
      if (!confirm("Are you sure you want to reset all exam timers? This action cannot be undone.")) {
        return;
      }
      runningExams.forEach(function(exam) {
        if (exam.timerInterval) {
          clearInterval(exam.timerInterval);
        }
      });
      runningExams = [];
      scheduledExams = [];
      updateScheduledExamDisplay();
      displayRunningExams();
      localStorage.removeItem('runningExams');
      localStorage.removeItem('scheduledExams');
      alert("Exam timer has been reset.");
    }

    // Navigate back to dashboard with no restrictions.
    function goToDashboard() {
      // Remove beforeunload event before navigating.
      window.onbeforeunload = null;
      window.location.href = "invigilator_dashboard.php";
    }

    // Theme toggle button handler.
    document.getElementById("themeToggle").addEventListener("click", function() {
      document.body.classList.toggle("light-mode");
    });
  </script>
</body>
</html>
