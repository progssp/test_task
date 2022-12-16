<?php  
  include_once(__DIR__ . '/data_scraper.php');
  include_once(__DIR__ . '/db_controls.php');


  if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['action_to_call']) && ($_GET['action_to_call'] != NULL)){
      $symbol = $_GET['symbol'];
      $closing_date = $_GET['closing_date'];

      $data = new DataScraper();
      $data->calculate_date($symbol, $closing_date);      
    }
    else if(isset($_GET['refresh']) && ($_GET['refresh'] == 1)){
      $data = new DatabaseControls();
      $data->save_query("delete from observations");
      $data->save_query("alter table observations auto_increment 1");
    }

      $data = new DatabaseControls();
      $qry = "select * from observations";
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
        <form action="" method="get" id="run_frm" style="padding:3em;background-color:rgb(200,200,200);">
          
          <input type="hidden" name="action_to_call" value="run" />
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="symbol">Symbol</label>
                <input required type="text" name="symbol" id="" class="form-control" />
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="closing_date">Closing Date</label>
                <input required type="text" name="closing_date" id="" placeholder="mm/dd/yyyy" class="form-control" />
              </div>
            </div>
          </div>

          <input type="submit" value="Run" class="btn btn-primary btn-md" style="margin-top:15px;" />
          <?php
            $refresh_lnk = $_SERVER['REQUEST_URI'] . "?refresh=1";
            $saved_queries_lnk = "/task/saved_queries.php";
            if(strpos($_SERVER['REQUEST_URI'],"?")){
              $refresh_lnk = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],"?"));
              $refresh_lnk .= "?refresh=1";
            }
          ?>
          <a href="<?php echo $refresh_lnk; ?>" class="btn btn-success btn-md" style="margin-top:15px;">Refresh</a>
          <a href="<?php echo $saved_queries_lnk; ?>" class="btn btn-success btn-md" style="margin-top:15px;">Saved queries</a>
        </form>
      </div>
    </div>

    <div class="row">
      <div class="col-md">
        <p id="waiting_msg"></p>
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


    <script>
      document.getElementById('run_frm').addEventListener('submit',(evt) => {
        var form_data = "";
        document.getElementById('waiting_msg').innerHTML = "Loading data...";
        evt.preventDefault();
        var fd = new FormData(evt.target);
        console.log(fd);
        fd = Object.fromEntries(fd.entries());
        console.log(fd);
        for (const key in fd) {  
          form_data += key+"="+fd[key]+"&";
        }
        console.log(form_data);
        // return;
        var url_to_req = window.location.origin + "/task/api.php";
        fetch(url_to_req,{
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: form_data
        })
        .then(res => res.json())
        .then(response => {
          if(response.status){
            window.location.href = "/task";
          }
          else{
            document.getElementById('waiting_msg').innerHTML = response.msg;
          }
        })
        .catch(err => {
          document.getElementById('waiting_msg').innerHTML = "Loading error";
        console.log(err);
        });     
      });
    </script>
  </body>
</html>