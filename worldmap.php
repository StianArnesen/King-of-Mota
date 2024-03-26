<?php
    session_start();

    if(isset($_SESSION['game_username']) && ($_SESSION['game_username'] != ""))
    {

    }
    else{
        header("Location: index.php");
    }

  
    if(isset($_SESSION['game_username'])){

       $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

        if($dbCon){

            $session_username = $_SESSION['game_username'];

            $sqlCommands = "SELECT id, username, password, money, level, profile_picture FROM users WHERE username='$session_username'";

            $query = mysqli_query($dbCon, $sqlCommands);
            $data_row = mysqli_fetch_row($query);

            $data_user_id = $data_row[0];
            $data_username = $data_row[1];
            $data_password = $data_row[2];
            $data_money = $data_row[3];
            $data_level = $data_row[4];
      $data_profile_picture = $data_row[5];
        }
        else{
            die("Error");
            $LOGIN_ERR = 2;
        }
}
else{
    header("Location: index.php");
}
?>

<html>
  <head>
  <title>King of Mota - World Map</title>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["geochart"]});
      google.setOnLoadCallback(drawRegionsMap);

      function drawRegionsMap() {

        var data = google.visualization.arrayToDataTable([
          ['Country', 'Popularity'],
          ['Germany', 200],
          ['United States', 300],
          ['Brazil', 400],
          ['Canada', 500],
          ['France', 600],
          ['RU', 700]
        ]);

        var options = {};

        var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
          <?php
                $btn_file = fopen("layout/top_banner/top_banner.php", "r") or die("Unable to open file!");
                echo fread($btn_file,filesize("layout/top_banner/top_banner.php"));
                fclose($btn_file);
          ?>
    <div id="regions_div" style="width: 100%; height: 94%; margin-top:60px; "></div>
  </body>
</html>