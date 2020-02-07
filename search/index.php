<?php session_start();
header('Content-Type: text/html; charset=utf-8');

require_once '../vendor/autoload.php';
require_once '../config/mysql.php';

$dsn = 'mysql:dbname='.DB_NAME.';host='.DB_HOST.';charset=UTF8';
$dbh = new PDO($dsn, DB_USER, DB_PWD);

// se if gere la partie recherche a partir de l'url
if (isset($_GET['search'])) {
    // génération d'une chaine sql precisant les 'where' dans la requete sql 
    // qui execute une recherche par la colonne content
    $sqlWhere = '';
    // Toutes le valeurs utiles pour PDO execute
    $words = [];

    // copie chaque mot entrée de chaque input vers le tableau words
    foreach ($_GET['search'] as $key => $search){
        foreach (explode(" ", $search) as $key => $value) $words[] = "%$value%";
    }

    // génération de la chaine where sql.
    // premiere boucle pour tous les input representant un or
    foreach ($_GET['search'] as $key => $search){           
        // deuxieme boucle, dans un input les mots séparer par des espaces sont des and
        foreach (explode(" ", $search) as $key2 => $value) {
            if(isset(explode(" ", $search)[$key2+1])) {
                $sqlWhere .= "`content` LIKE ? and ";
            }else{
                $sqlWhere .= "`content` LIKE ? ";
            }
        }

        if(isset($_GET['search'][$key+1])) $sqlWhere .= " or ";
    }




    $sql = "SELECT id, original_filename, date_last_access FROM `cv` WHERE $sqlWhere ORDER BY `id` DESC";
    $sth = $dbh->prepare($sql);
    $sth->execute($words);

}else{
    $sth = $dbh->prepare('SELECT id, original_filename, date_last_access FROM `cv`');
    $sth->execute();
}
$list = $sth->fetchAll(PDO::FETCH_ASSOC);






// ce if permet d'afficher le contenu d'un cv en texte brut
if (isset($_GET['filename'])) {

    $sth = $dbh->prepare('SELECT content FROM `cv` WHERE `original_filename` LIKE ? ');
    $sth->execute(["%$_GET[filename]%"]);

    $row = $sth->fetch(PDO::FETCH_ASSOC);

    $content = $row['content'];
    
    if (isset($_GET['search'])) {
        foreach ($_GET['search'] as $key => $search) {
            $words = explode(" ", $search);
            foreach ($words as $key => $word) {
                $content = str_ireplace($word, "<span class=\"light\">$word</span>", $content);
            }
        }
    }
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Search CV - CV CENTER</title>
    <!-- Certains fichiers css sont ajoutés grâce aux fichiers event -->
    <link rel="stylesheet" href="/static/css/page/search/infos-start.css" />
    <link rel="stylesheet" href="/static/css/page/search/search-bar/search-bar.css" />
    <link rel="stylesheet" href="/static/css/page/search/search-gui.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-icons/3.0.1/iconfont/material-icons.min.css" />
</head>
<body>
    <div id="cv-list">
        <div id="search-bar">
            <form>
                <div class="tools-search-bar">
                    <a href="#" class="nav-link-favories-cv">
                        <i class="material-icons">star</i>
                        <ul><ol class="title">Mes Favoris</ol></ul>
                    </a>
                    <a href="#" class="nav-link-history-cv">
                        <i class="material-icons">list</i>
                        <ul><ol class="title">Mes derniers accès</ol></ul>
                    </a>
                    <a href="#" class="nav-link-history-search">
                        <i class="material-icons">list</i>
                        <ul><ol class="title">Mes dernieres recherches</ol></ul>
                    </a>
                </div>                
                <div style="text-align:right;"><?= count($list) ?> Résultats</div>
                <div class="inputs-form" style="margin:auto;width:234px;padding: 10px 0">
                    <?php
                    if (isset($_GET['search'])) {
                        foreach ($_GET['search'] as $key => $value) {
                            if($key == 0) {
                                ?>
                                    <input type="text" name="search[]" value="<?= $value ?? '' ?>" />
                                    <input type="button" value="+" class="btn-form-add-value">
                                <?php
                            }else{
                                ?>
                                    <input type="text" name="search[]" value="<?= $value ?? '' ?>" class="row-form-<?= $key ?>" />
                                    <input type="button" value="+" class="btn-form-add-value row-form-<?= $key ?>">
                                    <input type="button" value="-" class="btn-form-del-value" data-index="<?= $key ?>">
                                <?php
                            }
                        }
                    } else {
                    ?>
                    <input type="text" name="search[]" value="<?= $_GET['search'] ?? '' ?>" /><input type="button" value="+" class="btn-form-add-value">
                    <?php
                    }
                    ?>
                </div>
                <div style="text-align:center;">
                    <input type="submit" value="Rechercher" />
                </div>
            </form>
        </div>
        <div id="cv-list-content">
            <?php 
            
            foreach ($list as $row) {

                if (isset($_GET['search'])) {
                    $qSearch = '';

                    foreach ($_GET['search'] as $key => $value) {
                        if(isset($_GET['search'][$key+1])){
                            $qSearch .= "search%5B%5D=$value&";
                        }else{
                            $qSearch .= "search%5B%5D=$value";
                        }
                        
                    }
                    
                
                    $url = "http://$_SERVER[HTTP_HOST]/search?$qSearch&filename=$row[original_filename]";
                } else {
                    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    $urlQuery = parse_url($actual_link, PHP_URL_QUERY);
                    $url = "http://$_SERVER[HTTP_HOST]/search?filename=$row[original_filename]";
                }



                ?>
                <div>
                    <?php if (isset($_GET['filename']) && $row['original_filename'] == $_GET['filename']) { ?>
                        <a href="<?= $url ?>" class="light"><?= $row['original_filename'] ?></a>
                    <?php } else { ?>
                        <a href="<?= $url ?>"><?= $row['original_filename'] ?></a>
                    <?php } ?>
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
                <a href="<?= "http://$_SERVER[HTTP_HOST]/search/download.php?filename=$_GET[filename]" ?>"><i class="material-icons">cloud_download</i></a>
                <a href="#" class="btn-favories-add-cv" data-filename="<?= "$_GET[filename]" ?>">
                    <i class="material-icons">star</i>
                </a>
            </div>
            <pre><?= $content ?></pre>
        <?php
        } else {
            include './info-start/infos-start.php';
        }
        ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="/static/js/search-bar/lib/global.js" type="module"></script>
    <script src="/static/js/page/search/app.js" type="module"></script> 
</body>
</html>
