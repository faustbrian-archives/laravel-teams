<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class CreateTeamTables extends Migration
{
    public function up()
    {
        Schema::create(Config::get('teams.tables.teams'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('owner_id')->index();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();

            $table
                ->foreign('owner_id')
                ->references('id')
                ->on(Config::get('teams.tables.users'))
                ->onDelete('cascade');
        });

        Schema::create(Config::get('teams.tables.members'), function (Blueprint $table) {
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role');
            $table->json('permissions');

            $table->unique(['team_id', 'user_id']);

            $table
                ->foreign('team_id')
                ->references('id')
                ->on(Config::get('teams.tables.teams'))
                ->onDelete('cascade');

            $table
                ->foreign('user_id')
                ->references('id')
                ->on(Config::get('teams.tables.users'))
                ->onDelete('cascade');
        });

        Schema::create(Config::get('teams.tables.invitations'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('email');
            $table->string('role');
            $table->json('permissions');
            $table->uuid('accept_token')->unique();
            $table->uuid('reject_token')->unique();
            $table->timestamps();

            $table
                ->foreign('team_id')
                ->references('id')
                ->on(Config::get('teams.tables.teams'))
                ->onDelete('cascade');

            $table
                ->foreign('user_id')
                ->references('id')
                ->on(Config::get('teams.tables.users'))
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop(Config::get('teams.tables.invitations'));
        Schema::drop(Config::get('teams.tables.members'));
        Schema::drop(Config::get('teams.tables.teams'));
    }
}
