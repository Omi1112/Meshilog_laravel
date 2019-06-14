<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follows', function (Blueprint $table) {

          $table->unsignedBigInteger('user_id');
          $table->unsignedBigInteger('follow_id');
          $table->timestamps();

          // 制約定義
          $table->primary(['user_id', 'follow_id']);

          $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

          $table->foreign('follow_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('follows');
    }
}
