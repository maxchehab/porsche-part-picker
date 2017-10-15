<?php

     require "vendor/autoload.php";
     use PHPHtmlParser\Dom;

     $query = urlencode($_POST["query"]);

     $response['results'] = array();

     /*Stoddard.com*/
     $dom = new Dom;
     $dom->loadFromUrl('http://www.stoddard.com/catalogsearch/result/?q=' . $query);
     $products = $dom->find('.category-products .item');
     foreach($products as $product){
          $result = array();
          $result['img'] = $product->find("img")[0]->getAttribute('src');
          $result['name'] = $product->find(".product-name a")[0]->text;
          $result['link'] = $product->find(".product-name a")[0]->getAttribute('href');

          $result['price'] = $product->find(".price-box .price")[0]->text;

          array_push($response['results'], $result);
     }

     /*Sierramadrecollection.com*/
     $dom = new Dom;
     $dom->loadFromUrl('http://www.sierramadrecollection.com/cart.php?m=search_results&search='. $query);
     $products = $dom->find('.grid__item');
     foreach($products as $product){
          $result = array();
          $result['img'] = $product->find("img")[0]->getAttribute('src');
          $result['name'] = $product->find(".item-name a")[0]->text;
          $result['link'] = $product->find(".item-name a")[0]->getAttribute('href');
          $result['price'] = $product->find(".item-price")[0]->text;
          if(strpos($result['price'], '$') == false){
               $result['price'] = $product->find(".salePrice")[0]->text;
          }

          array_push($response['results'], $result);
     }


     /*sunsetporscheparts.com*/
     $dom = new Dom;
     $dom->loadFromUrl('https://www.sunsetporscheparts.com/?year=&make=&model=&search_str=' . $query . '&p=catalog&mode=search&scat=&search_in=all');
     $products = $dom->find('.catalog-product');
     if(count($products) > 0){
          foreach($products as $product){
               $result = array();
               $result['img'] = $product->find(".catalog-product-image-wrapper img")[0]->getAttribute('src');
               $result['name'] = $product->find(".catalog-product-title a")[0]->text;
               $result['link'] = $product->find(".catalog-product-title a")[0]->getAttribute('href');

               if(count($product->find(".sale-price")) > 0){
                    $result['price'] = $product->find(".sale-price")[0]->text;
               }else{
                    $result['price'] = "N/A";
               }

               array_push($response['results'], $result);
          }
     }else{
          $result = array();
          $result['img'] = $dom->find('.product-images img')[0]->getAttribute('src');
          $result['name'] = $dom->find('h1.product-title')[0]->text;
          if(count($dom->find(".sale-price-amount")) > 0){
               $result['price'] = $dom->find(".sale-price-amount")[0]->text;
          }else{
               $result['price'] = "N/A";
          }

          $links = $dom->find('link');
          foreach($links as $link){
               if($link->getAttribute('rel') == 'canonical' &&  strpos($link->getAttribute('href'), 'https://www.sunsetporscheparts.com') !== false){
                    $result['link'] = $link->getAttribute('href');
                    break;
               }
          }
          array_push($response['results'], $result);
     }


     echo json_encode($response);

?>
