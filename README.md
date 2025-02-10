K2 editor is the plugin for Joomla but it was supported till Joomla version 3
As You know now we have newer version of Joomla so ... Need to disable the plugin to succesfully upgrade Your Joomla CMS.
Unfortunately disabling K2 plugin is equally to drop all the articles which we made before.
So ... i created some scripts which are importing articles from K2 plugin straight to the Joomla articles manager.
------------------------------------------------------------------------------------------------------------------------------------------------
In first file export.php You need to edit few things:
$servername = "addres and port of Your mariadb server";
$username = "username with privilegues to modify database";
$password = "password to the database";
$dbname = "name of Your database";
------------------------------------------------------------------------------------------------------------------------------------------------
Than you should set category mapping if the ID`s are different with newly created categories:

// Mapowanie kategorii (category mappings)
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

// Pobieranie danych z tabeli jos_k2_items (retrieving data from jos_k2_items)
$sql = "SELECT * FROM jos_k2_items";
$result = $conn->query($sql);

// Otwarcie pliku logu (creating log file to catch errors)
$log_file = fopen("error_log.txt", "a");

if ($result->num_rows > 0) {
    // Przechodzenie przez każdy wiersz (loop with row after row parsing)
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

        // Zapisanie pełnego zapytania SQL do pliku logu (writing a loop queries to an log file)
        fwrite($log_file, $insert_sql . "\n");

        if ($conn->query($insert_sql) !== TRUE) {
            // Logowanie bardziej szczegółowych komunikatów o błędach (more error details for logs)
            echo "Błąd: " . $insert_sql . "<br>" . $conn->error . "<br>";
        }
    }
    echo "Dane zostały przeniesione pomyślnie - succesfull migration !"; #succesfull migration
} else {
    echo "Brak danych do przeniesienia - no data found";
}

// Zamknięcie pliku logu (close a log file and write)
fclose($log_file);

$conn->close();

------------------------------------------------------------------------------------------------------------------------------------------------
... but after that nothing still works so ...
in your hosting phpmyadmin page You should run those queries:
UPDATE `jos_menu` SET link = REPLACE(link, 'com_k2', 'com_content') WHERE link LIKE '%com_k2%';

UPDATE `jos_content` SET `metadata` = '{\"robots\":\"\",\"author\":\"\",\"rights\":\"\",\"xreference\":\"\"}'; 

UPDATE `jos_content` SET `attribs` = '{\"article_layout\":\"\",\"show_title\":\"\",\"link_titles\":\"\",\"show_tags\":\"\",\"show_intro\":\"\",\"info_block_position\":\"\",\"info_block_show_title\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_associations\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_vote\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"alternative_readmore\":\"\",\"article_page_title\":\"\",\"show_publishing_options\":\"\",\"show_article_options\":\"\",\"show_urls_images_backend\":\"\",\"show_urls_images_frontend\":\"\"}'; 

UPDATE `jos_content` SET `urls` = '{\"urla\":false,\"urlatext\":\"\",\"targeta\":\"\",\"urlb\":false,\"urlbtext\":\"\",\"targetb\":\"\",\"urlc\":false,\"urlctext\":\"\",\"targetc\":\"\"}'; 

UPDATE `jos_content` SET `images` = '{\"image_intro\":\"\",\"float_intro\":\"\",\"image_intro_alt\":\"\",\"image_intro_caption\":\"\",\"image_fulltext\":\"\",\"float_fulltext\":\"\",\"image_fulltext_alt\":\"\",\"image_fulltext_caption\":\"\"}' WHERE `jos_content`.`id` = 30; 

UPDATE `jos_content` SET `publish_down`=null, `checked_out_time`=null, `checked_out`=null;

UPDATE jos_content c JOIN jos_assets a ON a.title = c.title SET c.asset_id = a.id WHERE a.name LIKE '%com_content%' 

INSERT INTO #__workflow_associations (item_id, stage_id, extension) 
SELECT c.id as item_id, '1', 'com_content.article' FROM #__content AS c 
WHERE NOT EXISTS (SELECT wa.item_id FROM #__workflow_associations AS wa WHERE wa.item_id = c.id);

Remember to change prefix from jos_ to prefix that corresponds Your database prefix.
Now the articles should appeear but without galleries and without cover image 
------------------------------------------------------------------------------------------------------------------------------------------------
... that is done by seconf script - article_cover_mod.php
Use Your credentials as before
$servername = "Your db server name:port";
$username = "Username with edit privilegues";
$password = "Password";
$dbname = "Database name";

// Tworzenie połączenia (making connection)
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);

// Sprawdzanie połączenia (checking connection)
if ($pdo->errorCode()) {
    die("Połączenie nieudane: " . $pdo->errorInfo()[2]);
}

------------------------------------------------------------------------------------------------------------------------------------------------
First loop modify it with correct values of articles that You phisycally have:
// Pętla od 1 do 2372 (loop from 1 to 2372 ... probably the 2372 value is different in Your case)
for ($k2item = 1; $k2item <= 2372; $k2item++) {
    // Generowanie nazwy pliku z użyciem md5 bez prefiksu katalogu
    $filename = md5("Image" . $k2item) . "_XL.jpg";
    $filepath = "media/k2/items/cache/" . $filename; 
    
    // Sprawdzenie, czy plik istnieje (checking if file exist)
    if (file_exists($filepath)) {
        // Pobieranie tytułu z tabeli jos_k2_items (checking title from jos_k2_items table)
        $stmt = $pdo->prepare('SELECT title FROM jos_k2_items WHERE id = ?');
        $stmt->execute([$k2item]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $current_title = $result['title'];

            // Sprawdzanie, czy istnieje wpis w tabeli jos_content z tym samym tytułem (comparing title from jos_content table)
            $stmt = $pdo->prepare('SELECT id FROM jos_content WHERE title = ?');
            $stmt->execute([$current_title]);
            $content_result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($content_result) {
                $content_id = $content_result['id'];

                // Tworzenie nowej wartości dla kolumny images (creating new value for column images)
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

                // Aktualizowanie kolumny images w tabeli jos_content (updating column jos_content to choose right image as a cover)
                $stmt = $pdo->prepare('UPDATE jos_content SET images = ? WHERE id = ?');
                $stmt->execute([$image_data, $content_id]);
                echo 'UPDATE jos_content SET images = ' . $image_data . ' WHERE id = ' . $content_id . '<br>';
            }
        }

        // Wyświetlenie ścieżki do pliku (for debbuging only view the result listing)
        echo $k2item.' - '.$filename.'<br>';
    }
}


