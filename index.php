<?php session_start();
header('Content-Type: text/html; charset=utf-8');
//sudo apt install antiword

/* CONVERTIR une image representant le cv en texte au format pdf
// lowriter -convert-to pdf:writer_pdf_Export 1616_CV-Ingenieur_systeme_Electronique.doc
// convert -density 600 1616_CV-Ingenieur_systeme_Electronique.pdf out.png
// sudo apt install tesseract-ocr
// tesseract out.png out.txt
// tesseract out.png out -l fra pdf
*/

// https://packagist.org/packages/wrseward/pdf-parser
require_once 'vendor/autoload.php';
require_once 'config/config.main.php';

require_once 'lib/helper.php';
require_once 'lib/cv.php';

require_once 'lib/docx.php';
require_once 'lib/odt.php';
require_once 'lib/ocr.php';
require_once 'lib/pdf.php';
require_once 'lib/doc.php';

use \Smalot\PdfParser\Parser;
use \Wrseward\PdfParser\Pdf\PdfToTextParser;
use Symfony\Component\Process\Process;


// ./cv/5de4fa2a0fd02894990424.pdf

$DefaultSource = "5de4fa2a0fd02894990424.pdf";


(isset($_GET['read-file'])) ? $filename = $_GET['read-file'] : $filename = $DefaultSource ;


// $fileList = fileList();
// foreach($fileList as $filename) {
//     try {
//         if (!registerFile($filename)) {
//             throw(new Exception('Echec: Enregistrement en base de donnée du CV a échoué'));
//             exit;
//         }

//         if (!rename(PATH_CV.$filename, './cvsql/'.$filename)) {
//             throw(new Exception('Echec: Déplacement du CV dans le dossier des CV enregistré a échoué'));
//             exit;
//         }

//     } catch (Exception $e) {
//         echo "Exception : {$e->getMessage()}\n";
//     }
// }
// exit;

//5de4fb523101d326844577.docx good
// 5de4fb4130777542159483.docx bad
// 2073_CV_CNM.doc


//$_SESSION["newsession"]=$value;
if (isset($_GET['list-file-format'])){
    if ($_GET['list-file-format'] == 'pdf') $_SESSION["list-file-format"]='pdf';
    if ($_GET['list-file-format'] == 'doc') $_SESSION["list-file-format"]='doc';
    if ($_GET['list-file-format'] == 'docx') $_SESSION["list-file-format"]='docx';
    if ($_GET['list-file-format'] == 'odt') $_SESSION["list-file-format"]='odt';
    if ($_GET['list-file-format'] == 'all') $_SESSION["list-file-format"]='*';

    header('Location: /');
    exit;
}



$fileList = fileList();
$text = CV_getText($filename);



// if ( isset($text) && strlen($text) < 300) {
//     $text = OCR_docToPdf($filename);
//     echo 'Le texte contient '.strlen($text).' caracteres';
// }else{
//     $text = !isset($text) ? 'texte vide' : 'texte trop petit';
// }
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>3WRH - Data Center - search CV</title>
        <link rel="stylesheet" href="static/css/main.css">
        <meta charset="UTF-8">
    </head>
    <body>
        <nav>
            <ul>
                <li><a href="?list-file-format=all">All</a></li>
                <li><a href="?list-file-format=pdf">PDF</a></li>
                <li><a href="?list-file-format=doc">DOC</a></li>
                <li><a href="?list-file-format=docx">DOCX</a></li>
                <li><a href="?list-file-format=odt">ODT</a></li>
            </ul>
        </nav>
        <div class="side-nav">
            <?php
            foreach($fileList as $file){
                ?>
                <div>
                    <a href="?read-file=<?= $file ?>"><?= $file ?></a>
                </div>
                <?php
            }
            ?>
        </div>
        <main >
            <!-- <div class="details"><?= PDF_getDetails($filename) ?></div> -->
            <div class="details"><?=  null //implode('; ',) ?></div>
            <div class="content">
                <pre>
                    <?= $text ?>
                </pre>

            </div>
        </main>
    </body>
    </html>
<?php


function registerFile($filename) {
    $dsn = 'mysql:dbname='.DB_NAME.';host='.DB_HOST;
    $dbh = new PDO($dsn, DB_USER, DB_PWD);

    $parser = new PdfToTextParser('/usr/bin/pdftotext');
    $parser->parse(PATH_CV.$filename);


    $sth = $dbh->prepare("INSERT INTO `cv` 
    (`id`, `md5`, `content`, `size`, `producer`, `keywords`, `date_last_access`)
    VALUES
    (NULL, ?,       ?,        ?,      ?,          ?,           ?)
    ");

    return $sth->execute([
        md5_file(PATH_CV.$filename), 
        $parser->text(), 
        filesize(PATH_CV.$filename), 
        'PC DE FLO', 
        '', 
        ((new DateTime())->format('Y-m-d H:i:s'))
    ]);

}



