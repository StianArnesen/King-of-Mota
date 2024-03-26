<?php
    session_start();

    $LOGIN_ERR = -1;

        $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

        if($dbCon){

            $input_username = strip_tags($_SESSION['game_username']);
            $input_password = strip_tags($_POST['password']);

            $sqlCommands = "SELECT id, password, money FROM users WHERE username='$input_username'";

            $query = mysqli_query($dbCon, $sqlCommands);
            $data_row = mysqli_fetch_row($query);

            $data_user_id = $data_row[0];
            $data_password = $data_row[1];
            $data_money = $data_row[2];


            mysql_select_db("motagamedata", $dbCon);

            $item_id = $_POST['item_id'];

            $query1 = "SELECT * FROM items WHERE id='$item_id'";

            $item = mysql_query($query1);

            $item_row = mysql_fetch_array($item);


            if($input_password == $data_password){
                if($data_money >= $_POST['price']){
                    $old_money = $data_money;
                    $new_money = $old_money - $_POST['price'];

                    mysqli_select_db("motagamedata", $dbCon);

                    $update_data_query = "UPDATE users SET money='$new_money' WHERE id='$data_user_id'";
                    $run = mysqli_query($dbCon,$update_data_query);


                    echo "Item bought! SQL: <h1>" . mysqli_error($dbCon) . "</h1>";
                }
                else{
                    echo "You need more money!";
                }

            }else{
                die("Password miss-match!!");
            }
    }
?>