<?php

use Cocur\Slugify\Slugify;
/**
 * Retourne le nom du fichier sans l'extension
 * 
 * @param string $filename nom du fichier avec extension
 * 
 * @return string
 */
function getNameFile($filename) {
    $name = explode('.',$filename);
    $name = array_reverse($name);
    $name = array_splice($name, 1, count($name)-1);
    $name = array_reverse($name);
    $name = implode('.', $name);
    return $name;
}

/**
 * Retourne l'extension du fichier sans son nom
 * 
 * @param string $filename nom du fichier avec extension
 * 
 * @return string
 */
function getExtensionFile($filename){
    $name = explode('.',$filename);
    $name = array_reverse($name);
    return $name[0];
}


/**
 * Retourne une liste de fichiers contenu dans le dossier cv
 * 
 * Attention cette fonction est incluencé par $_SESSION['list-file-format'] 
 * qui détermine le filtre mais l'argument $regex a la priorité
 * 
 * @param string $regex Chaine Expréssion réguliere pour filtrer les fichiers par extentions
 * 
 * @return array 
 */
function fileList($regex='*.*') {

    if ($regex == '*.*' && isset($_SESSION['list-file-format'])) {

        $regex = '*.'.$_SESSION["list-file-format"];
        $ListEntriesDir = glob(PATH_CV.$regex);
    }else{
        $ListEntriesDir = scandir(PATH_CV);
    }
    

    $ListEntriesDir = array_map(function($v){ return basename($v); }, $ListEntriesDir);

    return array_splice($ListEntriesDir, 3, count($ListEntriesDir));
}

function deleteFilesVoids(){
    $fileList = fileList();
    foreach ($fileList as $key => $filename) {
        if(filesize(PATH_CV.$filename) === 0) {
            if (unlink(PATH_CV.$filename)) {} // echo "Fichier vide $filename supprimé<br />";
        }
    }
}

function removeDuplicateFiles() {
    $fileList = fileList();
    $md5Files = [];
    foreach ($fileList as $key => $filename) {
        $md5Value = md5_file(PATH_CV.$filename);

        if(file_exists(PATH_CV.$filename)) {
            if(!isset($md5Files[$md5Value])) {
                $md5Files[$md5Value] = $filename;
                //echo "Enregistrement de $filename<br />";
            } else {
                
                $dateFilename = filemtime(PATH_CV.$filename);            
                
                if(file_exists(PATH_CV.$md5Files[$md5Value])){
                    $dateMd5File = filemtime(PATH_CV.$md5Files[$md5Value]);
                    if (preg_match("/^[0-9a-f]{6,}/", $md5Files[$md5Value])) {
                    
                        if (unlink(PATH_CV.$md5Files[$md5Value])) {} // echo $md5Files[$md5Value]." supprimé contre ".$filename.'<br />';
                    }else if (preg_match("/^[0-9a-f]{6,}/", $filename)) {
            
                        if (unlink(PATH_CV.$filename)) {} // echo $filename." supprimé contre ".$md5Files[$md5Value].'<br />';
                    }else{
                        if($filename === $md5Files[$md5Value]){
                            if (unlink(PATH_CV.$md5Files[$md5Value])) {} // echo $md5Files[$md5Value]." supprimé contre ".$filename;
                        }
                    }
                }
            }
        }  
    }
}

function checkValidFilename(){
    $fileList = fileList();
    $slugify = new Slugify();

    foreach($fileList as $filename) {
        
        $ext = getExtensionFile($filename);
        if (strlen($ext) < 5) {
            $newName = $slugify->slugify(getNameFile($filename)).'.'.$ext;
        }else{
            $newName = $slugify->slugify($filename);
        }
        
        rename(PATH_CV.$filename, PATH_CV.$newName);
        //echo "$filename > $newName<br />";
    }
    
}