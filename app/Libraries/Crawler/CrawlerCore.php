<?php

namespace App\Libraries\Crawler;

use Illuminate\Support\Facades\Log;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

use App\Models\CrawlerSeedsModel;
use App\Models\ProductModel;

/*
 * The Crawler works with an internal schedule.
 * A local source data of targets urls to attack (classical list).
 * If the list is empty, the crawler examine his $_baseTargetsURL and seeds again his internal schedule.
 * In each interaction, the crawler only attack "n" objectives avoiding get banned.
 */

abstract class CrawlerCore
{
    // The base targets url to get data
    protected $_baseTargetsURL = [];
    // Hits x hit
    protected $_urlXHit = 0;
    // Http client object
    private $_httpClient = null;
        
    public function __construct()
    {   
        // Init the http client
        $this->_httpClient = new Client();
        $this->_httpClient->setClient(new GuzzleClient([
            'timeout' => config('crawler.appliances.timeOutSeconds')
        ]));         
    }
        
    public function __destruct()
    {        
    }
    
    /*
     * Return the next url to analyze
     */
    public function nextSeed()
    {
        return CrawlerSeedsModel::nextSeed();
    }
    /*
     * Inserts new seeds
     */
    public function newSeeds($p_new_seeds)
    {
        return CrawlerSeedsModel::newSeeds($p_new_seeds);
    }    
    /*
     * Inserts or Update product data
     */
    public function updateData($v_products_data)
    {
        return ProductModel::updateData($v_products_data);
    }    
    
    /*
     * "Main" function, explained at top ;)
     */  
    public function run()
    {        
        // How many hits/bucle?
        $v_hits = $this->_urlXHit;        
        while($v_hits>0) {            
            // We have seeds?
            $v_seed = $this->nextSeed();            
            if($v_seed) {
                // Yes!?, try to get data, if fails nothing serius happens, the next cycle will do the job.
                $this->updateData( $this->getUrlContent($v_seed) );
            }
            else {
                // No more seeds? oh oh! we need feed again!
                $this->newSeeds( $this->feedFromBaseTargetsUrl() );
            }
            $v_hits--;
        }
        
        
    }
    
    // Extract products data of DOM
    public function getUrlContent($p_URL)
    {
        $v_products_data = [];
        try {                
            // Do get
            $v_crawler = $this->_httpClient->request('GET', $p_URL);
            // Extract data
            $v_products_data = $this->findProducsData($v_crawler);

        } catch (Exception $ex) {
            Log::warning('Exception CrawlerCore:getUrlContent -> ' . $ex->getMessage());
            set_error_handler('var_dump', 0);
            @trigger_error("");
            restore_error_handler();  
        }        
        return $v_products_data;
    }
        
    // Attack the base targets and gets seeds
    public function feedFromBaseTargetsUrl()
    {
        $v_seeds_to_feed = [];
        foreach($this->_baseTargetsURL as $v_url_base) {
            
            try {                
                // Do get
                $v_crawler = $this->_httpClient->request('GET', $v_url_base);
                // Extract number of pages and urls
                $v_seeds_to_feed = array_merge($v_seeds_to_feed , $this->findPaginationAndSeeds($v_url_base,$v_crawler) );
                
            } catch (Exception $ex) {
                Log::warning('Exception CrawlerCore:feedFromBaseTargetsUrl -> ' . $ex->getMessage());
                set_error_handler('var_dump', 0);
                @trigger_error("");
                restore_error_handler();  
            }
        }
        return $v_seeds_to_feed;
    }    
    
    // The inherit classes must define those methods.
    // Because, each site has his own DOM ...
    abstract public function findPaginationAndSeeds($p_url_base,$p_crawler);    
    abstract public function findProducsData($p_crawler);    
    
}