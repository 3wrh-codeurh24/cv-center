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



if (isset($_GET['execute'])) {


    if($_GET['execute'] == 1)  {
        $cmd ='php cvToSql.php >/dev/null 2>&1 & echo $!';
        $pid = shell_exec($cmd);
        $_SESSION['cv-center-pid-execute']  = $pid ;
    }else{
        shell_exec("kill -9 ".$_SESSION['cv-center-pid-execute']);
    }

    exit;
}

if (isset($_GET['info'])) {

    $cv = count(fileList());
    $cvsql = count(scandir(__DIR__.'/cvsql'));
    $cverror = count(scandir(__DIR__.'/cverror'));
    $total = $cv + $cvsql;


    echo json_encode([
        'cv' => $cv,
        'cvsql' => $cvsql,
        'total' => $total,
        'ratio' => $cvsql / $total,
        'percent' => ((round(($cvsql / $total), 2)) * 100),
        'cverror' => $cverror
    ]);
    exit;
}

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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CV CENTER </title>
</head>
<body>
    <button id="execute">Executer le script</button>
    <button id="execute-stop">Stopper le script</button>
    <progress id="progress-bar" max="100" value="70"> 70% </progress>
    <div class="infos">
        <div><span class="info-cv">0</span> cv</div>
        <div><span class="info-cvsql">0</span> cvsql</div>
        <div><span class="info-total">0</span> total</div>
        <div><span class="info-percent">0</span>%</div>
    </div>
    <div>
        <form action="/search" method="get">
            <button type="submit">Rechercher</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <script>
        $(function(){

            function infos(){
                $.ajax({
                    method: 'GET',
                    dataType: "json",
                    url: "/?info=1",
                }).done(function(data){
                    console.log('test ', data);
                    $('.info-cv').text(data.cv)
                    $('.info-cvsql').text(data.cvsql)
                    $('.info-total').text(data.total)
                    $('.info-percent').text(data.percent)
                    $('#progress-bar').attr('max', data.total);
                    $('#progress-bar').attr('value', data.cvsql);
                    $('#progress-bar').text(data.percent+'%');

                }).fail(function(data) {
                    console.log( "error update", data );
                });
            }


            $('#execute').click(async function(){                
                
                window.infoLoop = setInterval(() => {
                    infos();
                }, 1000);
                
                let response = await fetch('/?execute=1');
                let data = await response.json();
                console.log(data)
            })
            
            $('#execute-stop').click(async function(){ 
                clearInterval(window.infoLoop);
                let response = await fetch('/?execute=0');
                let data = await response.json();
                console.log(data)
            })
            infos();

        });
    </script>
</body>
</html>
<?php












