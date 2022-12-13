<?php  
  include_once(__DIR__ . '/data_scraper.php');
  include_once(__DIR__ . '/db_controls.php');
?>
<?php
  $url = "https://bigcharts.marketwatch.com/historical/default.asp?symb=csc&closeDate=12%2F01%2F17&x=32&y=18";
  // $url = "https://bigcharts.marketwatch.com/historical/default.asp?symb=msft&closeDate=12%2F12%2F17&x=33&y=15";

  if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['action_to_call']) && ($_GET['action_to_call'] != NULL)){
      $symbol = $_GET['symbol'];
      $closing_date = $_GET['closing_date'];

      $scraped_data = new DataScraper();
      $scraped_data->calculate_date($symbol, $closing_date);

      
    }

      $qry = "select * from observations";
                
      $data = new DatabaseControls();
      $response = $data->select_query($qry);
      $response = json_decode($response, true);

      $qry = "SELECT max(volume) as'volume' FROM `observations`";
      $vol = $data->select_max($qry);
  }



  
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Document</title>
</head>
<body class="container">
  <div class="row">
    <div class="col-md">
      <form action="" method="get" style="padding:3em;background-color:rgb(200,200,200);">
        
        <input type="hidden" name="action_to_call" value="run" />
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="symbol">Symbol</label>
              <input type="text" name="symbol" id="" class="form-control" />
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label for="closing_date">Closing Date</label>
              <input type="text" name="closing_date" id="" placeholder="mm/dd/yyyy" class="form-control" />
            </div>
          </div>
        </div>

        <input type="submit" value="Run" class="btn btn-primary btn-md" style="margin-top:15px;" />
      </form>
    </div>
  </div>

  <div class="row">
    <div class="col-md">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Symbol</th>
            <th scope="col">Date</th>
            <th scope="col">Closing Price</th>
            <th scope="col">Volume</th>
            <th scope="col">Edit</th>
          </tr>
        </thead>
        <tbody>
          <?php
            if(isset($response) && $response != NULL){
              foreach($response as $table_data){
                if($table_data['volume'] == $vol){ 
          ?>
                  <tr>
                    <td style="color:red;"><?php echo $table_data['id']; ?></td>
                    <td style="color:red;"><?php echo $table_data['symbol']; ?></td>
                    <td style="color:red;"><?php echo date('m/d/Y', strtotime($table_data['date'])); ?></td>
                    <td style="color:red;"><?php echo $table_data['closing_price']; ?></td>
                    <td style="color:red;"><?php echo number_format($table_data['volume'],2,"."); ?></td>
                    <td><a href="<?php echo substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'?')) . 'edit.php?id='.$table_data['id']; ?>">Edit</a></td>
                  </tr>
          <?php 
                }
                else {
          ?>
                  <tr>
                    <td><?php echo $table_data['id']; ?></td>
                    <td><?php echo $table_data['symbol']; ?></td>
                    <td><?php echo date('m/d/Y', strtotime($table_data['date'])); ?></td>
                    <td><?php echo $table_data['closing_price']; ?></td>
                    <td><?php echo number_format($table_data['volume'],2,"."); ?></td>
                    <td><a href="<?php echo substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'?')) . 'edit.php?id='.$table_data['id']; ?>">Edit</a></td>
                  </tr>
          <?php
                }
              }
            }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>