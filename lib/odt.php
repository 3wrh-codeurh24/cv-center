<?php


function ODT_getText($filename) {

    $xml_filename = "content.xml"; //content file name
	$zip_handle = new ZipArchive;
	$output_text = "";
	if(true === $zip_handle->open(PATH_CV.$filename)){
		if(($xml_index = $zip_handle->locateName($xml_filename)) !== false){
			$xml_datas = $zip_handle->getFromIndex($xml_index);
			$xml_handle = DOMDocument::loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
			// $output_text = strip_tags($xml_handle->saveXML());
            // $output_text = str_replace('</text:span>', "</text:span>\n", $xml_handle->saveXML());
            
            
            $pattern = '/(<text:p text:style-name="P[0-9]+">)/i';
            $replacement = '${1}'."\n";
            $output_text = preg_replace($pattern, $replacement, $xml_handle->saveXML());

            $pattern = '/(<text:h text:style-name=".+" text:outline-level="2">)/i';
            $replacement = "\n".'${1}'."\n\n";
            $output_text = preg_replace($pattern, $replacement, $output_text);

            $pattern = '/(<text:p text:style-name="Standard">)/i';
            $replacement = '${1} ';
            $output_text = preg_replace($pattern, $replacement, $output_text);

            $pattern = '/(<text:tab\/>)/i';
            $replacement = '${1} ';
            $output_text = preg_replace($pattern, $replacement, $output_text);

            

            $output_text = strip_tags($output_text);
            // $output_text = htmlspecialchars($output_text);
			// $output_text = $xml_handle->saveXML();
		}else{
			$output_text .="";
		}
		$zip_handle->close();
	}else{
	$output_text .="";
	}
	return htmlspecialchars($output_text) ;
}