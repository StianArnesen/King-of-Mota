<?php

class CrewPanel
{
    private $CREW;

    private $_ID;

    public function __construct($CREW_ID)
    {
        $this->_ID = $CREW_ID;
    }
    private function loadPanelInfo()
    {
        $ID = $this->_ID;
    }
    public function getCrewPanel()
    {
        $RESULT = "";

        $RESULT .= '<div id="crew_panel_view">
                        <div id="panel_title"> <span>Crew panel</span></div>

                        <div id="crew_image"><img src="" </div>
                    </div>
                    ';

        return $RESULT;
    }
}