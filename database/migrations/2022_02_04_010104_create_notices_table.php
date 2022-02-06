<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->longText('body');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('user_id')->default(0);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('notice_categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notices');
    }
}
