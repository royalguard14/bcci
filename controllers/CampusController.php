<?php 
require_once 'BaseController.php'; 
class CampusController extends BaseController { 
    public function __construct($db) { 
     parent::__construct($db, ['14']); 
 }


public function showCampusProfile() {
    try {
        // Fetch academic years
        $stmt = $this->db->prepare("SELECT * FROM academic_year ORDER BY start ASC");
        $stmt->execute();
        $academicYear = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch campus information
        $stmt = $this->db->prepare("SELECT * FROM campus_info WHERE id NOT IN (0)");
        $stmt->execute();
        $campusInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // Pass both datasets to the view
        include 'views/campus/profile.php';
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: /BCCI/campus-profile");
        exit();
    }
}




public function createScheduleForSemester() {
    // Step 1: Query to fetch all semester data (course_id, semester, subject_ids)
    $stmt = $this->db->prepare("SELECT course_id, semester, subject_ids FROM semester");
    $stmt->execute();
    $semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Step 2: Filter out rows with empty subject_ids
    $filteredSemesters = array_filter($semesters, function($semester) {
        return !empty($semester['subject_ids']);
    });

    // Step 3: Fetch academic year info
    $stmt = $this->db->prepare("SELECT function FROM campus_info WHERE id = 5");
    $stmt->execute();
    $academicYear = $stmt->fetchColumn(); // Get the academic year function

    // Step 4: Process each semester individually
    foreach ($filteredSemesters as $semester) {
        $courseId = $semester['course_id'];
        $subjectIds = explode(',', $semester['subject_ids']); // Split the subject_ids into an array

        // Fetch the details of the subjects for this semester
        $stmt = $this->db->prepare("SELECT id, unit_lec, unit_lab FROM subjects WHERE id IN (" . implode(',', array_map('intval', $subjectIds)) . ")");
        $stmt->execute();
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch campus information (Operating Days and Time Slots)
        $stmt = $this->db->prepare("SELECT * FROM campus_info WHERE id IN (3, 4)");
        $stmt->execute();
        $campusInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $operatingDays = [];
        $timeSlots = '';

        // Process campus info
        foreach ($campusInfo as $info) {
            if ($info['id'] == 3) {
                $operatingDays = explode(',', $info['function']); // Example: ["Monday", "Tuesday", "Wednesday", ...]
            } elseif ($info['id'] == 4) {
                $timeSlots = $info['function']; // Example: "6:00AM-9:00PM"
            }
        }

        // Parse time slots
        [$startTime, $endTime] = explode('-', $timeSlots);
        $startTime = strtotime($startTime);
        $endTime = strtotime($endTime);

        // Generate time slots with 1.5-hour intervals
        $timeSlotIntervals = [];
        $currentTime = $startTime;
        while ($currentTime + 5400 <= $endTime) { // 5400 seconds = 1.5 hours
            $timeSlotIntervals[] = date('h:iA', $currentTime) . '-' . date('h:iA', $currentTime + 5400);
            $currentTime += 5400;
        }

        // Create an empty schedule for this semester
        $emptySchedule = [];
        foreach ($operatingDays as $day) {
            foreach ($timeSlotIntervals as $slot) {
                $emptySchedule[] = [
                    'day' => $day,
                    'time' => $slot,
                    'subject' => null, // Placeholder for subjects
                ];
            }
        }

        // Randomly allocate subjects to the empty schedule based on unit_lec and unit_lab for this semester
        $schedule = [];
        $availableSlots = $emptySchedule; // All available slots to be filled
        shuffle($availableSlots); // Shuffle the available slots to randomize allocation

        // Process each subject
        foreach ($subjects as $subject) {
            $subjectId = $subject['id'];
            $lecUnits = $subject['unit_lec'];
            $labUnits = $subject['unit_lab'];

            // Step 5: Count the existing number of sessions for the same combination of academic_id, course_id, semester, subject_id
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM schedules WHERE academic_id = :academic_id AND course_id = :course_id AND semester = :semester AND subject_id = :subject_id");
            $stmt->execute([
                ':academic_id' => $academicYear,
                ':course_id' => $courseId,
                ':semester' => $semester['semester'],
                ':subject_id' => $subjectId,
            ]);
            $existingSessionsCount = $stmt->fetchColumn();

            // Calculate the batch number by dividing the existing sessions by 2
            $batchNumber = floor($existingSessionsCount / 2) + 1;

            // Allocate lecture hours
            $lecSessions = ceil($lecUnits / 1.5);
            for ($i = 0; $i < $lecSessions; $i++) {
                $slot = array_pop($availableSlots); // Take a slot for lecture
                $schedule[] = [
                    'subject_id' => $subjectId,
                    'day' => $slot['day'],
                    'time_slot' => $slot['time'],
                    'type' => 'Lecture',
                    'batch' => $batchNumber, // Assign calculated batch number
                ];
            }

            // Allocate lab hours
            $labSessions = ceil($labUnits / 1.5);
            for ($i = 0; $i < $labSessions; $i++) {
                $slot = array_pop($availableSlots); // Take a slot for lab
                $schedule[] = [
                    'subject_id' => $subjectId,
                    'day' => $slot['day'],
                    'time_slot' => $slot['time'],
                    'type' => 'Lab',
                    'batch' => $batchNumber, // Assign calculated batch number
                ];
            }
        }

        // Insert the generated schedule into the database
        foreach ($schedule as $entry) {
            // Validation: Check for duplicate schedules
            $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM schedules WHERE academic_id = :academic_id AND course_id = :course_id AND semester = :semester AND subject_id = :subject_id AND day = :day AND time_slot = :time_slot");
            $checkStmt->execute([
                ':academic_id' => $academicYear,
                ':course_id' => $courseId,
                ':semester' => $semester['semester'],
                ':subject_id' => $entry['subject_id'],
                ':day' => $entry['day'],
                ':time_slot' => $entry['time_slot'],
            ]);

            $exists = $checkStmt->fetchColumn();

            // Only insert if the schedule does not exist
            if ($exists == 0) {
                $stmt = $this->db->prepare("INSERT INTO schedules (academic_id, course_id, semester, subject_id, day, time_slot, session_type, batch) VALUES (:academic_id, :course_id, :semester, :subject_id, :day, :time_slot, :type, :batch)");
                $stmt->execute([
                    ':academic_id' => $academicYear,
                    ':course_id' => $courseId,
                    ':semester' => $semester['semester'],
                    ':subject_id' => $entry['subject_id'],
                    ':day' => $entry['day'],
                    ':time_slot' => $entry['time_slot'],
                    ':type' => $entry['type'],
                    ':batch' => $entry['batch'],
                ]);
            }
        }
    }
}




public function createScheduleForSemester2() {
    // Step 1: Query to fetch all semester data (course_id, semester, subject_ids)
    $stmt = $this->db->prepare("SELECT course_id, semester, subject_ids FROM semester");
    $stmt->execute();
    $semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Step 2: Filter out rows with empty subject_ids
    $filteredSemesters = array_filter($semesters, function($semester) {
        return !empty($semester['subject_ids']);
    });

    // Step 3: Fetch academic year info
    $stmt = $this->db->prepare("SELECT function FROM campus_info WHERE id = 5");
    $stmt->execute();
    $academicYear = $stmt->fetchColumn(); // Get the academic year function

    // Step 4: Process each semester individually
    foreach ($filteredSemesters as $semester) {
        $courseId = $semester['course_id'];
        $subjectIds = explode(',', $semester['subject_ids']); // Split the subject_ids into an array

        // Fetch the details of the subjects for this semester
        $stmt = $this->db->prepare("SELECT id, unit_lec, unit_lab FROM subjects WHERE id IN (" . implode(',', array_map('intval', $subjectIds)) . ")");
        $stmt->execute();
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch campus information (Operating Days and Time Slots)
        $stmt = $this->db->prepare("SELECT * FROM campus_info WHERE id IN (3, 4)");
        $stmt->execute();
        $campusInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $operatingDays = [];
        $timeSlots = '';

        // Process campus info
        foreach ($campusInfo as $info) {
            if ($info['id'] == 3) {
                $operatingDays = explode(',', $info['function']); // Example: ["Monday", "Tuesday", "Wednesday", ...]
            } elseif ($info['id'] == 4) {
                $timeSlots = $info['function']; // Example: "6:00AM-9:00PM"
            }
        }

        // Parse time slots
        [$startTime, $endTime] = explode('-', $timeSlots);
        $startTime = strtotime($startTime);
        $endTime = strtotime($endTime);

        // Generate time slots with 1.5-hour intervals
        $timeSlotIntervals = [];
        $currentTime = $startTime;
        while ($currentTime + 5400 <= $endTime) { // 5400 seconds = 1.5 hours
            $timeSlotIntervals[] = date('h:iA', $currentTime) . '-' . date('h:iA', $currentTime + 5400);
            $currentTime += 5400;
        }

        // Create an empty schedule for this semester
        $emptySchedule = [];
        foreach ($operatingDays as $day) {
            foreach ($timeSlotIntervals as $slot) {
                $emptySchedule[] = [
                    'day' => $day,
                    'time' => $slot,
                    'subject' => null, // Placeholder for subjects
                ];
            }
        }

        // Randomly allocate subjects to the empty schedule based on unit_lec and unit_lab for this semester
        $schedule = [];
        $availableSlots = $emptySchedule; // All available slots to be filled
        shuffle($availableSlots); // Shuffle the available slots to randomize allocation

        foreach ($subjects as $subject) {
            $subjectId = $subject['id'];
            $lecUnits = $subject['unit_lec'];
            $labUnits = $subject['unit_lab'];

            // Allocate lecture hours
            $lecSessions = ceil($lecUnits / 1.5);

            for ($i = 0; $i < $lecSessions; $i++) {
                $slot = array_pop($availableSlots);
                $schedule[] = [
                    'subject_id' => $subjectId,
                    'day' => $slot['day'],
                    'time_slot' => $slot['time'],
                    'type' => 'Lecture',
                ];
            }

            // Allocate lab hours
            $labSessions = ceil($labUnits / 1.5);

            for ($i = 0; $i < $labSessions; $i++) {
                $slot = array_pop($availableSlots);
                $schedule[] = [
                    'subject_id' => $subjectId,
                    'day' => $slot['day'],
                    'time_slot' => $slot['time'],
                    'type' => 'Lab',
                ];
            }
        }

        // Insert the generated schedule into the database
        foreach ($schedule as $entry) {
            $stmt = $this->db->prepare("INSERT INTO schedules (academic_id, course_id, semester, subject_id, day, time_slot, session_type) VALUES (:academic_id, :course_id, :semester, :subject_id, :day, :time_slot, :type)");
            $stmt->execute([
                ':academic_id' => $academicYear,
                ':course_id' => $courseId,
                ':semester' => $semester['semester'],
                ':subject_id' => $entry['subject_id'],
                ':day' => $entry['day'],
                ':time_slot' => $entry['time_slot'],
                ':type' => $entry['type'],
            ]);
        }
    }
}



public function createScheduleForSemester1() {
    // Step 1: Query to fetch all semester data (course_id, semester, subject_ids)
    $stmt = $this->db->prepare("SELECT course_id, semester, subject_ids FROM semester");
    $stmt->execute();

    // Step 2: Fetch the data as an associative array
    $semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Step 3: Filter out rows with empty subject_ids
    $filteredSemesters = array_filter($semesters, function($semester) {
        return !empty($semester['subject_ids']);
    });

    // Step 4: Process each semester individually (no merging)
    foreach ($filteredSemesters as $semester) {
        $courseId = $semester['course_id'];
        $subjectIds = explode(',', $semester['subject_ids']); // Split the subject_ids into an array

        // Step 5: Fetch the details of the subjects for this semester
        $stmt = $this->db->prepare("SELECT id, unit_lec, unit_lab FROM subjects WHERE id IN (" . implode(',', array_map('intval', $subjectIds)) . ")");
        $stmt->execute();
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Step 6: Fetch campus information (Operating Days and Time Slots)
        $stmt = $this->db->prepare("SELECT * FROM campus_info WHERE id IN (3, 4)");
        $stmt->execute();
        $campusInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize variables
        $operatingDays = [];
        $timeSlots = '';

        // Process campus info
        foreach ($campusInfo as $info) {
            if ($info['id'] == 3) {
                $operatingDays = explode(',', $info['function']); // Example: ["Monday", "Tuesday", "Wednesday", ...]
            } elseif ($info['id'] == 4) {
                $timeSlots = $info['function']; // Example: "6:00AM-9:00PM"
            }
        }

        // Parse time slots
        [$startTime, $endTime] = explode('-', $timeSlots);
        $startTime = strtotime($startTime);
        $endTime = strtotime($endTime);

        // Generate time slots with 1.5-hour intervals (5400 seconds)
        $timeSlotIntervals = [];
        $currentTime = $startTime;
        while ($currentTime + 5400 <= $endTime) { // 5400 seconds = 1.5 hours
            $timeSlotIntervals[] = date('h:iA', $currentTime) . '-' . date('h:iA', $currentTime + 5400);
            $currentTime += 5400; // Increment by 1.5 hours
        }

        // Step 7: Create an empty schedule for this semester
        $emptySchedule = [];
        foreach ($operatingDays as $day) {
            foreach ($timeSlotIntervals as $slot) {
                $emptySchedule[] = [
                    'day' => $day,
                    'time' => $slot,
                    'subject' => null, // Placeholder for subjects
                ];
            }
        }

        // Step 8: Randomly allocate subjects to the empty schedule based on unit_lec and unit_lab for this semester
        $schedule = [];
        $availableSlots = $emptySchedule; // All available slots to be filled
        shuffle($availableSlots); // Shuffle the available slots to randomize allocation

        foreach ($subjects as $subject) {
            $subjectId = $subject['id'];
            $lecUnits = $subject['unit_lec'];
            $labUnits = $subject['unit_lab'];

            // Allocate lecture hours
            $lecSessions = ceil($lecUnits / 1.5); // Calculate number of sessions, rounding up to ensure full sessions

            // Allocate the lecture sessions
            for ($i = 0; $i < $lecSessions; $i++) {
                $slot = array_pop($availableSlots); // Get a random available slot
                $schedule[] = [
                    'subject_id' => $subjectId,
                    'day' => $slot['day'],
                    'time_slot' => $slot['time'],
                    'type' => 'Lecture', // Type of session
                ];
            }

            // Allocate lab hours (if any)
            $labSessions = ceil($labUnits / 1.5); // Calculate number of sessions, rounding up to ensure full sessions
            for ($i = 0; $i < $labSessions; $i++) {
                $slot = array_pop($availableSlots); // Get a random available slot
                $schedule[] = [
                    'subject_id' => $subjectId,
                    'day' => $slot['day'],
                    'time_slot' => $slot['time'],
                    'type' => 'Lab', // Type of session
                ];
            }
        }

        // Step 9: Display the generated schedule for the current semester
        echo "<h2>Schedule for Course ID: {$courseId}, Semester: {$semester['semester']}</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Subject ID</th>
                    <th>Day</th>
                    <th>Time Slot</th>
                    <th>Session Type</th>
                </tr>";

        foreach ($schedule as $entry) {
            echo "<tr>
                    <td>{$entry['subject_id']}</td>
                    <td>{$entry['day']}</td>
                    <td>{$entry['time_slot']}</td>
                    <td>{$entry['type']}</td>
                  </tr>";
        }
        echo "</table>";
    }
}




//merge all
public function getAllSemesterData()
{
    // Step 1: Query to fetch all semester data (course_id, semester, subject_ids)
    $stmt = $this->db->prepare("SELECT course_id, semester, subject_ids FROM semester");
    $stmt->execute();
    
    // Step 2: Fetch the data as an associative array
    $semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Step 3: Filter out rows with empty subject_ids
    $filteredSemesters = array_filter($semesters, function($semester) {
        return !empty($semester['subject_ids']);
    });

    // Step 4: Merge subject_ids by course_id
    $mergedData = [];
    foreach ($filteredSemesters as $semester) {
        $courseId = $semester['course_id'];
        $subjectIds = explode(',', $semester['subject_ids']); // Split the subject_ids into an array

        // Merge subject_ids for the same course_id
        if (isset($mergedData[$courseId])) {
            $mergedData[$courseId] = array_merge($mergedData[$courseId], $subjectIds);
        } else {
            $mergedData[$courseId] = $subjectIds;
        }
    }

    // Step 5: Remove duplicate subject_ids for each course
    foreach ($mergedData as $courseId => $subjectIds) {
        $mergedData[$courseId] = array_unique($subjectIds);
    }

    // Step 6: Display the merged data in an HTML table
    echo "<h2>Merged Semester Data by Course ID</h2>";
    echo "<table border='1'>
            <tr>
                <th>Course ID</th>
                <th>Semester(s)</th>
                <th>Merged Subject IDs</th>
            </tr>";
    
    // Step 7: Loop through the merged data and display each row in the table
    foreach ($mergedData as $courseId => $subjectIds) {
        $subjectIdsList = implode(',', $subjectIds); // Convert the subject_ids array to a comma-separated string
        echo "<tr>
                <td>{$courseId}</td>
                <td>" . implode(", ", array_column(array_filter($semesters, fn($s) => $s['course_id'] == $courseId), 'semester')) . "</td>
                <td>{$subjectIdsList}</td>
              </tr>";
    }
    echo "</table>";
}






public function createEmptyScheduleWithSubjects() {
    // Step 1: Query to fetch all semester data (course_id, semester, subject_ids)
    $stmt = $this->db->prepare("SELECT course_id, semester, subject_ids FROM semester");
    $stmt->execute();

    // Step 2: Fetch the data as an associative array
    $semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Step 3: Filter out rows with empty subject_ids
    $filteredSemesters = array_filter($semesters, function($semester) {
        return !empty($semester['subject_ids']);
    });

    // Step 4: Merge subject_ids by course_id
    $mergedData = [];
    foreach ($filteredSemesters as $semester) {
        $courseId = $semester['course_id'];
        $subjectIds = explode(',', $semester['subject_ids']); // Split the subject_ids into an array

        // Merge subject_ids for the same course_id
        if (isset($mergedData[$courseId])) {
            $mergedData[$courseId] = array_merge($mergedData[$courseId], $subjectIds);
        } else {
            $mergedData[$courseId] = $subjectIds;
        }
    }

    // Step 5: Remove duplicate subject_ids for each course
    foreach ($mergedData as $courseId => $subjectIds) {
        $mergedData[$courseId] = array_unique($subjectIds);
    }

    // Step 6: Fetch subject details for unit_lec and unit_lab (e.g., from the subject table)
    $subjectDetails = [];
    $stmt = $this->db->prepare("SELECT id, unit_lec, unit_lab FROM subjects WHERE id IN (" . implode(',', array_map('intval', array_merge(...array_values($mergedData)))) . ")");
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($subjects as $subject) {
        $subjectDetails[$subject['id']] = [
            'unit_lec' => $subject['unit_lec'],
            'unit_lab' => $subject['unit_lab'],
            'total_units' => $subject['unit_lec'] + $subject['unit_lab']
        ];
    }

    // Step 7: Prepare the schedule with calculated hours
    $schedule = [];
    foreach ($mergedData as $courseId => $subjectIds) {
        foreach ($subjectIds as $subjectId) {
            // Get the total units (lecture + lab)
            $totalUnits = $subjectDetails[$subjectId]['total_units'];
            $lecUnits = $subjectDetails[$subjectId]['unit_lec'];
            $labUnits = $subjectDetails[$subjectId]['unit_lab'];

            // Each unit represents 1 hour, so total hours per week for this subject
            $hoursPerWeek = $totalUnits; // Total units = total weekly hours

            // Add subject with total hours per week to the schedule
            $schedule[] = [
                'course_id' => $courseId,
                'subject_id' => $subjectId,
                'total_hours_per_week' => $hoursPerWeek,
                'lec_units' => $lecUnits,
                'lab_units' => $labUnits
            ];
        }
    }

    // Step 8: Display the schedule in an HTML table
    echo "<h2>Course Schedule</h2>";
    echo "<table border='1'>
            <tr>
                <th>Course ID</th>
                <th>Subject ID</th>
                <th>Total Hours per Week</th>
                <th>Lecture Units</th>
                <th>Lab Units</th>
            </tr>";

    foreach ($schedule as $entry) {
        echo "<tr>
                <td>{$entry['course_id']}</td>
                <td>{$entry['subject_id']}</td>
                <td>{$entry['total_hours_per_week']} hours</td>
                <td>{$entry['lec_units']} hours</td>
                <td>{$entry['lab_units']} hours</td>
              </tr>";
    }
    echo "</table>";
}















public function addCampusSchoolYear() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        session_start(); // Start session for error handling
        $startYear = isset($_POST['start_school_year']) ? (int)$_POST['start_school_year'] : null;
        $endYear = isset($_POST['end_school_year']) ? (int)$_POST['end_school_year'] : null;

        // Validate input
        if (!$startYear || !$endYear || $startYear >= $endYear) {
            $_SESSION['error'] = "Error: Invalid school year range.";
            header("Location: /BCCI/campus-profile");
            exit();
        }

        try {
            // Check if the start year is unique
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM academic_year WHERE start = :start");
            $stmt->bindParam(':start', $startYear, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $_SESSION['error'] = "Error: Start year already exists.";
                header("Location: /BCCI/campus-profile");
                exit();
            }

            // Insert into academic_year table
            $stmt = $this->db->prepare("INSERT INTO academic_year (start, end) VALUES (:start, :end)");
            $stmt->bindParam(':start', $startYear, PDO::PARAM_INT);
            $stmt->bindParam(':end', $endYear, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION['success'] = "School year added successfully!";
                header("Location: /BCCI/campus-profile");
                exit();
            } else {
                $_SESSION['error'] = "Error: Could not add school year.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }

        // Redirect back with error
        header("Location: /BCCI/campus-profile");
        exit();
    }
}



public function deleteCampusSchoolYear() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = isset($_POST['sy_id']) ? (int)$_POST['sy_id'] : null;

        // Validate input
        if (!$id) {
            $_SESSION['error'] = "Error: Invalid school year ID.";
            header("Location: /BCCI/campus-profile");
            exit();
        }

        // Delete the school year
        $stmt = $this->db->prepare("DELETE FROM academic_year WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "School year deleted successfully!";
            header("Location: /BCCI/campus-profile");
            exit();
        } else {
            $_SESSION['error'] = "Error: Could not delete school year.";
            header("Location: /BCCI/campus-profile");
            exit();
        }
    }
}



public function updateCampusInfo() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['campus_info'])) {
        // Loop through the posted data and update each record

  

        foreach ($_POST['campus_info'] as $id => $function) {
            // Check if the value is an array (e.g., for Operating Days)
            if (is_array($function)) {
                $function = implode(',', $function); // Convert array to a comma-separated string
            } else {
                $function = trim($function); // Clean the input value
            }

            // Validate the input
      if ($function === '' && $function !== '0') {
    $_SESSION['error'] = "Error: All fields must be filled in.";
    header("Location: /BCCI/campus-profile");
    exit();
}
            try {
                // Update the record in the database
                $stmt = $this->db->prepare("UPDATE campus_info SET function = :function WHERE id = :id");
                $stmt->bindParam(':function', $function, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                if (!$stmt->execute()) {
                    $_SESSION['error'] = "Error: Could not update campus info.";
                    header("Location: /BCCI/campus-profile");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error: " . $e->getMessage();
                header("Location: /BCCI/campus-profile");
                exit();
            }
        }

        $_SESSION['success'] = "Campus info updated successfully!";
        header("Location: /BCCI/campus-profile");
        exit();
    }
}









}
?>