<?php

function DOCX_getText($filename){
    //Check for extension
    $ext = getExtensionFile(PATH_CV.$filename);

    //if its docx file
    if($ext == 'docx') {
        $dataFile = "word/document.xml";
    //else it must be odt file
    }else{
        $dataFile = "content.xml";     
    }
    //Create a new ZIP archive object
    $zip = new ZipArchive;

    // Open the archive file
    if (true === $zip->open(PATH_CV.$filename)) {
        // If successful, search for the data file in the archive
        if (($index = $zip->locateName($dataFile)) !== false) {
            // Index found! Now read it to a string
            $text = $zip->getFromIndex($index);
            // Load XML from a string
            // Ignore errors and warnings
            $xml = DOMDocument::loadXML($text, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            // Remove XML formatting tags and return the text
            // return strip_tags($xml->saveXML());
            $pattern = '/(<\/w:p>)/i';
            $replacement = '${1}'."\n";
            $output_text = preg_replace($pattern, $replacement, $xml->saveXML());

            $pattern = '/(<wp:posOffset>.+<\/wp:posOffset>)/i';
            $replacement = '${1}'."\n";
            $output_text = preg_replace($pattern, $replacement, $output_text);

            $patterns = [];
            $replacements = [];
            $patterns[0] = '/<wp:posOffset>.+<\/wp:posOffset>/';
            $replacements[0] = '';

            $patterns[1] = '/<wp14:pctWidth>.+<\/wp14:pctWidth>/';
            $replacements[1] = '';

            $patterns[2] = '/<wp14:pctHeight>.+<\/wp14:pctHeight>/';
            $replacements[2] = '';
            $output_text = preg_replace($patterns, $replacements, $output_text);

            return strip_tags($output_text);;
        }
        //Close the archive file
        $zip->close();
    }

    // In case of failure return a message
    return "File not found";
}