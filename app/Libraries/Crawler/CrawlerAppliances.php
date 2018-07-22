<?php

namespace App\Libraries\Crawler;

class CrawlerAppliances extends CrawlerCore
{
    public function __construct()
    {
        // Initialization values by config
        $this->_baseTargetsURL = config('crawler.appliances.base_targets_url');
        $this->_urlXHit= config('crawler.appliances.url_x_hit');        
        
        parent::__construct();        
    }
        
    public function __destruct()
    {
        parent::__destruct();
    }
    
    /*  
     * Find out pagination links and create the url product seed
     */
    public function findPaginationAndSeeds($p_url_base,$p_crawler)
    {
        $v_seeds_to_feed = [];        
        $v_total_pages = 0;
        
        try {             

            // Go to last pagination link (if exists!!)
            $v_last_link = $p_crawler->filter('p.result-list-pagination > a')->last();
            if($v_last_link && $v_last_link->count()) {
                $v_href = $v_last_link->attr('href');
                // And extract the numbers of pages
                $v_results = [];
                preg_match('/&page=([0-9]+)/',$v_href, $v_results);
                if(isset($v_results[1])) {
                    $v_total_pages = $v_results[1];
                }
            }

        } catch (Exception $ex) {
            Log::warning('Exception CrawlerAppliances:findURLPagination -> ' . $ex->getMessage());
            set_error_handler('var_dump', 0);
            @trigger_error("");
            restore_error_handler();  
        }        

        for($v_page=0;$v_page<=$v_total_pages;$v_page++) {
            $v_seeds_to_feed[] = [
                'url' => $p_url_base . '?sort=price_asc&page=' .$v_page
            ];
        }
        
        return $v_seeds_to_feed;
    }
    
    /*
     * This is the hard job.
     * Each site has his own DOM and get product information task is specific of each page.
     * For that reason, each inherit class have his own analyzer method page .
     */
    public function findProducsData($p_crawler)
    {
        try {             

            // Go to last pagination link (if exists!!)
            $v_products_data = $p_crawler->filter('div.search-results-product')->each(function ($p_node) {
                
                $v_product_data = [
                    'url_detail' => null,
                    'url_img_base' => null,
                    'url_img_logo' => null,
                    'name' => '',
                    'description' => null,
                    'price_previous' => null,
                    'price_now' => 0.0
                ];
                
                // URL detail page
                $v_detail_url_link = $p_node->filter('div.product-image > a')->first();
                if($v_detail_url_link && $v_detail_url_link->count()) {
                    $v_product_data['url_detail'] = $v_detail_url_link->attr('href');
                }
                
                // URL image
                $v_img_obj = $p_node->filter('div.product-image')->first()->filter('img.img-responsive')->first();
                if($v_img_obj && $v_img_obj->count()) {
                    $v_product_data['url_img_base'] = $v_img_obj->attr('src');
                }
                
                // Product name
                $v_name = $p_node->filter('div.product-description')->first()->filter('h4 > a')->first();
                if($v_name && $v_name->count()) {
                    $v_product_data['name'] = trim($v_name->text());
                }
                else {
                    $v_product_data['name'] = 'Unknown product - ' . microtime();
                }
                
                // Logo
                $v_logo = $p_node->filter('div.product-description')->first()->filter('img.article-brand')->first();
                if($v_logo && $v_logo->count()) {
                    $v_product_data['url_img_logo'] = $v_logo->attr('src');
                }                 
                
                // Description
                $v_product_data['description'] = [];
                $v_description = $p_node->filter('div.product-description')->first()->filter('ul.result-list-item-desc-list li')->each(function ($p_node_des) {
                    return trim($p_node_des->text());
                });
                $v_product_data['description'] = json_encode($v_description);
                
                // Prices
                $v_price_previus = $p_node->filter('div.product-description')->first()->filter('.price-previous')->first();
                if($v_price_previus && $v_price_previus->count()) {
                    $v_product_data['price_previous'] = h_normalizePriceFromHTML($v_price_previus->text());
                }                  
                $v_price_now = $p_node->filter('div.product-description')->first()->filter('.section-title')->first();
                if($v_price_now && $v_price_now->count()) {
                    $v_product_data['price_now'] = h_normalizePriceFromHTML($v_price_now->text());
                }  
                
                // Here we get the image file to save in our server.
                // Make image links to target server is dangerous (hotlinking)
                // In a massive traffic scenario we can consume a lot of megas from the source server
                // And we can get ban, a lot of bytes or hits must be avoid. 
                // Now, we must schedule this task, usually this job gets much resources (time and memory), 
                // This is possible when the fast_mode is activated :)                
                $v_image_name = str_slug($v_product_data['name']);                
                h_getImageAndSave($v_product_data['url_img_base'],$v_image_name,'base');
                h_getImageAndSave($v_product_data['url_img_logo'],$v_image_name,'logo');
                
                return $v_product_data;
                        
            });
                    

        } catch (Exception $ex) {
            Log::warning('Exception CrawlerAppliances:findProducsData -> ' . $ex->getMessage());
            set_error_handler('var_dump', 0);
            @trigger_error("");
            restore_error_handler();  
        }        

        
        return $v_products_data;
    }    
    
    
}