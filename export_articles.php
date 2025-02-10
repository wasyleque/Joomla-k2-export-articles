<?php
$servername = "7";
$username = "";
$password = "";
$dbname = "";


// Tworzenie połączenia
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzanie połączenia
if ($conn->connect_error) {
    die("Połączenie nieudane: " . $conn->connect_error);
}

// Mapowanie kategorii
$category_map = [
    1 => 8,
    2 => 9,
    3 => 2,
    4 => 8,
    5 => 9,
    6 => 10,
    7 => 11,
    8 => 12,
    9 => 8,
    10 => 14,
    11 => 15,
    12 => 21,
    13 => 22,
    14 => 23,
    15 => 24
];

// Pobieranie danych z tabeli jos_k2_items
$sql = "SELECT * FROM jos_k2_items";
$result = $conn->query($sql);

// Otwarcie pliku logu
$log_file = fopen("error_log.txt", "a");

if ($result->num_rows > 0) {
    // Przechodzenie przez każdy wiersz
    while ($row = $result->fetch_assoc()) {
        $catid = isset($category_map[$row['catid']]) ? $category_map[$row['catid']] : $row['catid'];
        $title = $conn->real_escape_string($row['title']);
        $alias = $conn->real_escape_string($row['alias']);
        $introtext = $conn->real_escape_string($row['introtext']);
        $fulltext = $conn->real_escape_string($row['fulltext']);
        $created_by_alias = $conn->real_escape_string($row['created_by_alias']);
        $metakey = $conn->real_escape_string($row['metakey']);
        $metadesc = $conn->real_escape_string($row['metadesc']);
        $metadata = $conn->real_escape_string($row['metadata']);
        $language = $conn->real_escape_string($row['language']);

        $insert_sql = "INSERT INTO `jos_content` (`id`, `asset_id`, `title`, `alias`, `introtext`, `fulltext`, `state`, `catid`, `created`, `created_by`, `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `images`, `urls`, `attribs`, `version`, `ordering`, `metakey`, `metadesc`, `access`, `hits`, `metadata`, `featured`, `language`, `note`) VALUES (null, '0', '{$title}', '{$alias}', '{$introtext}', '{$fulltext}', '{$row['published']}', '{$catid}', '{$row['created']}', '{$row['created_by']}', '{$created_by_alias}', '{$row['modified']}', '{$row['modified_by']}', '{$row['checked_out']}', '{$row['checked_out_time']}', '{$row['publish_up']}', '{$row['publish_down']}', '', '', '', '1', '0', '{$metakey}', '{$metadesc}', '{$row['access']}', '{$row['hits']}', '{$metadata}', '{$row['featured']}', '{$language}', '')";

        // Zapisanie pełnego zapytania SQL do pliku logu
        fwrite($log_file, $insert_sql . "\n");

        if ($conn->query($insert_sql) !== TRUE) {
            // Logowanie bardziej szczegółowych komunikatów o błędach
            echo "Błąd: " . $insert_sql . "<br>" . $conn->error . "<br>";
        }
    }
    echo "Dane zostały przeniesione pomyślnie";
} else {
    echo "Brak danych do przeniesienia";
}

// Zamknięcie pliku logu
fclose($log_file);

$conn->close();
?>

