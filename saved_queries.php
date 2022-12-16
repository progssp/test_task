<?php
  include_once(__DIR__ . '/db_controls.php');

  $data = new DatabaseControls();
  $qry = "select * from saved_queries order by id";
  $response = $data->select_query($qry);
  $response = json_decode($response, true);
   
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Saved Queries</title>
  </head>
  <body class="container">
    <div class="row">
      <div class="col-md">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Symbol</th>
              <th scope="col">Closing Date</th>
            </tr>
          </thead>
          <tbody>
            <?php
              if(isset($response) && $response != NULL){
                foreach($response as $table_data){
            ?>
                  <tr>
                    <td><?php echo $table_data['id']; ?></td>
                    <td><?php echo $table_data['symbol']; ?></td>
                    <td><?php echo date('m/d/Y', strtotime($table_data['closing_date'])); ?></td>
                    <td><a href="<?php echo substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'?')) . 'edit_saved.php?id='.$table_data['id']; ?>">Edit</a></td>
                  </tr>
            <?php 
                }
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </body>
</html>