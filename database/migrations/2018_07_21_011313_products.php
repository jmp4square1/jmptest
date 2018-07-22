<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Products extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');            
            $table->string('url_detail',1000)->nullable();
            $table->string('url_img_base',1000)->nullable();
            $table->string('name',1000);
            $table->string('url_img_logo',1000)->nullable();
            $table->string('description',2000)->nullable();
            $table->decimal('price_previous', 8, 2)->nullable(); // 999,999.99
            $table->decimal('price_now', 8, 2); // 999,999.99
            $table->timestamp('created_at')->useCurrent();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();        
        Schema::dropIfExists('products');
        Schema::enableForeignKeyConstraints();        
    }
}
