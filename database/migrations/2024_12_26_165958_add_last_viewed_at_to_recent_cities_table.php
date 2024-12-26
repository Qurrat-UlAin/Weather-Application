<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration //anon class for encapsulation of migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {   //access the db...access a pre-exisiting table....table's name, ....anon function hich takes a single parameter called $table
        Schema::table('recent_cities', function (Blueprint $table) {
            $table->timestamp('last_viewed_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('recent_cities', function (Blueprint $table) {
            $table->dropColumn('last_viewed_at');
        });
    }
};
