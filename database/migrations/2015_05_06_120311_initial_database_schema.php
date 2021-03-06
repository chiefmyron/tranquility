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
        Schema::create('cd_locales', function(Blueprint $table) {
            $table->string('code', 30)->primary();
            $table->string('description', 100);
            $table->integer('ordering');
            $table->dateTime('effectiveFrom');
            $table->dateTime('effectiveUntil')->nullable();
        });
        
        // Timezone reference data
        Schema::create('cd_timezones', function(Blueprint $table) {
            $table->string('code', 30)->primary();
            $table->string('description', 100);
            $table->boolean('daylightSavings');
            $table->integer('ordering');
            $table->dateTime('effectiveFrom');
            $table->dateTime('effectiveUntil')->nullable();
        });
        
        // Countries reference data
        Schema::create('cd_countries', function(Blueprint $table) {
            $table->string('code', 30)->primary();
            $table->string('description', 100);
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
        Schema::create('entity', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('version');
            $table->string('type', 25);
            $table->string('subType', 25)->nullable();
            $table->boolean('deleted');
            $table->bigInteger('transactionId');
        });
        
        // Address details - electronic, social, phone
        Schema::create('entity_addresses', function(Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('parentId');
            $table->string('category', 25);
            $table->string('addressType', 25);
            $table->string('addressText', 255);
            $table->boolean('primaryContact');
        });
        
        // Address details - physical
        Schema::create('entity_addresses_physical', function(Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('parentId');
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
        
        // Person 
        Schema::create('entity_people', function(Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('title', 50)->nullable();
            $table->string('firstName', 255);
            $table->string('lastName', 255);
            $table->string('position', 255)->nullable();
            $table->bigInteger('userId')->nullable();
        });
        
        // Application user account
        Schema::create('entity_users', function(Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('username', 255);
            $table->string('password', 255);
            $table->string('timezoneCode', 30);
            $table->string('localeCode', 30);
            $table->boolean('active');
            $table->bigInteger('securityGroupId');
            $table->dateTime('registeredDateTime');
        });

        // Account
        Schema::create('entity_accounts', function(Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('name', 255);
        });

        // Contact (linkage of Person to Account)
        Schema::create('entity_contacts', function(Blueprint $table) {
            $table->bigInteger('personId');
            $table->bigInteger('accountId');
            $table->boolean('primaryContact');
            $table->primary(['personId', 'accountId']);
        });

        // Entity cross-referencing for tags
        Schema::create('entity_tags_xref', function(Blueprint $table) {
            $table->bigInteger('entityId');
            $table->bigInteger('tagId');
            $table->primary(['entityId', 'tagId']);    
        });
        
        /*************************************************************************
         * Historical business object tables                                     *
         *                                                                       *
         * Used to store previous versions of business objects for audit         *
         * purposes                                                              *
         *************************************************************************/
        
        // Base business object entity
        Schema::create('history_entity', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('version');
            $table->string('type', 25);
            $table->string('subType', 25)->nullable();
            $table->boolean('deleted');
            $table->bigInteger('transactionId');
            $table->primary(['id', 'version']);
        });
        
        // Address details - electronic, social, phone
        Schema::create('history_entity_addresses', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('parentId');
            $table->integer('version');
            $table->string('category', 25);
            $table->string('addressType', 25);
            $table->string('addressText', 255);
            $table->boolean('primaryContact');
            $table->primary(['id', 'version']);
        });
        
        // Address details - physical
        Schema::create('history_entity_addresses_physical', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('parentId');
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
        
        // Person
        Schema::create('history_entity_people', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('version');
            $table->string('title', 50)->nullable();
            $table->string('firstName', 255);
            $table->string('lastName', 255);
            $table->string('position', 255)->nullable();
            $table->bigInteger('userId')->nullable();
            $table->primary(['id', 'version']);
        });
        
        // Application user account
        Schema::create('history_entity_users', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('version');
            $table->string('username', 255);
            $table->string('password', 255);
            $table->string('timezoneCode', 30);
            $table->string('localeCode', 30);
            $table->boolean('active');
            $table->bigInteger('securityGroupId');
            $table->dateTime('registeredDateTime');
            $table->primary(['id', 'version']);
        });

        // Account
        Schema::create('history_entity_accounts', function(Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('version');
            $table->string('name', 255);
            $table->primary(['id', 'version']);
        });

        // Contact (linkage of Person to Account)
        Schema::create('history_entity_contacts', function(Blueprint $table) {
            $table->bigInteger('personId');
            $table->bigInteger('accountId');
            $table->integer('version');
            $table->boolean('primaryContact');
            $table->primary(['personId', 'accountId', 'version']);
        });
        
        /*************************************************************************
         * Entity data extension tables                                          *
         *                                                                       *
         * Used to extend entity objects, but are not actual entities themselves *
         * e.g. Tags, etc...                                                     *
         *************************************************************************/
        // Tags
        Schema::create('ext_tags', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('text');    
        });
        
        /*************************************************************************
         * System tables                                                         *
         *                                                                       *
         * Used for audit trail details, system configuration and security roles *
         *************************************************************************/
        // Audit trail
        Schema::create('sys_trans_audit', function(Blueprint $table) {
            $table->bigIncrements('transactionId');
            $table->string('transactionSource', 100);
            $table->bigInteger('updateBy');
            $table->dateTime('updateDateTime');
            $table->string('updateReason', 100);
        });
        
        // Entity locking
        Schema::create('sys_entity_locks', function(Blueprint $table) {
            $table->bigInteger('entityId')->primary();
            $table->bigInteger('lockedBy');
            $table->dateTime('lockedDateTime');
        });
        
        // Server side session data storage
        Schema::create('sys_sessions', function(Blueprint $table) {
            $table->string('sessionId')->primary();
            $table->text('payload');    
            $table->integer('lastActivity');
            $table->integer('userId')->nullable();
            $table->string('ipAddress')->nullable();
            $table->text('userAgent');
        });
        
        // User account tokens (e.g. remember me, password reset, etc...)
        Schema::create('sys_user_tokens', function(Blueprint $table) {
            $table->bigInteger('userId');
            $table->string('type');
            $table->string('tokenText')->nullable();
            $table->primary(['userId', 'type']);
        });
    }

    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sys_user_tokens');
        Schema::dropIfExists('sys_sessions');
        Schema::dropIfExists('sys_entity_locks');
        Schema::dropIfExists('sys_trans_audit');
        Schema::dropIfExists('ext_tags');
        Schema::dropIfExists('history_entity_users');
        Schema::dropIfExists('history_entity_people');
        Schema::dropIfExists('history_entity_accounts');
        Schema::dropIfExists('history_entity_contacts');
        Schema::dropIfExists('history_entity_addresses_physical');
        Schema::dropIfExists('history_entity_addresses');
        Schema::dropIfExists('history_entity');
        Schema::dropIfExists('entity_tags_xref');
        Schema::dropIfExists('entity_users');
        Schema::dropIfExists('entity_people');
        Schema::dropIfExists('entity_accounts');
        Schema::dropIfExists('entity_contacts');
        Schema::dropIfExists('entity_addresses_physical');
        Schema::dropIfExists('entity_addresses');
        Schema::dropIfExists('entity');
        Schema::dropIfExists('cd_timezones');
        Schema::dropIfExists('cd_locales');
        Schema::dropIfExists('cd_countries');
    }
}
