<?php 
     error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
     require_once('settings.php');
     require_once('simple_html_dom.php');
     ini_set("memory_limit", "8000M");
     ini_set("max_execution_time", "0");
     set_time_limit(0);
     $sql = "SELECT tmp.html, urls.* FROM tmp LEFT JOIN urls ON tmp.url_id = urls.id ";
     $resultURL = mysql_query($sql);
     if(mysql_num_rows($resultURL) > 0){
          while($row = mysql_fetch_assoc($resultURL)){
               $url = $row['url'];
               $url_id = $row['id'];            
               $html = str_get_html($row['html']);
               if($html != ""){
                    $name = explode(',', $url);
                    $name = str_replace("http://shop.jamparts.com/shop/", "", $name[0])." ".$name[1]." ".$name[2];
                    $name = str_replace("_", " ", $name);
                    $name = str_replace(" .", " (", $name);
                    $name = str_replace(". ", ") ", $name);
                    $name = "<h1>".$name."</h1>";
                    try {
                         if(isset($html->find('#centerDiv',0)->find('#midContainerDiv',0)->find('form',0)->find('.articles',0)->innertext)){
                              foreach($html->find('#centerDiv #midContainerDiv form .articles .article') as $div) {
                                   $id = $div->id;
                                   if(isset($div->find('.prices',0)->innertext) && isset($div->find('.prices',0)->find('.titan',0)->innertext)){
                                        $flag = false;
                                        foreach($div->find('.prices',0)->find('.titan',0)->find('img') as $img) {
                                             if($img->src == '/assets/images/lieferbarkeit_1.png'){
                                                  $flag = true;
                                             }
                                        }
                                        if($flag){
                                             $image = array();
                                             $pdf = array();
                                             $video = array();
                                             foreach($div->find('.image',0)->find('a') as $img) {
                                                  $image[] = $siteurl.$img->href;
                                             }
                                             try {
                                                  foreach($div->find('.text',0)->find('a') as $a) {
                                                       $tmp = $a->href;
                                                       $tmpAr = explode('/', $tmp);
                                                       if(isset($tmpAr[1]) && $tmpAr[1] == "pdf"){
                                                            if(!in_array($siteurl.$tmpAr[1].'/'.$tmpAr[2], $pdf)){
                                                                 $pdf[] = $siteurl.$tmpAr[1].'/'.$tmpAr[2];
                                                            }
                                                       }
                                                       if(isset($tmpAr[0]) && $tmpAr[0] == "http:"){
                                                            if(!in_array($tmp, $video)){
                                                                 $video[] = $tmp;
                                                            }
                                                       }
                                                  }
                                             } catch (Exception $e) {
                                                 echo 'Caught exception PDF AND VIDEO : ',  $e->getMessage(), "\n";
                                                 die();
                                             }
                                             
                                             try {
                                                  $h2 = $div->find('.text',0)->find('h2',0)->innertext;
                                                  $h2 = str_replace("/assets/", "http://shop.jamparts.com/assets/", $h2);
                                                  
                                                  $h2 = str_replace("ÃŸ","ß",$h2);
                                                  $h2 = str_replace("Ã¶","ö",$h2);
                                                  $h2 = str_replace("Ã¤","ä",$h2);
                                                  $h2 = str_replace("Ã¼","ü",$h2);
                                                  $h2 = str_replace("ä","&auml;",$h2);
                                                  $h2 = str_replace("ö","&ouml;",$h2);
                                                  $h2 = str_replace("ü","&uuml;",$h2);
                                                  $h2 = str_replace("ß","&szlig;",$h2);
                                                  $h2 = str_replace("Ä","&Auml;",$h2);
                                                  $h2 = str_replace("É","&Eacute;",$h2);
                                                  $h2 = str_replace("é","&eacute;",$h2);
                                                  $h2 = str_replace("Ö","&Ouml;",$h2);
                                                  $h2 = str_replace("Ü","&Uuml;",$h2);
                                                  $h2 = str_replace("«","&laquo;",$h2);
                                                  $h2 = str_replace("»","&raquo;",$h2);
                                                  $h2 = str_replace("„","&#132;",$h2);
                                                  $h2 = str_replace("“","&#147;",$h2);
                                                  $h2 = str_replace("”","&#148;",$h2);
                                                  $h2 = str_replace("°","&#176;",$h2);
                                                  $h2 = str_replace("€","&euro;",$h2);
                                                  $h2 = str_replace("£","&pound;",$h2);
                                                  $h2 = str_replace(" – "," - ",$h2);
                                                  $h2 = str_replace("´","`",$h2);
                                                  $h2 = str_replace("’","'",$h2);
                                                  
                                                  $h2 = "<h2>".$h2."</h2>";
                                                  $h2 = mysql_real_escape_string($h2);
                                             } catch (Exception $e) {
                                                 echo 'Caught exception H2 : ',  $e->getMessage(), "\n";
                                                 die();
                                             }
                                             
                                             try {
                                                  $div->find('.text',0)->find('h2',0)->outertext = '';
                                                  
                                                  $text = $div->find('.text',0)->innertext;
                                                  $text = str_replace("/assets/", "http://shop.jamparts.com/assets/", $text);
                                                  $text = str_replace("/pdf/", "http://shop.jamparts.com/pdf/", $text);
                                                  $text = str_replace("/pdfimages/", "http://shop.jamparts.com/pdfimages/", $text);
                                                  $text = str_replace("/Ersatzteile/", "http://shop.jamparts.com/Ersatzteile/", $text);
                                                  
                                                  $text = str_replace("ÃŸ","ß",$text);
                                                  $text = str_replace("Ã¶","ö",$text);
                                                  $text = str_replace("Ã¤","ä",$text);
                                                  $text = str_replace("Ã¼","ü",$text);
                                                  $text = str_replace("ä","&auml;",$text);
                                                  $text = str_replace("ö","&ouml;",$text);
                                                  $text = str_replace("ü","&uuml;",$text);
                                                  $text = str_replace("ß","&szlig;",$text);
                                                  $text = str_replace("Ä","&Auml;",$text);
                                                  $text = str_replace("É","&Eacute;",$text);
                                                  $text = str_replace("é","&eacute;",$text);
                                                  $text = str_replace("Ö","&Ouml;",$text);
                                                  $text = str_replace("Ü","&Uuml;",$text);
                                                  $text = str_replace("«","&laquo;",$text);
                                                  $text = str_replace("»","&raquo;",$text);
                                                  $text = str_replace("„","&#132;",$text);
                                                  $text = str_replace("“","&#147;",$text);
                                                  $text = str_replace("”","&#148;",$text);
                                                  $text = str_replace("°","&#176;",$text);
                                                  $text = str_replace("€","&euro;",$text);
                                                  $text = str_replace("£","&pound;",$text);
                                                  $text = str_replace(" – "," - ",$text);
                                                  $text = str_replace("´","`",$text);
                                                  $text = str_replace("’","'",$text);
                                                  $text = '<div class="text">'.$text."</div>";
                                                  
                                                  $text = mysql_real_escape_string($text);
                                             } catch (Exception $e) {
                                                 echo 'Caught exception TEXT : ',  $e->getMessage(), "\n";
                                                 die();
                                             }
                                             
                                             try {
                                                  $price = "";
                                                  $ek = "";
                                                  $color = "";
                                                  $artnr = "";
                                                  if(isset($div->find('.prices',0)->find('.titan',0)->innertext)){
                                                       if(isset($div->find('.prices',0)->find('.titan',0)->find('.price',0)->innertext)){
                                                            if(isset($div->find('.prices',0)->find('.titan',0)->find('.price',0)->find('span',0)->innertext)){
                                                                 $price = $div->find('.prices',0)->find('.titan',0)->find('.price',0)->find('span',0)->innertext;
                                                                 if(isset($div->find('.prices',0)->find('.titan',0)->find('.label',0)->innertext)){
                                                                      $color = $div->find('.prices',0)->find('.titan',0)->find('.label',0)->innertext;
                                                                 }
                                                                 if(isset($div->find('.prices',0)->find('.titan',0)->find('.artnr',0)->innertext)){
                                                                      $artnr = $div->find('.prices',0)->find('.titan',0)->find('.artnr',0)->innertext;
                                                                 }
                                                            } else {
                                                                 $price = $div->find('.prices',0)->find('.titan',0)->find('.price',0)->innertext;
                                                            }
                                                            if(isset($div->find('.prices',0)->find('.haendlerrabattgruppe',0)->innertext)){
                                                                 $ek = $div->find('.prices',0)->find('.haendlerrabattgruppe',0)->innertext;
                                                            }
                                                            if(isset($div->find('.prices',0)->find('.titan',0)->find('.label',0)->innertext)){
                                                                 $color = $div->find('.prices',0)->find('.titan',0)->find('.label',0)->innertext;
                                                            }
                                                            if(isset($div->find('.prices',0)->find('.titan',0)->find('.artnr',0)->innertext)){
                                                                 $artnr = $div->find('.prices',0)->find('.titan',0)->find('.artnr',0)->innertext;
                                                            }
                                                       } else {
                                                            $price = $div->find('.prices',0)->find('.price',0)->innertext;
                                                       }    
                                                  }
                                                  $price = mysql_real_escape_string($price);
                                                  $price = preg_replace("/[^0-9,]/", "", $price);
                                             } catch (Exception $e) {
                                                 echo 'Caught exception PRICES : ',  $e->getMessage(), "\n";
                                                 die();
                                             }
                                             $color = str_replace("<b>", "", $color);
                                             $color = str_replace("</b>", "", $color);
                                             
                                             $sql = "SELECT id FROM product WHERE artnr='".$artnr."' AND `url_id`='".$url_id."';";
                                             $result = mysql_query($sql);
                                             if(mysql_num_rows($result) == 0){
                                                  foreach($image as $imgSRC) {
                                                       $sql = "SELECT id FROM `media` WHERE `prod_id`='".$id."' AND `image`='".$imgSRC."';";
                                                       $result = mysql_query($sql);
                                                       if(mysql_num_rows($result) == 0){
                                                            $insSQL = "INSERT INTO `media`(`prod_id`,`image`,`media_type`) VALUES ('".$id."','".$imgSRC."','img'); ";
                                                            mysql_query($insSQL);
                                                       }
                                                  }
                                                  
                                                  foreach($pdf as $p) {
                                                       $sql = "SELECT id FROM `media` WHERE `prod_id`='".$id."' AND `image`='".$p."';";
                                                       $result = mysql_query($sql);
                                                       if(mysql_num_rows($result) == 0){
                                                            $insSQL = "INSERT INTO `media`(`prod_id`,`image`,`media_type`) VALUES ('".$id."','".$p."','pdf'); ";
                                                            mysql_query($insSQL);
                                                       }
                                                  }
                                                  
                                                  foreach($video as $v) {
                                                       $sql = "SELECT id FROM `media` WHERE `prod_id`='".$id."' AND `image`='".$v."';";
                                                       $result = mysql_query($sql);
                                                       if(mysql_num_rows($result) == 0){
                                                            $insSQL = "INSERT INTO `media`(`prod_id`,`image`,`media_type`) VALUES ('".$id."','".$v."','video'); ";
                                                            mysql_query($insSQL);
                                                       }
                                                  }
                                                  
                                                  $insSQL = "INSERT INTO `product`(`prod_id`,`url_id`,`h1`,`h2`,`description`,`price`,`ek`,`color`,`artnr`) VALUES ('".$id."','".$url_id."','".$name."','".$h2."','".$text."','".$price."','".$ek."','".$color."','".$artnr."'); ";
                                                  mysql_query($insSQL);
                                             }
                                        } 
                                   } else if(isset($div->find('.prices',0)->innertext)){
                                        $flag = false;
                                        foreach($div->find('.prices',0)->find('img') as $img) {
                                             if($img->src == '/assets/images/lieferbarkeit_1.png'){
                                                  $flag = true;
                                             }
                                        }
                                        if($flag){
                                             $image = array();
                                             $pdf = array();
                                             $video = array();
                                             foreach($div->find('.image',0)->find('a') as $img) {
                                                  $image[] = $siteurl.$img->href;
                                             }
                                             try {
                                                  foreach($div->find('.text',0)->find('a') as $a) {
                                                       $tmp = $a->href;
                                                       $tmpAr = explode('/', $tmp);
                                                       if(isset($tmpAr[1]) && $tmpAr[1] == "pdf"){
                                                            if(!in_array($siteurl.$tmpAr[1].'/'.$tmpAr[2], $pdf)){
                                                                 $pdf[] = $siteurl.$tmpAr[1].'/'.$tmpAr[2];
                                                            }
                                                       }
                                                       if(isset($tmpAr[0]) && $tmpAr[0] == "http:"){
                                                            if(!in_array($tmp, $video)){
                                                                 $video[] = $tmp;
                                                            }
                                                       }
                                                  }
                                             } catch (Exception $e) {
                                                 echo 'Caught exception PDF AND VIDEO : ',  $e->getMessage(), "\n";
                                                 die();
                                             }
                                             
                                             try {
                                                  if(isset($div->find('.text',0)->find('h2',0)->innertext)){
                                                       $h2 = $div->find('.text',0)->find('h2',0)->innertext;
                                                       $h2 = str_replace("/assets/", "http://shop.jamparts.com/assets/", $h2);
                                                       
                                                       $h2 = str_replace("ÃŸ","ß",$h2);
                                                       $h2 = str_replace("Ã¶","ö",$h2);
                                                       $h2 = str_replace("Ã¤","ä",$h2);
                                                       $h2 = str_replace("Ã¼","ü",$h2);
                                                       $h2 = str_replace("ä","&auml;",$h2);
                                                       $h2 = str_replace("ö","&ouml;",$h2);
                                                       $h2 = str_replace("ü","&uuml;",$h2);
                                                       $h2 = str_replace("ß","&szlig;",$h2);
                                                       $h2 = str_replace("Ä","&Auml;",$h2);
                                                       $h2 = str_replace("É","&Eacute;",$h2);
                                                       $h2 = str_replace("é","&eacute;",$h2);
                                                       $h2 = str_replace("Ö","&Ouml;",$h2);
                                                       $h2 = str_replace("Ü","&Uuml;",$h2);
                                                       $h2 = str_replace("«","&laquo;",$h2);
                                                       $h2 = str_replace("»","&raquo;",$h2);
                                                       $h2 = str_replace("„","&#132;",$h2);
                                                       $h2 = str_replace("“","&#147;",$h2);
                                                       $h2 = str_replace("”","&#148;",$h2);
                                                       $h2 = str_replace("°","&#176;",$h2);
                                                       $h2 = str_replace("€","&euro;",$h2);
                                                       $h2 = str_replace("£","&pound;",$h2);
                                                       $h2 = str_replace(" – "," - ",$h2);
                                                       $h2 = str_replace("´","`",$h2);
                                                       $h2 = str_replace("’","'",$h2);
                                                       
                                                       $h2 = "<h2>".$h2."</h2>";
                                                       $h2 = mysql_real_escape_string($h2);
                                                       $div->find('.text',0)->find('h2',0)->outertext = '';
                                                  } else {
                                                       $h2 = "<h2></h2>";
                                                  }
                                             } catch (Exception $e) {
                                                 echo 'Caught exception H2 : ',  $e->getMessage(), "\n";
                                                 die();
                                             }
                                             
                                             try {
                                                  $text = $div->find('.text',0)->innertext;
                                                  $text = str_replace("/assets/", "http://shop.jamparts.com/assets/", $text);
                                                  $text = str_replace("/pdf/", "http://shop.jamparts.com/pdf/", $text);
                                                  $text = str_replace("/pdfimages/", "http://shop.jamparts.com/pdfimages/", $text);
                                                  $text = str_replace("/Ersatzteile/", "http://shop.jamparts.com/Ersatzteile/", $text);
                                                  
                                                  $text = str_replace("ÃŸ","ß",$text);
                                                  $text = str_replace("Ã¶","ö",$text);
                                                  $text = str_replace("Ã¤","ä",$text);
                                                  $text = str_replace("Ã¼","ü",$text);
                                                  $text = str_replace("ä","&auml;",$text);
                                                  $text = str_replace("ö","&ouml;",$text);
                                                  $text = str_replace("ü","&uuml;",$text);
                                                  $text = str_replace("ß","&szlig;",$text);
                                                  $text = str_replace("Ä","&Auml;",$text);
                                                  $text = str_replace("É","&Eacute;",$text);
                                                  $text = str_replace("é","&eacute;",$text);
                                                  $text = str_replace("Ö","&Ouml;",$text);
                                                  $text = str_replace("Ü","&Uuml;",$text);
                                                  $text = str_replace("«","&laquo;",$text);
                                                  $text = str_replace("»","&raquo;",$text);
                                                  $text = str_replace("„","&#132;",$text);
                                                  $text = str_replace("“","&#147;",$text);
                                                  $text = str_replace("”","&#148;",$text);
                                                  $text = str_replace("°","&#176;",$text);
                                                  $text = str_replace("€","&euro;",$text);
                                                  $text = str_replace("£","&pound;",$text);
                                                  $text = str_replace(" – "," - ",$text);
                                                  $text = str_replace("´","`",$text);
                                                  $text = str_replace("’","'",$text);
                                                  $text = '<div class="text">'.$text."</div>";
                                                  
                                                  $text = mysql_real_escape_string($text);
                                             } catch (Exception $e) {
                                                 echo 'Caught exception TEXT : ',  $e->getMessage(), "\n";
                                                 die();
                                             }
                                             
                                             try {
                                                  $price = "";
                                                  $ek = "";
                                                  $color = "";
                                                  $artnr = "";
                                                  if(isset($div->find('.prices',0)->find('.titan',0)->innertext)){
                                                       if(isset($div->find('.prices',0)->find('.titan',0)->find('.price',0)->innertext)){
                                                            if(isset($div->find('.prices',0)->find('.titan',0)->find('.price',0)->find('span',0)->innertext)){
                                                                 $price = $div->find('.prices',0)->find('.titan',0)->find('.price',0)->find('span',0)->innertext;
                                                                 if(isset($div->find('.prices',0)->find('.titan',0)->find('.label',0)->innertext)){
                                                                      $color = $div->find('.prices',0)->find('.titan',0)->find('.label',0)->innertext;
                                                                 }
                                                                 if(isset($div->find('.prices',0)->find('.titan',0)->find('.artnr',0)->innertext)){
                                                                      $artnr = $div->find('.prices',0)->find('.titan',0)->find('.artnr',0)->innertext;
                                                                 }
                                                            } else {
                                                                 $price = $div->find('.prices',0)->find('.titan',0)->find('.price',0)->innertext;
                                                            }
                                                            if(isset($div->find('.prices',0)->find('.haendlerrabattgruppe',0)->innertext)){
                                                                 $ek = $div->find('.prices',0)->find('.haendlerrabattgruppe',0)->innertext;
                                                            }
                                                            if(isset($div->find('.prices',0)->find('.titan',0)->find('.label',0)->innertext)){
                                                                 $color = $div->find('.prices',0)->find('.titan',0)->find('.label',0)->innertext;
                                                            }
                                                            if(isset($div->find('.prices',0)->find('.titan',0)->find('.artnr',0)->innertext)){
                                                                 $artnr = $div->find('.prices',0)->find('.titan',0)->find('.artnr',0)->innertext;
                                                            }
                                                       } else {
                                                            $price = $div->find('.prices',0)->find('.price',0)->innertext;
                                                       }    
                                                  } else {
                                                       if(isset($div->find('.prices',0)->find('.price',0)->find('span',0)->innertext)){
                                                            $price = $div->find('.prices',0)->find('.price',0)->find('span',0)->innertext;
                                                       } else {
                                                            $price = $div->find('.prices',0)->find('.price',0)->innertext;
                                                       }
                                                       if(isset($div->find('.prices',0)->find('.carbon',0)->find('.artnr',0)->innertext)){
                                                            $artnr = $div->find('.prices',0)->find('.carbon',0)->find('.artnr',0)->innertext;
                                                       } else if(isset($div->find('.prices',0)->find('.carbon',0)->find('.artnr2',0)->innertext)){
                                                            $artnr = $div->find('.prices',0)->find('.carbon',0)->find('.artnr2',0)->innertext;
                                                       }
                                                       if(isset($div->find('.prices',0)->find('.haendlerrabattgruppe',0)->innertext)){
                                                            $ek = $div->find('.prices',0)->find('.haendlerrabattgruppe',0)->innertext;    
                                                       }
                                                       if(isset($div->find('.prices',0)->find('.carbon',0)->find('.label',0)->innertext)){
                                                            $color = $div->find('.prices',0)->find('.carbon',0)->find('.label',0)->innertext;
                                                       }
                                                  }
                                                  $price = mysql_real_escape_string($price);
                                                  $price = preg_replace("/[^0-9,]/", "", $price);
                                             } catch (Exception $e) {
                                                 echo 'Caught exception PRICES : ',  $e->getMessage(), "\n";
                                                 die();
                                             }
                                             $color = str_replace("<b>", "", $color);
                                             $color = str_replace("</b>", "", $color);
                                             
                                             $sql = "SELECT id FROM product WHERE artnr='".$artnr."' AND `url_id`='".$url_id."';";
                                             $result = mysql_query($sql);
                                             if(mysql_num_rows($result) == 0){
                                                  foreach($image as $imgSRC) {
                                                       $sql = "SELECT id FROM `media` WHERE `prod_id`='".$id."' AND `image`='".$imgSRC."';";
                                                       $result = mysql_query($sql);
                                                       if(mysql_num_rows($result) == 0){
                                                            $insSQL = "INSERT INTO `media`(`prod_id`,`image`,`media_type`) VALUES ('".$id."','".$imgSRC."','img'); ";
                                                            mysql_query($insSQL);
                                                       }
                                                  }
                                                  
                                                  foreach($pdf as $p) {
                                                       $sql = "SELECT id FROM `media` WHERE `prod_id`='".$id."' AND `image`='".$p."';";
                                                       $result = mysql_query($sql);
                                                       if(mysql_num_rows($result) == 0){
                                                            $insSQL = "INSERT INTO `media`(`prod_id`,`image`,`media_type`) VALUES ('".$id."','".$p."','pdf'); ";
                                                            mysql_query($insSQL);
                                                       }
                                                  }
                                                  
                                                  foreach($video as $v) {
                                                       $sql = "SELECT id FROM `media` WHERE `prod_id`='".$id."' AND `image`='".$v."';";
                                                       $result = mysql_query($sql);
                                                       if(mysql_num_rows($result) == 0){
                                                            $insSQL = "INSERT INTO `media`(`prod_id`,`image`,`media_type`) VALUES ('".$id."','".$v."','video'); ";
                                                            mysql_query($insSQL);
                                                       }
                                                  }
                                                  
                                                  $insSQL = "INSERT INTO `product`(`prod_id`,`url_id`,`h1`,`h2`,`description`,`price`,`ek`,`color`,`artnr`) VALUES ('".$id."','".$url_id."','".$name."','".$h2."','".$text."','".$price."','".$ek."','".$color."','".$artnr."'); ";
                                                  mysql_query($insSQL);
                                             }
                                        } 
                                   }
                              }
                         }
                    } catch (Exception $e) {
                        echo 'Caught exception URL : ',  $e->getMessage(), "\n";
                        die();
                    }
                    
                    try {
                         if(isset($html->find('#centerDiv',0)->find('#midContainerDiv',0)->find('form',0)->find('.articles',0)->innertext)){
                              foreach($html->find('#centerDiv #midContainerDiv form .articles .article') as $div) {
                                   $id = $div->id;
                                   if(isset($div->find('.prices',0)->innertext) && isset($div->find('.prices',0)->find('.carbon',0)->innertext)){
                                        $flag = false;
                                        foreach($div->find('.prices',0)->find('.carbon',0)->find('img') as $img) {
                                             if($img->src == '/assets/images/lieferbarkeit_1.png'){
                                                  $flag = true;
                                             }
                                        }
                                        if($flag){
                                             $image = array();
                                             $pdf = array();
                                             $video = array();
                                             foreach($div->find('.image',0)->find('a') as $img) {
                                                  $image[] = $siteurl.$img->href;
                                             }
                                             try {
                                                  foreach($div->find('.text',0)->find('a') as $a) {
                                                       $tmp = $a->href;
                                                       $tmpAr = explode('/', $tmp);
                                                       if(isset($tmpAr[1]) && $tmpAr[1] == "pdf"){
                                                            if(!in_array($siteurl.$tmpAr[1].'/'.$tmpAr[2], $pdf)){
                                                                 $pdf[] = $siteurl.$tmpAr[1].'/'.$tmpAr[2];
                                                            }
                                                       }
                                                       if(isset($tmpAr[0]) && $tmpAr[0] == "http:"){
                                                            if(!in_array($tmp, $video)){
                                                                 $video[] = $tmp;
                                                            }
                                                       }
                                                  }
                                             } catch (Exception $e) {
                                                 echo 'Caught exception PDF AND VIDEO : ',  $e->getMessage(), "\n";
                                                 die();
                                             }
                                             
                                             try {
                                                  $h2 = $div->find('.text',0)->find('h2',0)->innertext;
                                                  $h2 = str_replace("/assets/", "http://shop.jamparts.com/assets/", $h2);
                                                  
                                                  $h2 = str_replace("ÃŸ","ß",$h2);
                                                  $h2 = str_replace("Ã¶","ö",$h2);
                                                  $h2 = str_replace("Ã¤","ä",$h2);
                                                  $h2 = str_replace("Ã¼","ü",$h2);
                                                  $h2 = str_replace("ä","&auml;",$h2);
                                                  $h2 = str_replace("ö","&ouml;",$h2);
                                                  $h2 = str_replace("ü","&uuml;",$h2);
                                                  $h2 = str_replace("ß","&szlig;",$h2);
                                                  $h2 = str_replace("Ä","&Auml;",$h2);
                                                  $h2 = str_replace("É","&Eacute;",$h2);
                                                  $h2 = str_replace("é","&eacute;",$h2);
                                                  $h2 = str_replace("Ö","&Ouml;",$h2);
                                                  $h2 = str_replace("Ü","&Uuml;",$h2);
                                                  $h2 = str_replace("«","&laquo;",$h2);
                                                  $h2 = str_replace("»","&raquo;",$h2);
                                                  $h2 = str_replace("„","&#132;",$h2);
                                                  $h2 = str_replace("“","&#147;",$h2);
                                                  $h2 = str_replace("”","&#148;",$h2);
                                                  $h2 = str_replace("°","&#176;",$h2);
                                                  $h2 = str_replace("€","&euro;",$h2);
                                                  $h2 = str_replace("£","&pound;",$h2);
                                                  $h2 = str_replace(" – "," - ",$h2);
                                                  $h2 = str_replace("´","`",$h2);
                                                  $h2 = str_replace("’","'",$h2);
                                                  
                                                  $h2 = "<h2>".$h2."</h2>";
                                                  $h2 = mysql_real_escape_string($h2);
                                             } catch (Exception $e) {
                                                 echo 'Caught exception H2 : ',  $e->getMessage(), "\n";
                                                 die();
                                             }
                                             
                                             try {
                                                  $div->find('.text',0)->find('h2',0)->outertext = '';
                                                  
                                                  $text = $div->find('.text',0)->innertext;
                                                  $text = str_replace("/assets/", "http://shop.jamparts.com/assets/", $text);
                                                  $text = str_replace("/pdf/", "http://shop.jamparts.com/pdf/", $text);
                                                  $text = str_replace("/pdfimages/", "http://shop.jamparts.com/pdfimages/", $text);
                                                  $text = str_replace("/Ersatzteile/", "http://shop.jamparts.com/Ersatzteile/", $text);
                                                  
                                                  $text = str_replace("ÃŸ","ß",$text);
                                                  $text = str_replace("Ã¶","ö",$text);
                                                  $text = str_replace("Ã¤","ä",$text);
                                                  $text = str_replace("Ã¼","ü",$text);
                                                  $text = str_replace("ä","&auml;",$text);
                                                  $text = str_replace("ö","&ouml;",$text);
                                                  $text = str_replace("ü","&uuml;",$text);
                                                  $text = str_replace("ß","&szlig;",$text);
                                                  $text = str_replace("Ä","&Auml;",$text);
                                                  $text = str_replace("É","&Eacute;",$text);
                                                  $text = str_replace("é","&eacute;",$text);
                                                  $text = str_replace("Ö","&Ouml;",$text);
                                                  $text = str_replace("Ü","&Uuml;",$text);
                                                  $text = str_replace("«","&laquo;",$text);
                                                  $text = str_replace("»","&raquo;",$text);
                                                  $text = str_replace("„","&#132;",$text);
                                                  $text = str_replace("“","&#147;",$text);
                                                  $text = str_replace("”","&#148;",$text);
                                                  $text = str_replace("°","&#176;",$text);
                                                  $text = str_replace("€","&euro;",$text);
                                                  $text = str_replace("£","&pound;",$text);
                                                  $text = str_replace(" – "," - ",$text);
                                                  $text = str_replace("´","`",$text);
                                                  $text = str_replace("’","'",$text);
                                                  $text = '<div class="text">'.$text."</div>";
                                                  
                                                  $text = mysql_real_escape_string($text);
                                             } catch (Exception $e) {
                                                 echo 'Caught exception TEXT : ',  $e->getMessage(), "\n";
                                                 die();
                                             }
                                             
                                             try {
                                                  $price = "";
                                                  $ek = "";
                                                  $color = "";
                                                  $artnr = "";
                                                  if(isset($div->find('.prices',0)->find('.carbon',0)->innertext)){
                                                       if(isset($div->find('.prices',0)->find('.carbon',0)->find('.price',0)->innertext)){
                                                            if(isset($div->find('.prices',0)->find('.carbon',0)->find('.price',0)->find('span',0)->innertext)){
                                                                 $price = $div->find('.prices',0)->find('.carbon',0)->find('.price',0)->find('span',0)->innertext;
                                                                 if(isset($div->find('.prices',0)->find('.carbon',0)->find('.label',0)->innertext)){
                                                                      $color = $div->find('.prices',0)->find('.carbon',0)->find('.label',0)->innertext;
                                                                 }
                                                                 if(isset($div->find('.prices',0)->find('.carbon',0)->find('.artnr',0)->innertext)){
                                                                      $artnr = $div->find('.prices',0)->find('.carbon',0)->find('.artnr',0)->innertext;
                                                                 }
                                                            } else {
                                                                 $price = $div->find('.prices',0)->find('.carbon',0)->find('.price',0)->innertext;
                                                            }
                                                            if(isset($div->find('.prices',0)->find('.haendlerrabattgruppe',0)->innertext)){
                                                                 $ek = $div->find('.prices',0)->find('.haendlerrabattgruppe',0)->innertext;
                                                            }
                                                            if(isset($div->find('.prices',0)->find('.carbon',0)->find('.label',0)->innertext)){
                                                                 $color = $div->find('.prices',0)->find('.carbon',0)->find('.label',0)->innertext;
                                                            }
                                                            if(isset($div->find('.prices',0)->find('.carbon',0)->find('.artnr',0)->innertext)){
                                                                 $artnr = $div->find('.prices',0)->find('.carbon',0)->find('.artnr',0)->innertext;
                                                            } else if(isset($div->find('.prices',0)->find('.carbon',0)->find('.artnr2',0)->innertext)){
                                                                 $artnr = $div->find('.prices',0)->find('.carbon',0)->find('.artnr2',0)->innertext;
                                                            }
                                                       } else {
                                                            $price = $div->find('.prices',0)->find('.price',0)->innertext;
                                                       }    
                                                  }
                                                  $price = mysql_real_escape_string($price);
                                                  $price = preg_replace("/[^0-9,]/", "", $price);
                                             } catch (Exception $e) {
                                                 echo 'Caught exception PRICES : ',  $e->getMessage(), "\n";
                                                 die();
                                             }
                                             $color = str_replace("<b>", "", $color);
                                             $color = str_replace("</b>", "", $color);
                                             
                                             $sql = "SELECT id FROM product WHERE artnr='".$artnr."' AND `url_id`='".$url_id."';";
                                             $result = mysql_query($sql);
                                             if(mysql_num_rows($result) == 0){
                                                  foreach($image as $imgSRC) {
                                                       $sql = "SELECT id FROM `media` WHERE `prod_id`='".$id."' AND `image`='".$imgSRC."';";
                                                       $result = mysql_query($sql);
                                                       if(mysql_num_rows($result) == 0){
                                                            $insSQL = "INSERT INTO `media`(`prod_id`,`image`,`media_type`) VALUES ('".$id."','".$imgSRC."','img'); ";
                                                            mysql_query($insSQL);
                                                       }
                                                  }
                                                  
                                                  foreach($pdf as $p) {
                                                       $sql = "SELECT id FROM `media` WHERE `prod_id`='".$id."' AND `image`='".$p."';";
                                                       $result = mysql_query($sql);
                                                       if(mysql_num_rows($result) == 0){
                                                            $insSQL = "INSERT INTO `media`(`prod_id`,`image`,`media_type`) VALUES ('".$id."','".$p."','pdf'); ";
                                                            mysql_query($insSQL);
                                                       }
                                                  }
                                                  
                                                  foreach($video as $v) {
                                                       $sql = "SELECT id FROM `media` WHERE `prod_id`='".$id."' AND `image`='".$v."';";
                                                       $result = mysql_query($sql);
                                                       if(mysql_num_rows($result) == 0){
                                                            $insSQL = "INSERT INTO `media`(`prod_id`,`image`,`media_type`) VALUES ('".$id."','".$v."','video'); ";
                                                            mysql_query($insSQL);
                                                       }
                                                  }
                                                  
                                                  $insSQL = "INSERT INTO `product`(`prod_id`,`url_id`,`h1`,`h2`,`description`,`price`,`ek`,`color`,`artnr`) VALUES ('".$id."','".$url_id."','".$name."','".$h2."','".$text."','".$price."','".$ek."','".$color."','".$artnr."'); ";
                                                  mysql_query($insSQL);
                                             }
                                        } 
                                   }
                              }
                         }
                    } catch (Exception $e) {
                        echo 'Caught exception URL : ',  $e->getMessage(), "\n";
                        die();
                    }
               }
          }     
     }
?>