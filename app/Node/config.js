var vConfig = {
    'REDIS_CHANNEL_DOWNLOAD_IMG' : 'SQUAREONE_GET_IMAGE',
    "REDIS_HOST" : "127.0.0.1" ,
    "REDIS_PUERTO" : 6379 ,
    "REDIS_DATABASE_ERP" : 0 ,
    "REDIS_VARIABLE_QUEUE" : "SQUAREONE:TECHTEST:QUEUE:IMG" ,    
    "IMAGE_DIRECTORY" : "public/img/product"
};

module.exports = function() {
  return vConfig;
};