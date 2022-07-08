<?php
class Fixer {

    private $version = "0.7";

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
        'â‚¬' => '€',
        'â€š' => '‚',
        'Æ’' => 'ƒ',
        'â€ž' => '„',
        'â€¦' => '…',
        'â€' => '”',
        'â€¡' => '‡',
        'Ë†' => 'ˆ',
        'â€°' => '‰',
        'â€¹' => '‹',
        'Å’' => 'Œ',
        'Å½' => 'Ž',
        'â€˜' => '‘',
        'â€™' => '’',
        'â€œ' => '“',
        'â€¢' => '•',
        'â€“' => '–',
        'â€”' => '—',
        'Ëœ' => '˜',
        'â„¢' => '™',
        'Å¡' => 'š',
        'â€º' => '›',
        'Å“' => 'œ',
        'Å¾' => 'ž',
        'Å¸' => 'Ÿ',
        'Â¡' => '¡',
        'Â¢' => '¢',
        'Â£' => '£',
        'Â¤' => '¤',
        'Â¥' => '¥',
        'Â¦' => '¦',
        'Â§' => '§',
        'Â¨' => '¨',
        'Â©' => '©',
        'Âª' => 'ª',
        'Â«' => '«',
        'Â¬' => '¬',
        'Â®' => '®',
        'Â¯' => '¯',
        'Â°' => '°',
        'Â±' => '±',
        'Â²' => '²',
        'Â³' => '³',
        'Â´' => '´',
        'Âµ' => 'µ',
        'Â¶' => '¶',
        'Â·' => '·',
        'Â¸' => '¸',
        'Â¹' => '¹',
        'Âº' => 'º',
        'Â»' => '»',
        'Â¼' => '¼',
        'Â½' => '½',
        'Â¾' => '¾',
        'Â¿' => '¿',
        'Ã€' => 'À',
        'Ã‚' => 'Â',
        'Ãƒ' => 'Ã',
        'Ã„' => 'Ä',
        'Ã…' => 'Å',
        'Ã†' => 'Æ',
        'Ã‡' => 'Ç',
        'Ãˆ' => 'È',
        'Ã‰' => 'É',
        'ÃŠ' => 'Ê',
        'Ã‹' => 'Ë',
        'ÃŒ' => 'Ì',
        'ÃŽ' => 'Î',
        'Ã‘' => 'Ñ',
        'Ã’' => 'Ò',
        'Ã“' => 'Ó',
        'Ã”' => 'Ô',
        'Ã•' => 'Õ',
        'Ã–' => 'Ö',
        'Ã—' => '×',
        'Ã˜' => 'Ø',
        'Ã™' => 'Ù',
        'Ãš' => 'Ú',
        'Ã›' => 'Û',
        'Ãœ' => 'Ü',
        'Ãž' => 'Þ',
        'ÃŸ' => 'ß',
        'Ã¡' => 'á',
        'Ã¢' => 'â',
        'Ã£' => 'ã',
        'Ã¤' => 'ä',
        'Ã¥' => 'å',
        'Ã¦' => 'æ',
        'Ã§' => 'ç',
        'Ã¨' => 'è',
        'Ã©' => 'é',
        'Ãª' => 'ê',
        'Ã«' => 'ë',
        'Ã¬' => 'ì',
        'Ã®' => 'î',
        'Ã¯' => 'ï',
        'Ã°' => 'ð',
        'Ã±' => 'ñ',
        'Ã²' => 'ò',
        'Ã³' => 'ó',
        'Ã´' => 'ô',
        'Ãµ' => 'õ',
        'Ã¶' => 'ö',
        'Ã·' => '÷',
        'Ã¸' => 'ø',
        'Ã¹' => 'ù',
        'Ãº' => 'ú',
        'Ã»' => 'û',
        'Ã¼' => 'ü',
        'Ã½' => 'ý',
        'Ã¾' => 'þ',
        'Ã¿' => 'ÿ',
        'Å‚' => 'ł',
        'Å„' => 'ń',
        'â€' => '”',
        'Ä™' => 'ę',
        'Å›' => 'ś',
        'Ä‡' => 'ć',
        'Å¼' => 'ż',
        'Ä…' => 'ą',
        'Åº' => 'ź',
        'Â-' => '-',
        'Âμ' => 'μ',
        'Å™' => 'ø',
        'â˜¯' => '☯',


        // single character replacements needs to be the last entries
        // in order to prevent conflicts with multi character replacements
        'Ã­'=> 'í', 
        'Å' => 'Š',
        'Â­' => '­',
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

            // create two intermediate arrays for later usage with str_replace
            $search = array();
            $replace = array();
            foreach($this->replacements as $old=>$new){
                    array_push($search, $old);
                    array_push($replace, $new);
            }

            // do replacement
            if($filecontents = str_replace($search,$replace,$filecontents,$counter)){
                $this->replaced_count = intval($counter);
            };

            // write to ouput file if there were replacements
            if($this->replaced_count>0){
                file_put_contents($this->out_file_path.$this->processed_suffix, $filecontents);
            }

            

        } else {
            throw new Exception("File does not exist in input folder ({$this->in_folder}).");
        }
        
    }
}
?>