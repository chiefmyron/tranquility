<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitialDatabaseSchema extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        /*************************************************************************
         * Reference data tables                                                 *
         *                                                                       *
         * Used for application lookups                                          *
         *************************************************************************/
        
        // Locale reference data
        Schema::create('tql_cd_locales', function(Blueprint $table) {
            $table->string('code', 30)->primary();
            $table->string('description', 100);
            $table->integer('ordering');
            $table->dateTime('effectiveFrom');
            $table->dateTime('effectiveUntil')->nullable();
        });
        
        
        // Timezone reference data
        Schema::create('tql_cd_timezones', function(Blueprint $table) {
            $table->string('code', 30)->primary();
            $table->string('description', 100);
            $table->boolean('daylightSavings');
            $table->integer('ordering');
            $table->dateTime('effectiveFrom');
            $table->dateTime('effectiveUntil')->nullable();
        });
        
        /*************************************************************************
         * Business object tables                                                *
         *                                                                       *
         * Used to store currently active business data                          *
         *************************************************************************/
        
        // Base business object entity
        Schema::create('tql_entity', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('version');
            $table->string('type', 25);
            $table->string('subType', 25)->nullable();
            $table->boolean('deleted');
            $table->bigInteger('transactionId');
            $table->primary('id');
        });
        
        // Address details - electronic
        Schema::create('tql_entity_addresses_electronic', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->string('addressType', 25);
            $table->string('category', 25);
            $table->string('addressText', 25);
            $table->boolean('primaryContact');
        });
        
        // Address details - phone
        Schema::create('tql_entity_addresses_phone', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->string('addressType', 25);
            $table->string('addressText', 25);
            $table->boolean('primaryContact');
        });
        
        // Address details - physical
        Schema::create('tql_entity_addresses_physical', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->string('addressType', 25);
            $table->string('addressLine1', 255);
            $table->string('addressLine2', 255)->nullable();
            $table->string('addressLine3', 255)->nullable();
            $table->string('addressLine4', 255)->nullable();
            $table->string('city', 255);
            $table->string('state', 255);
            $table->string('postcode', 255);
            $table->string('country', 255);
            $table->float('latitude')->default(0);
            $table->float('longitude')->default(0);
        });
        
        // Person or contact
        Schema::create('tql_entity_people', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->string('title', 50);
            $table->string('firstName', 255);
            $table->string('lastName', 255);
            $table->string('position', 255)->nullable();
            $table->bigInteger('userId')->nullable();
        });
        
        // Application user account
        Schema::create('tql_entity_users', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->string('username', 255)->unique();
            $table->string('password', 255);
            $table->string('timezoneCode', 30);
            $table->string('localeCode', 30);
            $table->boolean('active');
            $table->bigInteger('securityGroupId');
            $table->dateTime('registeredDateTime');
        });
        
        // Business object cross-reference table
        Schema::create('tql_entity_xref', function(Blueprint $table) {
            $table->bigInteger('parentId');
            $table->bigInteger('childId');
            $table->primary(['parentId', 'childId']);
        });
        
        /*************************************************************************
         * Historical business object tables                                     *
         *                                                                       *
         * Used to store previous versions of business objects for audit         *
         * purposes                                                              *
         *************************************************************************/
        
        // Base business object entity
        Schema::create('tql_history_entity', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('version');
            $table->string('type', 25);
            $table->string('subType', 25)->nullable();
            $table->boolean('deleted');
            $table->bigInteger('transactionId');
            $table->primary(['id', 'version']);
        });
        
        // Address details - electronic
        Schema::create('tql_history_entity_addresses_electronic', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('version');
            $table->string('addressType', 25);
            $table->string('category', 25);
            $table->string('addressText', 25);
            $table->boolean('primaryContact');
            $table->primary(['id', 'version']);
        });
        
        // Address details - phone
        Schema::create('tql_history_entity_addresses_phone', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('version');
            $table->string('addressType', 25);
            $table->string('addressText', 25);
            $table->boolean('primaryContact');
            $table->primary(['id', 'version']);
        });
        
        // Address details - physical
        Schema::create('tql_history_entity_addresses_physical', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('version');
            $table->string('addressType', 25);
            $table->string('addressLine1', 255);
            $table->string('addressLine2', 255)->nullable();
            $table->string('addressLine3', 255)->nullable();
            $table->string('addressLine4', 255)->nullable();
            $table->string('city', 255);
            $table->string('state', 255);
            $table->string('postcode', 255);
            $table->string('country', 255);
            $table->float('latitude')->default(0);
            $table->float('longitude')->default(0);
            $table->primary(['id', 'version']);
        });
        
        // Person or contact
        Schema::create('tql_history_entity_people', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('version');
            $table->string('title', 50);
            $table->string('firstName', 255);
            $table->string('lastName', 255);
            $table->string('position', 255)->nullable();
            $table->bigInteger('userId')->nullable();
            $table->primary(['id', 'version']);
        });
        
        // Application user account
        Schema::create('tql_history_entity_users', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('version');
            $table->string('username', 255)->unique();
            $table->string('password', 255);
            $table->string('timezoneCode', 30);
            $table->string('localeCode', 30);
            $table->boolean('active');
            $table->bigInteger('securityGroupId');
            $table->dateTime('registeredDateTime');
            $table->primary(['id', 'version']);
        });
        
        /*************************************************************************
         * System tables                                                         *
         *                                                                       *
         * Used for audit trail details, system configuration and security roles *
         *************************************************************************/
        Schema::create('tql_sys_trans_audit', function(Blueprint $table) {
            $table->bigIncrements('transactionId');
            $table->string('transactionSource', 100);
            $table->bigInteger('updateBy');
            $table->dateTime('updateDateTime');
            $table->string('updateReason', 100);
        });
        
        Schema::create('tql_sys_entity_locks', function(Blueprint $table) {
            $table->bigInteger('entityId')->primary();
            $table->bigInteger('lockedBy');
            $table->dateTime('lockedDateTime');
        });
        
        Schema::create('tql_sys_sessions', function(Blueprint $table) {
            $table->string('sessionId')->primary();
            $table->text('payload');    
            $table->integer('lastActivity');
            $table->integer('userId')->nullable();
            $table->string('ipAddress')->nullable();
            $table->text('userAgent');
        });
        
        Schema::create('tql_sys_user_tokens', function(Blueprint $table) {
            $table->bigInteger('userId')->primary();
            $table->string('sessionId')->nullable();
            $table->string('rememberToken')->nullable();
        });
    }

    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('tql_sys_user_tokens');
        Schema::dropIfExists('tql_sys_entity_locks');
        Schema::dropIfExists('tql_sys_trans_audit');
        Schema::dropIfExists('tql_history_entity_users');
        Schema::dropIfExists('tql_history_entity_people');
        Schema::dropIfExists('tql_history_entity_addresses_physical');
        Schema::dropIfExists('tql_history_entity_addresses_phone');
        Schema::dropIfExists('tql_history_entity_addresses_electronic');
        Schema::dropIfExists('tql_history_entity');
        Schema::dropIfExists('tql_entity_xref');
        Schema::dropIfExists('tql_entity_users');
        Schema::dropIfExists('tql_entity_people');
        Schema::dropIfExists('tql_entity_addresses_physical');
        Schema::dropIfExists('tql_entity_addresses_phone');
        Schema::dropIfExists('tql_entity_addresses_electronic');
        Schema::dropIfExists('tql_entity');
        Schema::dropIfExists('tql_cd_timezones');
        Schema::dropIfExists('tql_cd_locales');
    }
}
