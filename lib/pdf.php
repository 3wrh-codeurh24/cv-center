<?php
include 'vendor/autoload.php';

use \Smalot\PdfParser\Parser;
use \Wrseward\PdfParser\Pdf\PdfToTextParser;

function PDF_getText($filename) {
    $parser = new PdfToTextParser('/usr/bin/pdftotext');
    $parser->parse(PATH_CV.$filename);
    $text = $parser->text();
    // $parser = new \Smalot\PdfParser\Parser();
    // $pdf    = $parser->parseFile(PATH_CV.$source);
     
    // $text = $pdf->getText();
    return $text;
}

function PDF_getDetails($source) {
    if(mime_content_type(PATH_CV.$source) != 'application/pdf') {
        return null;
    }

    $parser = new \Smalot\PdfParser\Parser();
    $pdf    = $parser->parseFile(PATH_CV.$source);
    
    $details  = $pdf->getDetails();
    
    $data = '';
    foreach ($details as $property => $value) {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        $data .= $property . ' => ' . $value . "\n";
    }
    return $data;
}