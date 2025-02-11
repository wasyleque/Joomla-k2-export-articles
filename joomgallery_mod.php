<?php
// Put Your credentials here
$servername = "";
$username = "";
$password = "";
$dbname = "";

// Tworzenie połączenia (creating connection)
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzanie połączenia (checking connection)
if ($conn->connect_error) {
    die("Połączenie nieudane: " . $conn->connect_error);
}

// Krok 1: Przeszukiwanie tabeli jos_content (step 1 - searching for jos_content)
$result = $conn->query("SELECT `id`, `fulltext` FROM `jos_content` WHERE `fulltext` LIKE '%joomplucat%'");

if ($result === false) {
    die("Błąd zapytania do tabeli jos_content: " . $conn->error);
}

while ($row = $result->fetch_assoc()) {
    // Krok 2: Wyodrębnienie numeru po dwukropku i parametrów limit oraz columns (step 2 - Extracting the number after the colon and the limit and columns parameters)
    preg_match_all("/joomplucat:(\d+) limit=(\d+)\|columns=(\d+)/", $row['fulltext'], $matches);
    foreach ($matches[1] as $index => $cid) {
        $limit = $matches[2][$index];
        $columns = $matches[3][$index];

        // Krok 3: Uzyskanie ścieżki katalogu (step 3 - getting path to folder)
        $cat_result = $conn->query("SELECT catpath FROM jos_joomgallery_catg WHERE cid = $cid");
        if ($cat_result === false) {
            echo "Błąd zapytania do tabeli jos_joomgallery_catg dla id = $cid: " . $conn->error;
            continue;
        }
        $cat_row = $cat_result->fetch_assoc();
        if ($cat_row === null) {
            echo "Nie znaleziono wpisu o id = $cid w tabeli jos_joomgallery_catg - error path not found";
            continue;
        }
        $folder_path = $cat_row['catpath'];

        // Krok 4: Generowanie mini galerii (Step 4: Generating a mini gallery)
        $images_query = "SELECT imgfilename, imgthumbname FROM jos_joomgallery WHERE catid = $cid";
        if ($limit > 0) {
            $images_query .= " LIMIT $limit";
        }
        $images_result = $conn->query($images_query);
        if ($images_result === false) {
            echo "Błąd zapytania do tabeli jos_joomgallery dla catid = $cid: " . $conn->error;
            continue;
        }
        $gallery_html = '<div class="gallery" style="display: grid; grid-template-columns: repeat(' . $columns . ', 1fr); gap: 10px;">';
        while ($image_row = $images_result->fetch_assoc()) {
            $original_path = "./images/joomgallery/originals/$folder_path/" . $image_row['imgfilename'];
            $thumb_path = "./images/joomgallery/thumbnails/$folder_path/" . $image_row['imgthumbname'];
            $gallery_html .= '<a href="' . $original_path . '"><img src="' . $thumb_path . '" alt="" target="_top"></a>';
        }
        $gallery_html .= '</div>';

        // Krok 5: Zastępowanie wiersza w tabeli jos_content (Step 5: Replacing a row in the jos_content table)
        $pattern = "{joomplucat:$cid limit=$limit|columns=$columns}";
        $new_fulltext = str_replace($pattern, $gallery_html, $row['fulltext']);
        $conn->query("UPDATE `jos_content` SET `fulltext` = '$new_fulltext' WHERE id = " . $row['id']);
		echo  "UPDATE `jos_content` SET `fulltext` = '$new_fulltext' WHERE id = " . $row['id']."<br><hr><br>";
        #echo $gallery_html.'<br><hr><br>';
    }
}

$conn->close();
?>
