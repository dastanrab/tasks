<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {


        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->text('message')->nullable()->comment('Error message');
            $table->integer('status_code')->nullable()->comment('HTTP status code');
            $table->string('exception')->nullable()->comment('Type of error (exception name)');
            $table->integer('priority')->nullable()->comment('Priority of the error');
            $table->string('url')->nullable()->comment('URL where the error occurred');
            $table->string('created_at_jalali')->nullable();
            $table->tinyInteger('seen_status')->default(\App\Models\ErrorLog::status_unseen)->comment("0 => unseen, 1 => seen");
            $table->unsignedBigInteger('user_id_logged')->nullable()->comment('User who logged the error');
            $table->text('extra_data')->nullable()->comment('Additional data set by the developer');
            $table->text('requests')->nullable()->comment('Data sent by the user');
            $table->text('headers')->nullable()->comment('User headers information');
            $table->string('user_agent')->nullable()->comment('User agent information');
            $table->text('stack_trace')->nullable()->comment('Location of the error');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }

};
