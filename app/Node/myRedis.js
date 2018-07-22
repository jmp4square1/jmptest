var REDIS = require("redis");
var CONFIG = require('./config.js')();

var myRedis = function () {
    this._redisClient = false;
};

myRedis.prototype.actionInit = function() {
    this._redisClient = REDIS.createClient({
        "host" : CONFIG.REDIS_HOST ,
        "port" : CONFIG.REDIS_PUERTO ,
        "db" : CONFIG.REDIS_DATABASE_ERP
    });
    if(this._redisClient) {
        this._redisClient.select(CONFIG.REDIS_DATABASE_ERP,function libRedis_construct() {
            console.log('** Up REDIS Action on ' + CONFIG.REDIS_DATABASE_ERP + ' database.');
        });
        this._redisClient.on("error", function (pErr) {
            console.log('** REDIS Action Error');
            console.log(pErr);
        });    
    }
    else {
        console.log('** REDIS Action init error ');
        process.exit(1);
    }    
}

myRedis.prototype.listenInit = function(pChannel,pQueue) {

    this._redisClient = REDIS.createClient({
        "host" : CONFIG.REDIS_HOST ,
        "port" : CONFIG.REDIS_PUERTO ,
        "db" : CONFIG.REDIS_DATABASE_ERP
    });
    if(this._redisClient) {
        this._redisClient.select(CONFIG.REDIS_DATABASE_ERP,function libRedis_construct() {
            console.log('** Up REDIS Listen on ' + CONFIG.REDIS_DATABASE_ERP + ' database.');
        });
        this._redisClient.on("error", function (pErr) {
            console.log('** REDIS Listen Error');
            console.log(pErr);
        });    
    }
    else {
        console.log('** REDIS Listen init error ');
        process.exit(1);
    }
    
    // Avisamos de entidad escuchando el canal
    this._redisClient.on("subscribe", function (pChannel) {
        console.log('** Redis now listen on channel '+pChannel);
    });


    this._redisClient.subscribe(pChannel);
    
    var vQueue = pQueue;   
    
    this._redisClient.on("message", function (pCanal, pMessage) {   
        console.log('++ New message listened!!')
        vQueue["process"](pMessage);
    });    
};


myRedis.prototype.conection = function() {
    return this._redisClient;
};

module.exports = myRedis;


