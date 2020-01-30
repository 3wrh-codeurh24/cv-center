<?php
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
    }
    

    $ListEntriesDir = glob(PATH_CV.$regex);
    $ListEntriesDir = array_map(function($v){ return basename($v); }, $ListEntriesDir);

    return array_splice($ListEntriesDir, 3, count($ListEntriesDir));
}