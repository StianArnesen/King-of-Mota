<?php

class PageClass
{
    private $TOP_BANNER;

    private $HEAD_INFO;

    private $ROOT_DIR;

    private $_HEAD_DIR = "layout/header/header.html";
    
    public function __construct()
    {
        $this->ROOT_DIR = $_SERVER['DOCUMENT_ROOT'];
        
        $this->mainInit();
    }
    private function mainInit()
    {
        if($this->load_topBanner()){
            if($this->load_header())
            {
                
            }
        }else{
            die("Warning: FAILED TO LOAD TOP_BANNER");
        }
    }
    private function load_header()
    {
        try{
            $this->HEAD_INFO = fopen($this->ROOT_DIR .  $this->_HEAD_DIR, "r") or die("Unable to open file!");
            return true;
        }catch (HttpUrlException $e)
        {
            return false;
        }
    }
    private function load_topBanner()
    {
        try{
            $this->TOP_BANNER = fopen($this->ROOT_DIR . "layout/top_banner/top_banner.php", "r") or die("Unable to open file!");
            return true;
        }catch (HttpUrlException $e)
        {
            echo $e->getMessage();
        }
        return false;
    }


    /**
     * @param $TOP_BANNER: resource
     */
    private function readStaticEngineTopBanner($TOP_BANNER)
    {
        $file_size = filesize("layout/top_banner/top_banner.php");

        fread($TOP_BANNER, $file_size);
    }

    public function getHeaderInfo()
    {
        $RESULT = fread($this->HEAD_INFO,filesize($this->ROOT_DIR . $this->_HEAD_DIR));
        fclose($this->HEAD_INFO);

        return $RESULT;
    }
    public function getTopBanner()
    {
        $RESULT = fread($this->TOP_BANNER,filesize($this->ROOT_DIR ."layout/top_banner/top_banner.php"));
        fclose($this->TOP_BANNER);

        return $RESULT;
    }
}
