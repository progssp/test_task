<?php
  require_once(__DIR__."/db_controls.php");
?>
<?php
    class DataScraper {
        private $updated_date;

        public function __construct(){
            $this->updated_date = NULL;
        }

        public function getData($url){
            ini_set('max_execution_time', 0); // 0 = Unlimited
            $response_obj = new \stdClass;
            $response_closing_price = 0;
            $response_volume = NULL;
        
            // curl init
            $curl = curl_init();
            
            // curl options
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
        
            $result = curl_exec($curl);
        
            curl_close($curl);
        
            // loading html code in domdocument
            $document = new DOMDocument();
            @$document->loadHTML($result);
            // $doc = $document->saveHTML($document);
        
            // getting table node from html code
            $table = $document->getElementById("historicalquote");
        
        
            $symbol_text_box = $document->getElementById("symb");
            $close_date_text_box = $document->getElementById("closeDate");
            // echo "symbol: " . $symbol_text_box->getAttribute('value') . "<br/>";
            //echo "close_date: " . $close_date_text_box->getAttribute('value') . "<br/>";
        
            // echo date("Y-m-d",strtotime($close_date_text_box->getAttribute('value') . "-5 days"));
            
        
        
            if($table != NULL){
            
              $headerEl = $table->getElementsByTagName("tr");    
              
              foreach ($headerEl as $row) {
                $header_val = trim(strtolower($row->textContent));
        
                if((strpos($header_val,"closing price") !== false)) {
                  $childEl = $row->getElementsByTagName("td");
        
                  foreach ($childEl as $td_data) {
                    $response_closing_price = floatval($td_data->textContent);
                  }
                }
                else if(strpos($header_val,"volume") !== false){
                  $childEl = $row->getElementsByTagName("td");
        
                  foreach ($childEl as $td_data) {
                    $response_volume = ($td_data->textContent);
                  }
                }
              }
        
              $response_obj->symbol = trim($symbol_text_box->getAttribute('value'));
              $response_obj->closing_date = date("m/d/Y",strtotime($close_date_text_box->getAttribute('value')));              
              $response_obj->closing_price = floatval($response_closing_price);

              
              //converting $reponse_closing_price to float for database saving
              $response_volume = str_replace(",","",$response_volume);
              $response_obj->volume = floatval($response_volume);

              
              
              $qry = "insert into observations values(0,'".$response_obj->symbol."','".date("Y-m-d",strtotime($close_date_text_box->getAttribute('value')))."',".$response_obj->closing_price.",".$response_obj->volume.")";
              
              $data = new DatabaseControls();
              $data->save_query($qry);
            }
            else {
              $response_obj->symbol = trim($symbol_text_box->getAttribute('value'));
              $response_obj->closing_date = date("Y-m-d",strtotime($close_date_text_box->getAttribute('value')));
              $response_obj->closing_price = 0;
              $response_obj->volume = 0;

              $qry = "insert into observations values(0,'".$response_obj->symbol."','".date("Y-m-d",strtotime($close_date_text_box->getAttribute('value')))."',".$response_obj->closing_price.",".$response_obj->volume.")";
              
              $data = new DatabaseControls();
              $data->save_query($qry);
            }
        
            return $response_obj;
        }

        public function calculate_date($symbol, $closing_date){
          $qry = "insert into saved_queries values(0,'".$symbol."','".date("Y-m-d",strtotime($closing_date))."')";
              
          $data = new DatabaseControls();
          $data->save_query($qry);
          $updated_date = date('m/d/Y', strtotime($closing_date));
          for($i=1;$i<=30;$i++){
            
            if($i != 1){
              $updated_date = date('m/d/Y', strtotime($updated_date . "-1 day"));
            }
            $url = "https://bigcharts.marketwatch.com/historical/default.asp?symb=".$symbol."&closeDate=".$updated_date;
            $res = $this->getData($url);
          }
        }
    }
?>