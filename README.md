# jmptest
Technical test from Javier Moral to Square1

<h2>Technical specifications:</h2>

<ul>
    <li>Apache2</li>
    <li>From LARAVEL 5.6 (https://laravel.com/docs/5.6#server-requirements)</li>
    <li>MySQL (mariadb)</li>
    <li>Redis server (optional but mandatory in fast_mode)</li>
    <li>NodeJS & NPM (optional but mandatory in fast_mode)</li>
    <li>PM2 (http://pm2.keymetrics.io/docs/usage/quick-start/) (optional but mandatory in fast_mode)</li>
</ul>

<h2>General Installation:</h2>

<ul>
    <li>Run <b>git clone https://github.com/jmp4square1/jmptest.git</b> in Apache root directory. The app url should be: http://localhost/jmptest/public</li>
    <li>Download dependencies run <b>composer install</b></li>
    <li>You need your application key to secure sessions, run <b>php artisan key:generate</b></li>
    <li>Change the mysql access credentials in your <b>.env</b> (the default database name is "appliances")</li>
    <li>You need create the application tables in MYSQL, run <b>php artisan migrate</b>.</li>
    <li>Be aware about permissions in <b>public/img</b> and <b>storage</b>, apache user need write inside, we avoid problems if you run <b>chmod 777 -R public/img && chmod 777 -R storage</b></li>
    <li>At this point, you should see the portal and navigate although the catalog is empty. Register as an user if you want.</li>
    <li>The general Laravel cron is needed, you must install the cron: <b>* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1</b></li>
    <li>At this moment, each minute (default configuration for a fast catalog feeding), the Laravel schedule task system will update the catalog, but if you don't want wait you can run manually as many times as you want: <b>php artisan catalog:update</b> (be aware with your user permissions again)</li>
</ul>

<h2>Documentation (a brief explanation about project):</h2>

<u><b>Configuration Files</b></u>: Always is a good practice extract operations values to a config file (or to database), the most important are: 
<ul>
<li><i>config/squareone.php</i>:
    <ul>
        <li>
            cron_catalog_update: The catalog update cron frequency written in "linux style" used in App\Console\Kernel.php
        </li>
        <li>
            catalog_n_products_x_page: Number of products per page showing in frontend.
        </li>
        <li>
            fast_mode: Enable/disable fast mode.
        </li>
    </ul>    
</li>
<li><i>config/crawler.php</i>:
    <ul>
        <li>
            timeOutSeconds: Timeout in seconds to abort a call to source url.
        </li>
        <li>
            appliances.base_targets_url: Specific of Appliances, array of urls to attack and extract the product data, if is needed another url, add here.
        </li>
        <li>
            appliances.url_x_hit: Specific of Appliances, the number of pages processed in each iteration (cron call)
        </li>
    </ul>    
</li>
<li><i>config/filesystems.php</i>:
    <ul>
        <li>
            disk.catalog: Our own file system to Laravel, endpoint: public/img/product
        </li>
    </ul>
</li>
</ul>

<u><b>Crawler's</b></u>: 
<ul>
    <li><i>Classes</i>: There are two classes in app/Libraries/Crawler, a Core and an inherit class specific for read the test urls proposal (appliances), why?, because in the future we want attack another site we only to inherit another class and adjust the read DOM.
        <ul>
            <li><i>CrawlerCore.php</i>: The main logic locate at <i>run()</i> (explained in next point). This is a abstract class with abstract methods, <i>findPaginationAndSeeds()</i> and <i>findProducsData</i>, the inherit class must define them for do the work.</li>
            <li><i>CrawlerAppliances.php</i>: Does the "hard work", analyzing the specific DOM of source server and normalize the product data.</li>        
        </ul>
    </li>    
    <li><i>How works?</i>: The operation is focus in avoid ban from source server, avoid mass request and traffic consume
    <ol>
        <li>In each iteration look in his seeds table (<i>nextSeed()</i>) where he reads the true urls to analyze</li>
        <li>If his seeds table is empty (fresh installation), go to read <i>appliances.base_targets_url</i> of <i>config/crawler.php</i> and get all future urls to read <i>feedFromBaseTargetsUrl()</i> calling to specific inherit class method <i>findPaginationAndSeeds</i>, feeds his seeds table calling <i>newSeeds()</i> and back to first step.</li>
        <li>If his seeds table has registers, gets one and reads the DOM <i>getUrlContent()</i> calling to specific inherit class method <i>findProducsData()</i> and saves all the products data (in this pagination url) with <i>newSeeds()</i>.
        </li>
    </ol>
    </li>
    <li><i>Storage</i>: All information retrieved, is storage in our local database, including the image product (in our server directory). The crawler is always working and read the same product again and again... so If any attribute change we get the update data, but image only is downloaded the first time, not always.
    </li>
</ul>

<h2>Activating Fast Mode:</h2>

<ul>
    <li><i>About:</i> Fast mode is a chance for show a portion of my technical knowledge about Redis and Node JS. Is optional because need a much complex deploying and i don't know if the test machine will support this.</li>
    <li><i>What does?:</i> The UX is invariant, the change is in background. Mainly does two important changes:
    <ol>
        <li>When the crawler feeds his seeds talks with Redis avoid mysql transactions.</li>
        <li>The hard work of download images is delegated to a queue, programmed in NODE with Redis support. PHP and NODE talks through a channel published by REDIS, PHP publish and NODE suscribe, inserting in the queue (list in REDIS) the request and consume one by one avoiding get ban.</li>
    </ol>        
    </li>
    <li><i>Technical requirements:</i> The app needs Redis, NodeJS and PM2, the other requeriments are installed in the main deploy when you run "composer install".</li>
    <li><i>NodeJS deploy:</i> The queue programmed in NodeJS is locate at <b>/app/Node</b>, you need download dependencies, go there and run <b>npm install</b></li>
    <li><i>PM2</i>: We need "something" to run the node queue in background, PM2 is a good choice. Run <b>npm install pm2@latest -g</b>, then we need demonize the queue.js, run <b>pm2 start queue.js</b> . Shutdown the queue.js run <b>pm2 stop queue</b> or delete it <b>pm2 delete queue</b></li>
    <li><i>How can I activate the fast mode?:</i> If all is installed (redis-node-pm2) you can enable or disable at any time as many times as you want, when enable "fast mode" the crawler talks with redis and nodejs, when disable it talks with mysql and download image in the same php process. Go to <b>config/squareone.php</b> and change the <b>fast_mode</b> variable.</li>
    <li><i>Can I take a look to queue works?:</i> Yes, due to test purposes, the queue is login his process with console.log, you can view with <b>pm2 log queue</b></li>
    <li><i><b>Remember</b>:</i> If REDIS server not has default values, you need change configurations, <b>config/database.php</b> to Laravel and </b>app/Node/config.js</b> to NodeJS.</li>
</ul>

<u><b>Favourites</b></u>: 

Only one consideration. If you share your wishlist with social buttons, the app generate a unique url link where an anonymous user can view your favourites.

<h2>That's all</h2>

<b>Thanks</b> for the chance :)
