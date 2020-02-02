<?php session_start();
header('Content-Type: text/html; charset=utf-8');

require_once '../vendor/autoload.php';
require_once '../config/mysql.php';

$dsn = 'mysql:dbname='.DB_NAME.';host='.DB_HOST.';charset=UTF8';
$dbh = new PDO($dsn, DB_USER, DB_PWD);

if (isset($_GET['advanced_search'])) {

    $qValues = $_GET['advanced_search']['text'];
    $qOperator = $_GET['advanced_search']['select'];

    $sqlWhere = '';
    $sqlDebug = '';
    foreach ($qValues as $key => $value) {
        $qValues[$key] = '%'.$qValues[$key].'%';
        $_value = $qValues[$key];
        $_operator = $qOperator[$key] ?? '';
        $sqlWhere .= "`content` LIKE ? $_operator ";
        $sqlDebug .= "`content` LIKE '$_value' $_operator ";
    }
    echo "$sqlDebug<br />";
    $sth = $dbh->prepare("SELECT id, original_filename, date_last_access FROM `cv` WHERE $sqlWhere ORDER BY `id` DESC");
    $sth->execute($qValues);
    

} else {
    $word = "developpeur";

    if (isset($_GET['search'])) {
        $word = $_GET['search'];
    }
    
    $sth = $dbh->prepare('SELECT id, original_filename, date_last_access FROM `cv` WHERE `content` LIKE ? ORDER BY `id` DESC');
    $sth->execute(["%$word%"]);
}
// exit;





$list = $sth->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['filename'])) {

    $sth = $dbh->prepare('SELECT content FROM `cv` WHERE `original_filename` LIKE ? ');
    $sth->execute(["%$_GET[filename]%"]);

    $row = $sth->fetch(PDO::FETCH_ASSOC);

    $content = $row['content'];
    // $content = mb_convert_encoding($row['content'], "UTF-8", mb_detect_encoding($row['content'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    // $content = mb_convert_encoding($row['content'], "ISO-8859-1", mb_detect_encoding($row['content'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    
    if (isset($_GET['search'])) {
        $content = str_ireplace($word, "<span class=\"light\">$word</span>", $content);
    }
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Search CV - CV CENTER</title>
    <link rel="stylesheet" href="/static/css/search-bar.css" />
    <link rel="stylesheet" href="/static/css/search-gui.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-icons/3.0.1/iconfont/material-icons.min.css" />
</head>
<body>
    <div id="cv-list">
        <div id="search-bar">
            <form>
                <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>">
                <input type="submit" value="Rechercher">
            </form>
            <div class="info-search-result">
                <?= count($list) ?> Résultats - <span><a href="#" class="btn-advanced-search">Recherche avancé</a></span>
            </div>
            <div id="search-bar-advanced">
                <form>
                    <div class="inputs-form">
                        <div>
                            <input type="text" name="advanced_search[text][]">
                        </div>
                        <div>
                            <select name="advanced_search[select][]">
                                <option value="and">Et</option>
                                <option value="or">Ou</option>
                            </select>
                            <input type="text" name="advanced_search[text][]">
                            <input type="button" value="+" class="btn-form-add-value">
                        </div>
                    </div>
                    <div>
                        <input type="submit" value="Rechercher">
                    </div>
                </form>
            </div>
        </div>
        <div id="cv-list-content">
            <?php 
            
            foreach ($list as $row) {

                if (isset($_GET['search'])) {
                
                    $url = "http://$_SERVER[HTTP_HOST]/search?search=$_GET[search]&filename=$row[original_filename]";
                } else {
                    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    $urlQuery = parse_url($actual_link, PHP_URL_QUERY);
                    $url = "http://$_SERVER[HTTP_HOST]/search?$urlQuery&filename=$row[original_filename]";
                }



                ?>
                <div>
                    <a href="<?= $url ?>"><?= $row['original_filename'] ?></a>
                </div>
                <?php
            }
            ?>
        </div>
 
    </div>
    <div id="cv-content">
        <?php 
        if (isset($content)) {
        ?> 
            <div id="cv-content-tool-bar">
                <a href="<?= "http://$_SERVER[HTTP_HOST]/search/download.php?filename=$_GET[filename]" ?>">
                    <i class="material-icons">cloud_download</i>
                </a>
            </div>
            <pre><?= $content ?></pre>
        <?php
        } 
        ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="/static/js/advanced-search.js"></script>
</body>
</html>
