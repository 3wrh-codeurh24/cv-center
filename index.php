<?php session_start();
header('Content-Type: text/html; charset=utf-8');
set_time_limit(3600);

// astuce pour supprimer tout les fichier non caché: sudo find ./cv/ -type f -delete


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



// Supprime tous les fichiers contenant zéro octets
deleteFilesVoids(); // 7405 - 7366

// Supprime les fichiers ayant un contenu identique.
// La suppression privilégie les noms de fichier humainement lisible.
removeDuplicateFiles(); // 7366 - 4089

// Renomme les fichiers sous forme de slug à cause des caractères difficilement lisibles.
checkValidFilename();

$fileList = fileList();
foreach($fileList as $filename) {
    try {
        if (!registerFile($filename)) {
            if (!rename(PATH_CV.$filename, './cverror/'.$filename)){
                echo "(try 0) Echec: Déplacement du CV dans le dossier des CVerror a échoué<br />\n";
            }
            echo "(try 1) Echec: Enregistrement en base de donnée du CV a échoué $filename<br />\n";
            continue;
        }
        
        if (!rename(PATH_CV.$filename, './cvsql/'.$filename)) {
            echo "(try 2) Echec: Déplacement du CV dans le dossier des CVsql a échoué<br />\n";
        }

    } catch (Exception $e) {
        echo "Exception : {$e->getMessage()}\n";
        if (!rename(PATH_CV.$filename, './cverror/'.$filename)){
            echo "(catch) Echec: Déplacement du CV dans le dossier des CVerror a échoué<br />\n";
        }
    }
}

exit('Programme fini');



function registerFile($filename) {
    $dsn = 'mysql:dbname='.DB_NAME.';host='.DB_HOST.';charset=UTF8';
    $dbh = new PDO($dsn, DB_USER, DB_PWD);

    // $parser = new PdfToTextParser('/usr/bin/pdftotext');
    // $parser->parse(PATH_CV.$filename);
    // $parser->text(),

    $text = CV_getText($filename);


    $sth = $dbh->prepare("INSERT INTO `cv` 
    (`id`, `original_filename`, `md5_file`, `content`, `size`, `file_type`, `date_last_access`)
    VALUES
    (NULL,  ?,                   ?,          ?,         ?,      ?,            ?)
    ");

    return $sth->execute([
        $filename,
        md5_file(PATH_CV.$filename),
        $text, 
        filesize(PATH_CV.$filename), 
        CV_getFileType($filename), 
        ((new DateTime())->format('Y-m-d H:i:s'))
    ]);

}



