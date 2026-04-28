<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpoPushNotificationsTable extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up()
	{
		Schema::create(app(config('expo-push.log.driver.instance'))->getTable(), function (Blueprint $table) {
			$table->id();
			$table->string('item_type')->nullable();
			$table->string('item_id')->nullable();
			$table->text('payload')->nullable();
			$table->text('response')->nullable();
			$table->unsignedSmallInteger('code')->default(0)->index();
			$table->string('status')->nullable()->index();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down()
	{
		Schema::drop(app(config('expo-push.log.driver.instance'))->getTable());
	}
}
