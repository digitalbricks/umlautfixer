<?php
$frontent_version = "1.0";

// configure input and output folders
$settings = array(
    'inputDirectory' => 'input',
    'outputDirectory' => 'output',
);

// get files in input directory
$files = getFilesInDirectory($settings['inputDirectory']);

// instantiate umlautfixer
require_once('class.php');
$fixer = new Fixer();


/**
 * @param string $folder
 * @return array
 */
function getFilesInDirectory(string $folder){
    // define files to be ignored in input folder
    $ignore_files = array(
        '.DS_Store',
        'thumbs.db',
        'desktop.ini',
        '.gitignore',
        '.gitkeep'
    );

    // scan input directory
    $dir = dirname(__FILE__).DIRECTORY_SEPARATOR.$folder;
    $scannedfiles = scandir($dir);
    $files = array();
    foreach($scannedfiles as $file)
    {
        $filepath = $dir.DIRECTORY_SEPARATOR.$file;
        if(is_file($filepath) AND !in_array($file,$ignore_files)){
            $files[$file] = array(
                'size' => filesize($filepath),
                'date' => filemtime($filepath)
            );
        }
    }

    return $files;
}

/**
 * @source https://stackoverflow.com/a/2021729
 * @param string $file
 * @return false|string|null
 */
function sanitizeFilename(string $file){
    $file = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file);
    $file = mb_ereg_replace("([\.]{2,})", '', $file);
    return $file;
}




?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Umlautfixer</title>
    <style>
        html{
            font-family: Arial, sans-serif;
        }
        .container{
            width: 95%;
            max-width: 600px;
            margin: 0 auto;
        }
        .fileinfo{
            background-color: #fae3e1;
            border: 1px solid #fff;
            padding: 1rem;
        }
        .fileinfo table{
            width: 100%;
            border-collapse: collapse;
        }
        .fileinfo table td,
        .fileinfo table th{
            border-bottom: 1px solid #000;
            padding: 0.3rem;
        }
        table th{
            text-align: left;
        }
        button{
            display: inline-block;
            padding: 0.3em 2em;
            background-color: #f00;
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0px 0px 15px 5px rgba(0,0,0,0.35);
            border: none;
            margin-top: 1rem; 
        }
    </style>
</head>
<body>
    <div class="container">

        <h1>Umlautfixer</h1>
        <p><small>Fontent version: v<?=$frontent_version?> | Class version: <?=$fixer->getVersion()?></small></p>

        <p>This script is fixing broken umlauts in in files.</p>
        
        <?php if(!isset($_POST['file'])):?>
                <div class="fileinfo">

                    <p><strong>Select file to fix:</strong></p>
                    <form action="<?=basename($_SERVER['PHP_SELF'])?>" method="post">
                        <table>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Filename</th>
                                    <th>Size</th>
                                    <th>Modified</th>
                                </tr>
                            </thead>
                            <?php foreach($files as $file=>$info):?>
                            <tr>
                                <td><input type="radio" name="file" value="<?=$file?>" /></td>
                                <td><strong><?=$file?></strong></td>
                                <td><?=round($info['size']/1024,2)?> kB</td>
                                <td><?=date('d.m.Y H:i:s', $info['date'])?></td>
                            </tr>    
                            <?php endforeach;?>
                        </table>
                        <button type="submit">Fix it!</button>
                    </form>
                </div>
            </div>
        <?php else:?>
            <div class="fileinfo">
                <?php
                // get file from Post
                $filename = sanitizeFilename($_POST['file']);

                // set input and output directory in umlautfixer
                $fixer->setInputDir($settings['inputDirectory']);
                $fixer->setOutputDir($settings['outputDirectory']);

                // set file
                $fixer->setFile($filename);


                //$fixer->fixUmlauts();
                ?>

                <?php if($fixer->fileExists()):?>
                    <?php $fixer->fixUmlauts(); ?>
                    <?php if($fixer->getCount()>0):?>
                        File <strong><?=$fixer->getFile()?></strong> processed (<?=$fixer->getCount()?> replacements). See output folder (<strong><?=$settings['outputDirectory']?></strong>).
                    <?php else:?>
                        <strong>No chars to replace found in input file!</strong>
                    <?php endif;?>
                    
                <?php else: ?>
                        <strong>File "<?=$fixer->getFile()?>" not found in directory "<?=$settings['inputDirectory']?>"</strong>
                <?php endif;?>
            </div>


        
        <?php endif;?>
    
</body>
</html>