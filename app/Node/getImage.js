var CONFIG = require('./config.js')();
var REQUEST = require("request");
var FS = require("fs");
var PATH = require('path');

var getImage = function (pRedis) {
    this._redis = pRedis;    
    this._working = false;
    this._imagePath  = PATH.dirname(require.main.filename).replace('app/Node','') + CONFIG.IMAGE_DIRECTORY + '/';
    // Go to work!
    console.log('++ Go to find out queue tasks!')
    this.getFromQueue();
};

getImage.prototype.process = function(pNewTask) {
    
    var vSelf = this;
    this._redis.conection().sadd(CONFIG.REDIS_VARIABLE_QUEUE,pNewTask,function process_sadd(pErr,pResult) {
        if(pErr) { 
            console.log('++ Error sadd message'); 
            console.log(pErr);
            console.log(pNewTask);
            return;
        }
        
        if(pResult) {
            console.log('++ New task!!!');
            // If queue is stopped, go to work!!
            if(!vSelf._working) {
                console.log('++ Go work!!!');
                vSelf.getFromQueue();
            }
            else {
                console.log('++ Wait, queue working!!!');
            }
            return;
        }
    });    

};

getImage.prototype.Error = function(pErr,pMessage) {
    this._working = false;
    console.log(pMessage); 
    console.log(pErr);
    return;
}

getImage.prototype.getFromQueue = function() {
    var vSelf = this;
    if(vSelf._working) {return false;}
    // Activate queue
    vSelf._working = true;
    this._redis.conection().spop(CONFIG.REDIS_VARIABLE_QUEUE,function getFromQueue_spop(pErr,pTask) {
        if(pErr) {             
            return vSelf.Error(pErr,'++ Error spop message');
        }
        if(pTask) {
            var vData = JSON.parse(pTask);
            if(vData) {
                var vName = vData.name;
                var vFullName = vSelf._imagePath + vName;

                // The image is new??
                FS.stat(vFullName, function(err, stats) {
                    if(err) {
                        console.log('++ New image to download!!');
                    
                        var vOptions = {
                            "followAllRedirects" : true,
                            "followOriginalHttpMethod" : false,
                            "rejectUnauthorized": false,
                            "gzip" : true,
                            "headers" : {
                                "Content-Type" : "application/x-www-form-urlencoded",
                                "Connection": "Keep-Alive" ,
                                "Keep-Alive": "timeout=10, max=1000",
                                "gzip" : true,                    
                            },                
                            "uri" : vData.url,
                            "encoding" : null,
                            "method" : "GET"
                        };

                        REQUEST(vOptions,function REQUEST_getImagefromURL(pErrorGet, pResponse, pImageRaw) {

                            if (!pErrorGet && pResponse.statusCode == 200) { 

                                FS.writeFile(vSelf._imagePath + vName, pImageRaw,function(pErr,pResult) {
                                    if(pErr) {
                                        console.log('++ Error saving image !! -> ' + pErr);
                                    }
                                    else {
                                        console.log('++ Image download successfully !!');
                                    }
                                    // Not important if fail or success, next cycle get it
                                    // OK, finish!! stop queue and call me again!
                                    vSelf._working = false;
                                    vSelf.getFromQueue();
                                    return true;                                    
                                });
                            }
                            else {
                                return vSelf.Error(pErrorGet,'++ Error get url: ' + vData.url);
                            }
                        });                    

                    }
                    else {
                        console.log('++ Old image!! skipping!!');                    
                        // OK, stop queue and call me again!
                        vSelf._working = false;
                        vSelf.getFromQueue();
                        return true;                                        
                    }
                });                

            }
            else {
                return vSelf.Error(pErr,'++ Error spop json invalid task?');
            }
        }
        else {
            // No more task? we are finish!! 
            console.log('++ Queue empty, task fisnish!');
            vSelf._working = false;
            return true;            
        }
    });    

};


module.exports = getImage;


