<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->uuid('id')->primary;
            $table->string('name', 30)->nullable()->comment('姓名');
            $table->string('password')->nullable()->comment('密碼');
            $table->string('email', 255)->unique()->nullable()->comment('Email');
            $table->unsignedTinyInteger('status')->default(1)->comment('會員狀態-0:刪除,1:正常');
            $table->unsignedInteger('created_year')->default(2022);
            $table->unsignedTinyInteger('created_month')->default(1);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });

        Schema::create('books', function (Blueprint $table) {
            $table->uuid('id')->primary;
            $table->uuid('member_id');
            $table->string('title', 60)->nullable()->comment('title');
            $table->string('author', 30)->nullable()->comment('author');
            $table->string('category', 30)->nullable()->comment('Email');
            $table->date('publication_date')->nullable();
            $table->integer('price')->default(0);
            $table->integer('quantity')->default(0);
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態-0:刪除,1:正常');
            $table->unsignedInteger('created_year')->default(2022);
            $table->unsignedTinyInteger('created_month')->default(1);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });

        Schema::create('book_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('book_id');
            $table->string('name', 30)->nullable();
            $table->text('path', 30)->nullable();
            $table->dateTime('created_at')->nullable();
        });

        Schema::create('book_edit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('member_id');
            $table->uuid('book_id');
            $table->string('title', 60)->nullable()->comment('title');
            $table->string('author', 30)->nullable()->comment('author');
            $table->string('category', 30)->nullable()->comment('Email');
            $table->date('publication_date')->nullable();
            $table->integer('price')->default(0);
            $table->integer('quantity')->default(0);
            $table->longText('images')->nullable();
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_edit_logs');
        Schema::dropIfExists('book_images');
        Schema::dropIfExists('books');
        Schema::dropIfExists('members');
    }
}
