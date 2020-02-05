<?php

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