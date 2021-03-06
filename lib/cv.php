<?php
include 'vendor/autoload.php';

use Symfony\Component\Process\Process;



function CV_docToPdf($filename){
    // lowriter -convert-to pdf:writer_pdf_Export 1607_23_CV_Fabrizio_Durand.doc
    // shell_exec('antiword '.PATH_CV.$filename);
    if (!file_exists(PATH_CV.$filename)) return false;
    if (mime_content_type(PATH_CV.$filename) != 'application/msword') return false;

    // ce str_replace est simplifié, il faudrait vérifier que ce sont bien les 3 dernieres lettres
    $newFilePdf = str_replace('.doc', '.pdf', $filename);


    if (file_exists(PATH_CV.$newFilePdf)) return $newFilePdf;
    
    $cmd ='lowriter -convert-to pdf:writer_pdf_Export 1607_23_CV_Fabrizio_Durand.doc';
    echo "Command: ".$cmd." <br />";
    // shell_exec($cmd);
    $process = new Process($cmd);
    echo 'chemin '.getcwd() . "/./cv <br />";
    $process->setWorkingDirectory(getcwd() . "/./cv");
    $process->start();
    $process->wait();

    // patiente pendant 0.5 secondes le temps de creer le fichier pdf
    usleep(500000);

    echo "Convertion PDF de $filename en $newFilePdf <br />";
    // echo file_exists(PATH_CV.$newFilePdf) ? 'Conversion ok' : 'conversion echec' ;
    echo '<br />';

    return file_exists(PATH_CV.$newFilePdf) ? $newFilePdf : false;
}

function CV_getFileType($filename) {

    $type = 'none';

    if (file_exists(PATH_CV.$filename)) {
        // echo 'mime_content_type: '.mime_content_type(PATH_CV.$filename).'<br />';
        switch (mime_content_type(PATH_CV.$filename)) {
            case 'application/pdf':
                $type = 'pdf';
                break;

            case 'application/msword':
                $type = 'doc';
                break;
            
            case 'application/vnd.oasis.opendocument.text':
                $type = 'odt';
                break;
            
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $type = 'docx';
                break;
            
            default:
                $type = 'none';
                break;
        }
    } else {
        $type = 'Le fichier '.PATH_CV.$filename.' n\'existe pas';
    }

    return $type;
}

function CV_getText($filename) {
    if (file_exists(PATH_CV.$filename)) {
        // echo 'mime_content_type: '.mime_content_type(PATH_CV.$filename).'<br />';
        switch (mime_content_type(PATH_CV.$filename)) {
            case 'application/pdf':
                $text = PDF_getText($filename);
                break;

            case 'application/msword':
                $text = DOC_getText($filename);
                break;
            
            case 'application/vnd.oasis.opendocument.text':
                $text = ODT_getText($filename);
                break;
            
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $text = DOCX_getText($filename);
                break;
            
            default:
                $text = 'Aucun format reconnu';
                break;
        }
    } else {
        $text = 'Le fichier '.PATH_CV.$filename.' n\'existe pas';
    }

    return $text;
}