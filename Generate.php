<?php

// Function to generate random dates within a range
function randomDate($startDate, $endDate) {
    $startTimestamp = strtotime($startDate);
    $endTimestamp = strtotime($endDate);
    $randomTimestamp = mt_rand($startTimestamp, $endTimestamp);
    return date('Y-m-d', $randomTimestamp);
}

// Function to generate random float values within a range
function randomFloat($min, $max) {
    return round($min + mt_rand() / mt_getrandmax() * ($max - $min), 2);
}

// List of valid machine IDs (based on your machine table)
$validMachineIds = [5, 10, 11, 12, 13, 14, 15, 16, 17];

// List of valid technician IDs (based on your technicien table)
$validTechnicienIds = [6, 7];

// List of possible status values (matching StatutMaintenance enum)
$statusValues = [
    'EN_ATTENTE'=>'En attente'  , // Attente de disponibilité de technicien
    'EN_COURS'=>'En cours' ,     // En cours de maintenance par un technicien
    'TERMINEE'=> 'Terminée' , 
        // Le technicien a terminé la maintenance
];

// Generate 200 rows of synthetic data
$data = [];
for ($i = 0; $i < 200; $i++) {
    // Generate random values
    $dateEntretien = randomDate('2022-01-01', '2023-12-31');
    $cout = randomFloat(50, 1000);
    $temperature = randomFloat(-10, 50);
    $humidite = randomFloat(0, 100);
    $consoCarburant = randomFloat(0, 100);
    $consoEnergie = randomFloat(0, 1000);
    $idMachine = $validMachineIds[array_rand($validMachineIds)];
    $idTechnicien = $validTechnicienIds[array_rand($validTechnicienIds)];

    // Calculate a score based on logical relationships
    $score = 0;

    // Higher cost increases the score
    $score += ($cout - 50) / 950 * 40; // Normalize cost to a score between 0 and 40

    // Extreme temperatures increase the score
    if ($temperature < 0 || $temperature > 40) {
        $score += 20; // Add 20 for extreme temperatures
    }

    // High humidity increases the score
    if ($humidite > 80) {
        $score += 10; // Add 10 for high humidity
    }

    // Higher fuel or energy consumption increases the score
    $score += ($consoCarburant / 100) * 15; // Normalize fuel consumption to a score between 0 and 15
    $score += ($consoEnergie / 1000) * 15; // Normalize energy consumption to a score between 0 and 15

    // Older maintenance dates increase the score
    $maintenanceDate = strtotime($dateEntretien);
    $currentDate = strtotime('2023-12-31');
    $daysSinceMaintenance = ($currentDate - $maintenanceDate) / (60 * 60 * 24);
    $score += min($daysSinceMaintenance / 365 * 10, 10); // Add up to 10 for older maintenance dates

    // Determine the Status based on the score
    $status = ($score > 50) ?  'terminée': 'en attente'; // Threshold of 50 for failure

    // Generate the row
    $row = [
        'dateEntretien' => $dateEntretien,
        'cout' => $cout,
        'temperature' => $temperature,
        'humidite' => $humidite,
        'consoCarburant' => $consoCarburant,
        'consoEnergie' => $consoEnergie,
        'status' => $status,
        'idMachine' => $idMachine,
        'idTechnicien' => $idTechnicien,
    ];
    $data[] = $row;
}

// Output the data as SQL INSERT statements
foreach ($data as $row) {
    echo "INSERT INTO `maintenance` (`date_entretien`, `cout`, `temperature`, `humidite`, `conso_carburant`, `conso_energie`, `status`, `id_machine_id`, `id_technicien_id`) VALUES (";
    echo "'" . $row['dateEntretien'] . "', ";
    echo $row['cout'] . ", ";
    echo $row['temperature'] . ", ";
    echo $row['humidite'] . ", ";
    echo $row['consoCarburant'] . ", ";
    echo $row['consoEnergie'] . ", ";
    echo "'" . $row['status'] . "', ";
    echo $row['idMachine'] . ", ";
    echo $row['idTechnicien'];
    echo ");\n";
}