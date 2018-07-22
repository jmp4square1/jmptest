var CONFIG = require('./config.js')();
var REDIS = require('./myRedis.js');
var GETIMAGE = require('./getImage.js');

console.log('*** NODE SQUAREONE GETIMAGE QUEUE ***');

// I need two connection, one for listen and another for actions
var vRedisListen = new REDIS();
var vRedisAction = new REDIS();

vRedisAction.actionInit();
var vGetImage = new GETIMAGE(vRedisAction);

vRedisListen.listenInit(CONFIG.REDIS_CHANNEL_DOWNLOAD_IMG,vGetImage);
