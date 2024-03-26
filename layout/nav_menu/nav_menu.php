<?php

class navigationMenu
{
  private $HTML;

  private $ROOT;

  public function __construct()
  {
    $this->ROOT = $_SERVER['DOCUMENT_ROOT'];
    $this->initHtml();
  }
  public function getNavbar()
  {
    return $this->HTML;
  }
  private function initHtml()
  {
    $this->HTML = '
    <head>
      <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
      <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

      <link href="/layout/nav_menu/nav_menu_style.css" rel="stylesheet/css" type="text/css">
      <script src="/layout/nav_menu/nav_menu_js.js"></script>
    </head>
    <nav id="slide-menu">
    	<ul>
    		<li class="timeline">Timeline</li>
    		<li class="events">Events</li>
    		<li class="calendar">Calendar</li>
    		<li class="sep settings">Settings</li>
    		<li class="logout">Logout</li>
    	</ul>
    </nav>
    ';

  }
}
