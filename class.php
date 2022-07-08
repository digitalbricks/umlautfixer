<?php
class Fixer {

    private $version = "0.5";

    private $file = false;
    private $fileexists = false;

    private $in_folder = "input";
    private $out_folder = "output";
    private $processed_suffix = ".processed";

    private $in_file_path = false;
    private $out_file_path = false;

    private $replaced_count = 0;

    private $replacements =  array(
        // see https://www.i18nqa.com/debug/utf8-debug.html
        // see https://bueltge.de/wp-content/download/wk/utf-8_kodierungen.pdf
        // NOTE: I only implemented some selected characters, so the list isn't complete
        'Ã‚'=> 'Á', 
        'ÃŒ'=> 'Í',  
        'Â'=> '', 
        'Ã–'=> 'Ö', 
        'Ã—'=> '×', 
        'Ã¡'=> 'á', 
        'Ã¿'=> 'ÿ', 
        'Ã…'=> 'Å', 
        'Ã·'=> '÷', 
        'Ã‘'=> 'Ñ', 
        'Ã’'=> 'Ò', 
        'Å‚'=> 'ł', 
        'Ã‚'=> 'Â', 
        'â‚¬'=> '€', 
        'Ã‹'=> 'Ë', 
        'Ã›'=> 'Û', 
        'Ã“'=> 'Ó', 
        'Ã”'=> 'Ô', 
        'Å„'=> 'ń', 
        'Ã„'=> 'Ä', 
        'â„¢'=> '™', 
        'Ã«'=> 'ë', 
        'Ã»'=> 'û', 
        'Ã§'=> 'ç', 
        'Ã¶'=> 'ö', 
        'Ã‰'=> 'É', 
        'Ã†'=> 'Æ', 
        'Ã‡'=> 'Ç', 
        'Ã•'=> 'Õ', 
        'Â´'=> '´', 
        'Ã´'=> 'ô', 
        'Ã˜'=> 'Ø', 
        'Ã¯'=> 'ï', 
        'Ã¨'=> 'è', 
        'Ã¸'=> 'ø', 
        'Ãˆ'=> 'È', 
        'Â°'=> '°', 
        'Ã°'=> 'ð', 
        'Â©'=> '©', 
        'Ã©'=> 'é', 
        'Â®'=> '®', 
        'Ã®'=> 'î', 
        'Â±'=> '±', 
        'Ã±'=> 'ñ', 
        'Ã¬'=> 'ì', 
        'Ã¦'=> 'æ', 
        'Ã¤'=> 'ä', 
        'Ã¢'=> 'â', 
        'Ã£'=> 'ã', 
        'Ã¥'=> 'å', 
        'â€'=> '”', 
        'Ã€'=> 'À', 
        'â€“'=> '–', 
        'Ã¹'=> 'ù', 
        'Â½'=> '½', 
        'Ã½'=> 'ý', 
        'Â¼'=> '¼', 
        'Ã¼'=> 'ü', 
        'Â²'=> '²', 
        'Ã²'=> 'ò', 
        'Â³'=> '³', 
        'Ã³'=> 'ó', 
        'Â¾'=> '¾', 
        'Ã¾'=> 'þ', 
        'Ãª'=> 'ê', 
        'Ãº'=> 'ú', 
        'ÃŒ'=> 'Ì', 
        'Ãœ'=> 'Ü', 
        'ÃŠ'=> 'Ê', 
        'Ãš'=> 'Ú', 
        'Ã™'=> 'Ù', 
        'ÃŸ'=> 'ß', 
        'ÃŽ'=> 'Î', 
        'Ãž'=> 'Þ', 
        'Ãµ'=> 'õ',
        'Ãƒ'=> 'Ã',
        'Ä™' => 'ę',
        'Å›' => 'ś',
        'Ä‡' => 'ć',
        'Å¼' => 'ż',
        'Ä…' => 'ą',
        'Ä™' => 'ę',
        'Åº' => 'ź',
        'Â¤' => '¤',
        'Â¦' => '¦',
        'Â§' => '§',
        'Â©' => '©',
        'Â«' => '«',
        'Â-' => '-',
        'Â®' => '®',
        'Â°' => '°',
        'Â±' => '±',
        'Âμ' => 'μ',
        'Â·' => '·',
        'Â»' => '»',
        'ÃŸ' => 'ß',
        'Å™' => 'ø',
        'â€°' => '‰',
        'â˜¯' => '☯',

        // single character replacements needs to be the last entries
        // in order to prevent conflicts with multi character replacements
        'Ã­'=> 'í', 
    );

    function __construct($file = null){
        if(is_string($file)){
            $this->setFile($file);
        }
    }

    public function setInputDir(string $dir){
        $this->in_folder = $dir;
    }

    public function setOutputDir(string $dir){
        $this->out_folder = $dir;
    }

    public function setFile(string $file){
        $this->file = $file;
        $this->in_file_path = $this->in_folder.DIRECTORY_SEPARATOR.$this->file;
        $this->out_file_path = $this->out_folder.DIRECTORY_SEPARATOR.$this->file;
        if(file_exists($this->in_file_path)){
            $this->fileexists = true;
        }
    }

    public function getVersion(){
        return $this->version;
    }

    public function getFile(){
        return $this->file;
    }

    public function fileExists(){
        return $this->fileexists;
    }

    public function getCount(){
        return $this->replaced_count;
    }

    public function fixUmlauts(){
        if($this->fileexists){

            // read input file to variable
            $filecontents = file_get_contents($this->in_file_path);

            // do replacements
            foreach($this->replacements as $old=>$new){
                if($filecontents = str_replace($old,$new,$filecontents,$counter)){
                    $this->replaced_count = $this->replaced_count + intval($counter);
                };
            }

            // write to ouput file
            file_put_contents($this->out_file_path.$this->processed_suffix, $filecontents);

        } else {
            throw new Exception("File does not exist in input folder ({$this->in_folder}).");
        }
        
    }
}
?>