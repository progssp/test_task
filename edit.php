<?php
    require_once(__DIR__."/db_controls.php");
?>

<?php
    if(!isset($_GET['id']) || $_GET['id'] == NULL){
        die("id not found");
    }

    $id = $_GET['id'];

    $data = new DatabaseControls();
    $record_data = $data->select_query("select * from observations where id=".$_GET['id']);
    $record_data = json_decode($record_data, true);
?>

<?php
    if(isset($_POST['action_to_call']) && $_POST['action_to_call'] != NULL){
        
        $sym = $_POST['symbol'];
        $date = date('Y-m-d',strtotime($_POST['date']));
        $closing_price = $_POST['closing_price'];
        $volume = $_POST['volume'];

        $qry = "update observations set symbol = '".$sym."', date = '".$date."', closing_price=".$closing_price.", volume=".$volume." where id=".$id;

        $data->save_query($qry);

        $record_data = $data->select_query("select * from observations where id=".$_GET['id']);
        $record_data = json_decode($record_data, true);

        $location = substr($_SERVER['REQUEST_URI'],0,strrpos($_SERVER['REQUEST_URI'],"/"));
        header("location: " . $location);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <title>Edit</title>
    </head>
    <body class="container">

        <form action="" method="post">
            <input type="hidden" name="action_to_call" value="edit" />
            <div class="row">
                <div class="col-md">
                    <div class="form-group">
                        <label for="symbol">Symbol</label>
                        <input required type="text" name="symbol" id="" class="form-control" value="<?php echo (isset($record_data[0]['symbol']))?$record_data[0]['symbol']:''; ?>" />
                    </div>
                </div>

                <div class="col-md">
                    <div class="form-group">
                        <label for="symbol">Date</label>
                        <input required type="text" name="date" id="" class="form-control" value="<?php echo (isset($record_data[0]['date']))?date('m/d/Y',strtotime($record_data[0]['date'])):''; ?>" />
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top:10px;">
                <div class="col-md">
                    <div class="form-group">
                        <label for="symbol">Closing price</label>
                        <input required type="text" name="closing_price" id="" class="form-control" value="<?php echo (isset($record_data[0]['closing_price']))?$record_data[0]['closing_price']:''; ?>" />
                    </div>
                </div>

                <div class="col-md">
                    <div class="form-group">
                        <label for="symbol">Volume</label>
                        <input required type="text" name="volume" id="" class="form-control" value="<?php echo (isset($record_data[0]['volume']))?$record_data[0]['volume']:''; ?>" />
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top:10px;">
                <div class="col-md">
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary btn-md" value="Edit" />
                    </div>
                </div>
            </div>
        </form>
        
    </body>
</html>