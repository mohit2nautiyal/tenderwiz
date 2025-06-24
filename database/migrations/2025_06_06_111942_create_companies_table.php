<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->text('company_description')->nullable();
            $table->string('state')->nullable();
            $table->string('company_id')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('company_type')->nullable();
            $table->string('department')->nullable();
            $table->json('keywords')->nullable();
            $table->json('websites')->nullable();
            $table->string('company_registration_type')->nullable();
            $table->integer('company_registered_year')->nullable();
            $table->string('company_sector_type')->nullable();
            $table->string('nature_of_business')->nullable();
            $table->string('business_specialization')->nullable();
            $table->string('procurement_category')->nullable();
            $table->string('tender_nature')->nullable();
            $table->json('work_experience')->nullable();
            $table->json('certificates')->nullable();
            $table->json('financial_statements')->nullable();
            $table->json('financials')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}