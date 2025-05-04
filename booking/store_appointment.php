<?php
// Make sure the upload directory exists
$uploadDir = __DIR__ . '/upload';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// File path
$csvFile = $uploadDir . '/appointments.csv';

// Get POST data safely
$name = $_POST['username'] ?? '';
$date = $_POST['appointmentDate'] ?? '';
$doctor = $_POST['doctor'] ?? '';
$symptoms = $_POST['symptoms'] ?? '';
$lvl = $_POST['lvl'] ?? '';

// Prepare CSV row
$row = [$name, $date, $doctor, $symptoms, $lvl, date("Y-m-d H:i:s")];

// Write to CSV file
$file = fopen($csvFile, 'a');
fputcsv($file, $row);
fclose($file);

// Optional: Redirect or confirm success
echo "<h2>Appointment Booked Successfully for</strong> $name </h2>";
header( "refresh:3;url=./book.html" ); 
?>
