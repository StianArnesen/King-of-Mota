<?php


class Overlay
{
    private $LAYOUT;

    private $_TYPE;
    private $_TITLE;
    private $_DATA;

    public function __construct($TYPE, $TITLE, $DATA)
    {
        $this->newDialog($TYPE, $TITLE, $DATA);
    }
    private function newDialog($TYPE, $TITLE , $DATA)
    {
        $this->_TYPE = $TYPE;
        $this->_TITLE = $TITLE;
        $this->_DATA= $DATA;

        $this->LAYOUT = "";

        $this->setDialogConfirm();

    }
    public function setDialogConfirm()
    {

        $TITLE = $this->_TITLE;
        $DATA = $this->_DATA;


        $this->LAYOUT .=
            '
<link rel="stylesheet" href="style.css">

<div id="overlay">


    <div id="overlay_dialog">
        <div id="overlay_dialog_title">'. $TITLE. '</div>
        <div id="overlay_dialog_info">'. $DATA .'</div>
        <div id="overlay_dialog_buttons">
            <button>Yes</button>
            <button>No</button>
        </div>

    </div>

</div>
        ';
    }
    public function getOverlay()
    {
        return $this->LAYOUT;
    }
}


$OVERLAY = new Overlay(0,"Confirm this", "Are you sure you want to confirm this?");

die($OVERLAY->getOverlay());