<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_document_share_email_lists', function (Blueprint $table) {
            $table->id();
            $table->string('share_id')->comment('');
            $table->string('email');
            $table->timestamps();
            $table->foreign('share_id')->references('id')->on('voucher_document_share_email_links');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_document_share_email_lists');
    }
};