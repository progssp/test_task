<?php
    include_once(__DIR__."/db_controls.php");
    include_once(__DIR__."/data_scraper.php");

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['action_to_call']) && ($_POST['action_to_call'] != NULL)){
            if($_POST['action_to_call'] == "load_data"){

                $data = new DatabaseControls();
                $qry = "select * from observations";
                $response = $data->select_query($qry);
                $response = json_decode($response, true);
                header("Content-Type: application/json");
                echo json_encode(['status'=>true, 'data' => $response]);
            }
            else if($_POST['action_to_call'] == "run"){
                $symbol = $_POST['symbol'];
                $closing_date = $_POST['closing_date'];

                $data = new DataScraper();
                $data->calculate_date($symbol, $closing_date);

                $data = new DatabaseControls();
                $qry = "select * from observations";
                $response = $data->select_query($qry);
                $response = json_decode($response, true);
                header("Content-Type: application/json");
                echo json_encode(['status'=>true, 'data' => $response]);
            }
            else if($_POST['action_to_call'] == "refresh"){
                $data = new DatabaseControls();
                $data->save_query("delete from observations");
                $data->save_query("alter table observations auto_increment 1");
                header("Content-Type: application/json");
                echo json_encode(['status'=>true, 'data' => 'done']);
            }
            else{
                header("Content-Type: application/json");
                echo json_encode(['status'=>false, 'msg' => 'invalid action_to_call']);
            }
        }
        else{
            header("Content-Type: application/json");
            echo json_encode(['status'=>false, 'msg' => 'please specify some action_to_call']);
        }
    }

?>