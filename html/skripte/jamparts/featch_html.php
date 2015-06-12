<?php 
     error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
     require_once('settings.php');
     require_once('simple_html_dom.php');
     ini_set("memory_limit", "8000M");
     ini_set("max_execution_time", "0");
     set_time_limit(0);
     $sql = "SELECT * FROM urls";
     $resultURL = mysql_query($sql);
     if(mysql_num_rows($resultURL) > 0){
          while($row = mysql_fetch_assoc($resultURL)){
               $url = $row['url'];
               $url_id = $row['id'];
               /*$curl = curl_init();
               curl_setopt($curl, CURLOPT_URL, $url);  
               curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
               curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
               $str = curl_exec($curl);
               curl_close($curl);
               $html = str_get_html($str);
               $html = mysql_real_escape_string($html);*/
               $html = mysql_real_escape_string(@file_get_contents($url));
               $sql = "SELECT id FROM tmp WHERE url_id='".$url_id."';";
               $result = mysql_query($sql);
               if(mysql_num_rows($result) == 0){
                    $insSQL = "INSERT INTO `tmp`(`url_id`,`html`) VALUES ('".$url_id."','".$html."'); ";
                    mysql_query($insSQL);
               } else {
                    $insSQL = "UPDATE `tmp` SET `html`='".$html."' WHERE `url_id`='".$url_id."'; ";
                    mysql_query($insSQL);
               }
          }
     }
?>