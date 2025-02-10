<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head><meta content="text/html; charset=ISO-8859-1" http-equiv="content-type"><title>fuckit</title></head><body>
<h2>K2 editor is the plugin for Joomla but it was supported till Joomla version 3</h2>
<h2>As You know now we have newer version of Joomla so ... Need to disable the plugin to succesfully upgrade Your Joomla CMS.</h2>
<h2>Unfortunately disabling K2 plugin is equally to drop all the articles which we made before.</h2>
<h2>So ... i created some scripts which are importing articles from K2 plugin straight to the Joomla articles manager.</h2>
<h2>In first file export.php You need to edit few things:</h2>
------------------------------------------------------------------------------------------------------------------------------------------------<br>
<span style="font-weight: bold;">$servername = "addres and port of Your mariadb server";</span><br style="font-weight: bold;">
<span style="font-weight: bold;">$username = "username with privilegues to modify database";</span><br style="font-weight: bold;">
<span style="font-weight: bold;">$password = "password to the database";</span><br style="font-weight: bold;">
<span style="font-weight: bold;">$dbname = "name of Your database";</span><br>
------------------------------------------------------------------------------------------------------------------------------------------------<br>
<h2>Than you should set category mapping if the ID`s are different with newly created categories:</h2>
<h2><br>
// Mapowanie kategorii (category mappings)</h2>
<span style="font-weight: bold;">$category_map = [</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 1 =&gt; 8,</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 2 =&gt; 9,</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 3 =&gt; 2,</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 4 =&gt; 8,</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 5 =&gt; 9,</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 6 =&gt; 10,</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 7 =&gt; 11,</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 8 =&gt; 12,</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 9 =&gt; 8,</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 10 =&gt; 14,</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 11 =&gt; 15,</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 12 =&gt; 21,</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 13 =&gt; 22,</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 14 =&gt; 23,</span><br style="font-weight: bold;">
<span style="font-weight: bold;">&nbsp;&nbsp;&nbsp; 15 =&gt; 24</span><br style="font-weight: bold;">
<span style="font-weight: bold;">];</span><br>
<br>
// Pobieranie danych z tabeli jos_k2_items (retrieving data from jos_k2_items)<br>
$sql = "SELECT * FROM jos_k2_items";<br>
$result = $conn-&gt;query($sql);<br>
<br>
// Otwarcie pliku logu (creating log file to catch errors)<br>
$log_file = fopen("error_log.txt", "a");<br>
<br>
if ($result-&gt;num_rows &gt; 0) {<br>
&nbsp;&nbsp;&nbsp; // Przechodzenie przez ka&#380;dy wiersz (loop with row after row parsing)<br>
&nbsp;&nbsp;&nbsp; while ($row = $result-&gt;fetch_assoc()) {<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $catid =
isset($category_map[$row['catid']]) ? $category_map[$row['catid']] :
$row['catid'];<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $title = $conn-&gt;real_escape_string($row['title']);<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $alias = $conn-&gt;real_escape_string($row['alias']);<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $introtext = $conn-&gt;real_escape_string($row['introtext']);<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $fulltext = $conn-&gt;real_escape_string($row['fulltext']);<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $created_by_alias = $conn-&gt;real_escape_string($row['created_by_alias']);<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $metakey = $conn-&gt;real_escape_string($row['metakey']);<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $metadesc = $conn-&gt;real_escape_string($row['metadesc']);<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $metadata = $conn-&gt;real_escape_string($row['metadata']);<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $language = $conn-&gt;real_escape_string($row['language']);<br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $insert_sql = "INSERT INTO
`jos_content` (`id`, `asset_id`, `title`, `alias`, `introtext`,
`fulltext`, `state`, `catid`, `created`, `created_by`,
`created_by_alias`, `modified`, `modified_by`, `checked_out`,
`checked_out_time`, `publish_up`, `publish_down`, `images`, `urls`,
`attribs`, `version`, `ordering`, `metakey`, `metadesc`, `access`,
`hits`, `metadata`, `featured`, `language`, `note`) VALUES (null, '0',
'{$title}', '{$alias}', '{$introtext}', '{$fulltext}',
'{$row['published']}', '{$catid}', '{$row['created']}',
'{$row['created_by']}', '{$created_by_alias}', '{$row['modified']}',
'{$row['modified_by']}', '{$row['checked_out']}',
'{$row['checked_out_time']}', '{$row['publish_up']}',
'{$row['publish_down']}', '', '', '', '1', '0', '{$metakey}',
'{$metadesc}', '{$row['access']}', '{$row['hits']}', '{$metadata}',
'{$row['featured']}', '{$language}', '')";<br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; // Zapisanie pe&#322;nego
zapytania SQL do pliku logu (writing a loop queries to an log file)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; fwrite($log_file, $insert_sql . "\n");<br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; if ($conn-&gt;query($insert_sql) !== TRUE) {<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; //
Logowanie bardziej szczegó&#322;owych komunikatów o b&#322;&#281;dach (more error
details for logs)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; echo
"B&#322;&#261;d: " . $insert_sql . "&lt;br&gt;" . $conn-&gt;error . "&lt;br&gt;";<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; }<br>
&nbsp;&nbsp;&nbsp; }<br>
&nbsp;&nbsp;&nbsp; echo "Dane zosta&#322;y przeniesione pomy&#347;lnie - succesfull migration !"; #succesfull migration<br>
} else {<br>
&nbsp;&nbsp;&nbsp; echo "Brak danych do przeniesienia - no data found";<br>
}<br>
<br>
// Zamkni&#281;cie pliku logu (close a log file and write)<br>
fclose($log_file);<br>
<br>
$conn-&gt;close();<br>
<br>
------------------------------------------------------------------------------------------------------------------------------------------------<br>
<h2>... but after that nothing still works so ...</h2>
<h2>in your hosting phpmyadmin page You should run those queries:</h2>
UPDATE `jos_menu` SET link = REPLACE(link, 'com_k2', 'com_content') WHERE link LIKE '%com_k2%';<br>
<br>
UPDATE `jos_content` SET `metadata` = '{\"robots\":\"\",\"author\":\"\",\"rights\":\"\",\"xreference\":\"\"}'; <br>
<br>
UPDATE `jos_content` SET `attribs` =
'{\"article_layout\":\"\",\"show_title\":\"\",\"link_titles\":\"\",\"show_tags\":\"\",\"show_intro\":\"\",\"info_block_position\":\"\",\"info_block_show_title\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_associations\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_vote\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"alternative_readmore\":\"\",\"article_page_title\":\"\",\"show_publishing_options\":\"\",\"show_article_options\":\"\",\"show_urls_images_backend\":\"\",\"show_urls_images_frontend\":\"\"}';
<br>
<br>
UPDATE `jos_content` SET `urls` =
'{\"urla\":false,\"urlatext\":\"\",\"targeta\":\"\",\"urlb\":false,\"urlbtext\":\"\",\"targetb\":\"\",\"urlc\":false,\"urlctext\":\"\",\"targetc\":\"\"}';
<br>
<br>
UPDATE `jos_content` SET `images` =
'{\"image_intro\":\"\",\"float_intro\":\"\",\"image_intro_alt\":\"\",\"image_intro_caption\":\"\",\"image_fulltext\":\"\",\"float_fulltext\":\"\",\"image_fulltext_alt\":\"\",\"image_fulltext_caption\":\"\"}'
WHERE `jos_content`.`id` = 30; <br>
<br>
UPDATE `jos_content` SET `publish_down`=null, `checked_out_time`=null, `checked_out`=null;<br>
<br>
UPDATE jos_content c JOIN jos_assets a ON a.title = c.title SET c.asset_id = a.id WHERE a.name LIKE '%com_content%' <br>
<br>
INSERT INTO #__workflow_associations (item_id, stage_id, extension) <br>
SELECT c.id as item_id, '1', 'com_content.article' FROM #__content AS c <br>
WHERE NOT EXISTS (SELECT wa.item_id FROM #__workflow_associations AS wa WHERE wa.item_id = c.id);<br>
<br>
Remember to change prefix from jos_ to prefix that corresponds Your database prefix.<br>
Now the articles should appeear but without galleries and without cover image <br>
------------------------------------------------------------------------------------------------------------------------------------------------<br>
<h2>... that is done by seconf script - article_cover_mod.php</h2>
<h2>Use Your credentials as before</h2>
$servername = "Your db server name:port";<br>
$username = "Username with edit privilegues";<br>
$password = "Password";<br>
$dbname = "Database name";<br>
<br>
// Tworzenie po&#322;&#261;czenia (making connection)<br>
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);<br>
<br>
// Sprawdzanie po&#322;&#261;czenia (checking connection)<br>
if ($pdo-&gt;errorCode()) {<br>
&nbsp;&nbsp;&nbsp; die("Po&#322;&#261;czenie nieudane: " . $pdo-&gt;errorInfo()[2]);<br>
}<br>
<br>
------------------------------------------------------------------------------------------------------------------------------------------------<br>
First loop modify it with correct values of articles that You phisycally have:<br>
// P&#281;tla od 1 do 2372 (loop from 1 to 2372 ... probably the 2372 value is different in Your case)<br>
for ($k2item = 1; $k2item &lt;= 2372; $k2item++) {<br>
&nbsp;&nbsp;&nbsp; // Generowanie nazwy pliku z u&#380;yciem md5 bez prefiksu katalogu<br>
&nbsp;&nbsp;&nbsp; $filename = md5("Image" . $k2item) . "_XL.jpg";<br>
&nbsp;&nbsp;&nbsp; $filepath = "media/k2/items/cache/" . $filename; <br>
&nbsp;&nbsp;&nbsp; <br>
&nbsp;&nbsp;&nbsp; // Sprawdzenie, czy plik istnieje (checking if file exist)<br>
&nbsp;&nbsp;&nbsp; if (file_exists($filepath)) {<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; // Pobieranie tytu&#322;u z tabeli jos_k2_items (checking title from jos_k2_items table)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $stmt = $pdo-&gt;prepare('SELECT title FROM jos_k2_items WHERE id = ?');<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $stmt-&gt;execute([$k2item]);<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $result = $stmt-&gt;fetch(PDO::FETCH_ASSOC);<br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; if ($result) {<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $current_title = $result['title'];<br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; //
Sprawdzanie, czy istnieje wpis w tabeli jos_content z tym samym tytu&#322;em
(comparing title from jos_content table)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
$stmt = $pdo-&gt;prepare('SELECT id FROM jos_content WHERE title = ?');<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $stmt-&gt;execute([$current_title]);<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $content_result = $stmt-&gt;fetch(PDO::FETCH_ASSOC);<br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; if ($content_result) {<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $content_id = $content_result['id'];<br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
// Tworzenie nowej warto&#347;ci dla kolumny images (creating new value for
column images)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $image_data = json_encode([<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
"image_intro" =&gt;
"media/k2/items/cache/{$filename}#joomlaImage://media/k2/items/cache/{$filename}?width=auto&amp;height=auto",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
"image_intro_alt" =&gt; "",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
"float_intro" =&gt; "",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
"image_intro_caption" =&gt; "",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
"image_fulltext" =&gt; "",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
"image_fulltext_alt" =&gt; "",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
"float_fulltext" =&gt; "",<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
"image_fulltext_caption" =&gt; ""<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ]);<br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
// Aktualizowanie kolumny images w tabeli jos_content (updating column
jos_content to choose right image as a cover)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
$stmt = $pdo-&gt;prepare('UPDATE jos_content SET images = ? WHERE id =
?');<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
$stmt-&gt;execute([$image_data, $content_id]);<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
echo 'UPDATE jos_content SET images = ' . $image_data . ' WHERE id = '
. $content_id . '&lt;br&gt;';<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; }<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; }<br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; // Wy&#347;wietlenie &#347;cie&#380;ki do pliku (for debbuging only view the result listing)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; echo $k2item.' - '.$filename.'&lt;br&gt;';<br>
&nbsp;&nbsp;&nbsp; }<br>
}<br>

</body></html>
