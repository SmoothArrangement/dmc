<?php
     error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
     require_once('settings.php');
     require_once('simple_html_dom.php');
     ini_set("memory_limit", "8000M");
     ini_set("max_execution_time", "0");
     set_time_limit(0);
     $category_master = array('ka');
     for($i=0; $i < count($category_master); $i++){
          $provider_array = array();
          $newURL = $siteurl."/shop/,,,,".$category_master[$i].".htm";
          $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL, $newURL);  
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
          $str = curl_exec($curl);
          curl_close($curl);
          $html = str_get_html($str);
          foreach($html->find('#centerDiv #midContainerDiv form .bikeSelect') as $div) {
               foreach($div->find('#anbieter a') as $a) {
                    if($a->innertext != "Alle"){
                         $provider_array[] = $a->href;
                    }
               }
          }
          if(!empty($provider_array)){
               for($j=0; $j < count($provider_array); $j++){
                    $bcategory = array();
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $siteurl.$provider_array[$j]);  
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
                    $str = curl_exec($curl);
                    curl_close($curl);
                    $html = str_get_html($str);
                    foreach($html->find('#centerDiv #midContainerDiv form .bikeSelect') as $div) {
                         foreach($div->find('#hersteller a') as $a) {
                              if($a->innertext != "Alle"){
                                   $bcategory[] = $a->href;
                              }
                         }
                    }
                    if(!empty($bcategory)){
                         for($k=0; $k < count($bcategory); $k++){
                              $carmodel = array();
                              $curl = curl_init();
                              curl_setopt($curl, CURLOPT_URL, $siteurl.$bcategory[$k]);  
                              curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                              curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
                              $str = curl_exec($curl);
                              curl_close($curl);
                              $html = str_get_html($str);
                              foreach($html->find('#centerDiv #midContainerDiv form .bikeSelect') as $div) {
                                   foreach($div->find('#modell a') as $a) {
                                        if($a->innertext != "Alle"){
                                             $carmodel[] = $a->href;
                                        }
                                   }
                              }
                              if(!empty($carmodel)){
                                   for($l=0; $l < count($carmodel); $l++){
                                        $construction = array();
                                        $curl = curl_init();
                                        curl_setopt($curl, CURLOPT_URL, $siteurl.$carmodel[$l]);  
                                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                                        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
                                        $str = curl_exec($curl);
                                        curl_close($curl);
                                        $html = str_get_html($str);
                                        foreach($html->find('#centerDiv #midContainerDiv form .bikeSelect') as $div) {
                                             foreach($div->find('#baujahr a') as $a) {
                                                  if($a->innertext != "Alle"){
                                                       $construction[] = $a->href;
                                                  }
                                             }
                                        }
                                        if(!empty($construction)){
                                             for($m=0; $m < count($construction); $m++){
                                                  $url = $siteurl.$construction[$m];
                                                  $sql = "SELECT id FROM urls WHERE url='".$url."';";
                                                  $result = mysql_query($sql);
                                                  if(mysql_num_rows($result) == 0){
                                                       $urlDATA = explode('/', $construction[$m]);
                                                       $urlDATA = explode(',', $urlDATA[2]);
                                                       $category = explode('.', $urlDATA[4]);
                                                       $category = $category[0];
                                                       $provider = $urlDATA[3];
                                                       $brand = $urlDATA[0];
                                                       $model = $urlDATA[1];
                                                       $yearofbuild = $urlDATA[2];
                                                       $insUrlSQL = "INSERT INTO `urls`(`category`,`provider`,`brand`,`model`,`yearofbuild`,`url`) VALUES ('".$category."','".$provider."','".$brand."','".$model."','".$yearofbuild."','".$url."'); ";
                                                       mysql_query($insUrlSQL);
                                                  }   
                                             }
                                        }    
                                   }
                              }
                         }
                    } 
               }  
          }
     }
?>