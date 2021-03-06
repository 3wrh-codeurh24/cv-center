<?php
include 'vendor/autoload.php';

use Symfony\Component\Process\Process;

function OCR_PdfToPng($filename){

    if(getExtensionFile($filename) == 'pdf' || getExtensionFile($filename) == 'png') {

        $name = getNameFile($filename);
    }
    

    if (file_exists(PATH_CV.$name.'.png')) unlink(PATH_CV.$name.'.png');

    $cmd = "convert -density 600 $filename $name.png";
    $process = new Process($cmd);
    echo "execution de OCR_PdfToPng, command: $cmd <br /> ";
    echo " chemin: ".getcwd() . "/cv"." <br />";
    $process->setWorkingDirectory(getcwd() . "/cv");
    $process->start();
    $process->wait();

    // patiente pendant 0.5 secondes le temps de creer le fichier png
    usleep(500000);

    return file_exists(PATH_CV.$name.'.png') ? $name.'.png' : false;
}

function OCR_CreatePDF($filename){
    if(getExtensionFile($filename) == 'pdf' || getExtensionFile($filename) == 'png') {

        $name = getNameFile($filename);
    }

    if (file_exists(PATH_CV.$filename)) rename(PATH_CV.$filename, './trash/'.$filename);
    
    $cmd = "tesseract $name.png $name -l fra pdf";
    $process = new Process($cmd);
    echo "execution de OCR_CreatePDF, command: $cmd <br /> ";
    $process->setWorkingDirectory(getcwd() . "/cv");
    $process->start();
    $process->wait();

    usleep(500000);

    return (file_exists(PATH_CV.$name.'.pdf') && file_exists(PATH_TRASH.$name.'.pdf')) ? $name.'.pdf' : false;
}

function OCR_docToPdf($filename){
    if( ($newFilePdf = CV_docToPdf($filename)) !== false) {
        echo 'Conversion docToPdf ok<br />';
        $text = CV_getText($newFilePdf);
        if ( strpos($text, 'Une image du CV') !== false ) {
            $text = 'Image detected in the CV';
            $resultPng = OCR_PdfToPng($newFilePdf);
            
            if ($resultPng !== false) {
                echo 'OCR png ok<br />';
                if (OCR_CreatePDF($newFilePdf)) {
                    echo 'Creation du nouveau PDF ok';
                    $text = CV_getText($newFilePdf);
                    return $text;
                }else{
                    echo 'Probleme sur la creation du nouveau PDF';
                }
            } else {
                echo 'OCR png echec<br />';
            }

        }else{
            return $text;
        }
    }else{
        return 'CV_docToPdf error';
    }
}