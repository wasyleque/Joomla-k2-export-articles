<?php
$servername = "";
$username = "r";
$password = "";
$dbname = "";

// Tworzenie połączenia (Creating a connection)
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);

// Sprawdzanie połączenia (checking for connection)
if ($pdo->errorCode()) {
    die("Połączenie nieudane: " . $pdo->errorInfo()[2]);
}

// Pętla od 1 do 2372 (loop from 1 to 2372 ... You should change to Your last article number ID)
for ($k2item = 1; $k2item <= 2372; $k2item++) {
    // Generowanie nazwy pliku z użyciem md5 bez prefiksu katalogu
    $filename = md5("Image" . $k2item) . "_XL.jpg";
    $filepath = "media/k2/items/cache/" . $filename; 
    
    // Sprawdzenie, czy plik istnieje (checking if the file exist)
    if (file_exists($filepath)) {
        // Pobieranie tytułu z tabeli jos_k2_items
        $stmt = $pdo->prepare('SELECT title FROM jos_k2_items WHERE id = ?');
        $stmt->execute([$k2item]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $current_title = $result['title'];

            // Sprawdzanie, czy istnieje wpis w tabeli jos_content z tym samym tytułem (Checking if there is an entry in the jos_content table with the same title)
            $stmt = $pdo->prepare('SELECT id FROM jos_content WHERE title = ?');
            $stmt->execute([$current_title]);
            $content_result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($content_result) {
                $content_id = $content_result['id'];

                // Tworzenie nowej wartości dla kolumny images (Creating a new value for the images column)
                $image_data = json_encode([
                    "image_intro" => "media/k2/items/cache/{$filename}#joomlaImage://media/k2/items/cache/{$filename}?width=auto&height=auto",
                    "image_intro_alt" => "",
                    "float_intro" => "",
                    "image_intro_caption" => "",
                    "image_fulltext" => "",
                    "image_fulltext_alt" => "",
                    "float_fulltext" => "",
                    "image_fulltext_caption" => ""
                ]);

                // Aktualizowanie kolumny images w tabeli jos_content (Updating the images column in the jos_content table)
                $stmt = $pdo->prepare('UPDATE jos_content SET images = ? WHERE id = ?');
                $stmt->execute([$image_data, $content_id]);
                echo 'UPDATE jos_content SET images = ' . $image_data . ' WHERE id = ' . $content_id . '<br>';
            }
        }

        // Wyświetlenie ścieżki do pliku (for debug - displaying path to files)
        echo $k2item.' - '.$filename.'<br>';
    }
}
?>
