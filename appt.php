<?php
header("refresh: 3");
?>

<?php
$filename = __DIR__ . '/booking/upload/appointments.csv';


$rows = array_map('str_getcsv', file($filename));

$header = null;
if (!empty($rows)) {
    $header = $rows[0];
    if (!is_numeric($rows[1][1])) {
        $data = array_slice($rows, 1);
    } else {
        $data = $rows;
        $header = null;
    }
}

$today = date('Y-m-d');

$filtered = array_filter($data, function($row) use ($today) {
    return $row[1] >= $today;
});

$fp = fopen($filename, 'w');

if ($header) {
    fputcsv($fp, $header);
}

foreach ($filtered as $row) {
    fputcsv($fp, $row);
}

fclose($fp);

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
    <a href="./index.php" class="nav-link">Dashboard</a>
    <div href="./appt.php" class="nav-link">Appointments</div>
  </div>
  <div class="main">
    <div class="header">
      <h1>Appointments</h1>
      <a href="./booking/book.html"><button class="btn-primary" id="bookBtn">Book Appointment</button></a>
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
    <?php
        $csvFile = __DIR__ . '/booking/upload/appointments.csv';
        $appointmentsByDoctor = [];

        if (file_exists($csvFile) && filesize($csvFile) > 0) {
            if (($handle = fopen($csvFile, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    list($name, $date, $doctor, $symptoms, $timestamp) = $data;
                    $appointmentsByDoctor[$doctor][] = [
                        'name' => $name,
                        'date' => $date,
                        'symptoms' => $symptoms,
                        'timestamp' => $timestamp,
                    ];
                }
                fclose($handle);
            }

            foreach ($appointmentsByDoctor as $doctor => $appointments) {
                echo "<h3>$doctor</h3>";
                echo "<table>";
                echo "<thead><tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Treatment for</th>
                        <th>Emergency Level</th>
                        </tr></thead><tbody>";

                foreach ($appointments as $appt) {
                    echo "<tr>
                            <td>" . htmlspecialchars($appt['name']) . "</td>
                            <td>" . htmlspecialchars($appt['date']) . "</td>
                            <td>" . htmlspecialchars($appt['symptoms']) . "</td>
                            <td>" . htmlspecialchars($appt['timestamp']) . "</td>
                            </tr>";
                }

                echo "</tbody></table>";
            }
        } else {
            echo "<div class='empty-message'>No appointments found.</div>";
        }
        ?>
  </div>
  <script src="script.js"></script>
</body>
</html>