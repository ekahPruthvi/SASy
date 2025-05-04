<?php
header("refresh: 3"); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Smart Appointment Scheduler</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="sidebar">
    <h2>SASy</h2>
    <div class="nav-link">Dashboard</div>
    <a href="./appt.php" class="nav-link">Appointments</a>
  </div>
  <div class="main">
    <div class="header">
      <h1>Dashboard</h1>
      <a href="./booking/book.html"><button class="btn-primary" id="bookBtn">Book Appointment</button></a>
    </div>
    <div class="cards">
      <div class="card">
        <p>Total Appointments</p>
        <?php
          $csvFile = __DIR__ . '/booking/upload/appointments.csv';
          $lines = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); 
          $line_count = count($lines); 
          echo '<h2>'. $line_count. '</h2>';
        ?>
        <small>This week</small>
      </div>
      <div class="card">
        <p>Active Patients</p>
        <h2>2,856</h2>
        <small>In system</small>
      </div>
      <div class="card">
        <p>Avg. Wait Time</p>
        <h2>12 min</h2>
        <small>This month</small>
      </div>
    </div>
    <div class="section">
      <div class="panel">
        <h3>Upcoming Appointments</h3>
        <?php
          $csvFile = __DIR__ . '/booking/upload/appointments.csv';
          $doctorCounts = [];

          if (file_exists($csvFile) && filesize($csvFile) > 0) {
              if (($handle = fopen($csvFile, "r")) !== FALSE) {
                  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                      $doctor = $data[2]; // doctor name
                      if (!isset($doctorCounts[$doctor])) {
                          $doctorCounts[$doctor] = 0;
                      }
                      $doctorCounts[$doctor]++;
                  }
                  fclose($handle);
              }

              foreach ($doctorCounts as $doctor => $count) {
                  echo '<div class="appt">';
                  echo '<div class="info">' . htmlspecialchars($doctor) . '</div>';
                  echo '<div class="status-confirmed">' . $count . ' appointment' . ($count > 1 ? 's' : '') . '</div>';
                  echo '</div>';
              }
          } else {
              echo '<div>No upcoming appointments found.</div>';
          }
        ?>
        <a href="./appt.php"><button class="btn-primary" style="margin-top: 15px;">View All Appointments</button></a>
      </div>
      <div class="panel">
        <h3>Doctors on Duty</h3>
        <div class="doctor">
          <div class="doctor-circle">JP</div>
          <div>
            <strong>Dr. John Pork</strong><br>
            <small>Genral Practice,Cardiology</small><br>
          </div>
        </div>
        <div class="doctor">
          <div class="doctor-circle">MD</div>
          <div>
            <strong>Dr. Moo Deng</strong><br>
            <small>Neurology</small><br>
          </div>
        </div>
        <div class="doctor">
          <div class="doctor-circle">PP</div>
          <div>
            <strong>Dr. Priya Patel</strong><br>
            <small>Pediatrics</small><br>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="script.js"></script>
</body>
</html>
