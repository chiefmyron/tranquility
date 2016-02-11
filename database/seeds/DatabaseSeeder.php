<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Doctrine\ORM\EntityManagerInterface;
use Tranquility\Enums\System\TransactionSource as EnumTransactionSource;
use Tranquility\Enums\System\EntityType        as EnumEntityType;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();
        
        // Add reference data for locales
        if (($handle = fopen("./database/seeds/referenceData/cd_locales.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                DB::table('cd_locales')->insert(array(
                    'code' => $data[0],
                    'description' => $data[1],
                    'ordering' => $data[2],
                    'effectiveFrom' => DB::raw('NOW()')
                ));
            }
            fclose($handle);
        }
        
        // Add reference data for timezones
        if (($handle = fopen("./database/seeds/referenceData/cd_timezones.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                DB::table('cd_timezones')->insert(array(
                    'code' => $data[0],
                    'description' => $data[1],
                    'ordering' => $data[3],
                    'effectiveFrom' => DB::raw('NOW()')
                ));
            }
            fclose($handle);
        }
        
        // Add reference data for countries
        if (($handle = fopen("./database/seeds/referenceData/cd_countries.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                DB::table('cd_countries')->insert(array(
                    'code' => $data[0],
                    'description' => $data[1],
                    'ordering' => $data[2],
                    'effectiveFrom' => DB::raw('NOW()')
                ));
            }
            fclose($handle);
        }
        
        // Create new User record for system administrator
        $transactionId = DB::table('sys_trans_audit')->insertGetId(array(
            'transactionSource' => EnumTransactionSource::Setup,
            'updateBy' => 1,
            'updateDateTime' => DB::raw('NOW()'),
            'updateReason' => 'database seed'
        ));
        
        $userId = DB::table('entity')->insertGetId(array(
            'version' => 1,
            'type' => EnumEntityType::User,
            'deleted' => 0,
            'transactionId' => $transactionId
        ));
        
        DB::table('entity_users')->insert(array(
            'id' => $userId,
            'username' => 'administrator@homestead.app',
            'password' => Hash::make('password'),
            'timezoneCode' => 'Europe/London',
            'localeCode' => 'en-AU',
            'active' => 1,
            'securityGroupId' => 1,
            'registeredDateTime' => DB::raw('NOW()')
        ));

        // Create new Person record, and associate with the User
        $personId = DB::table('entity')->insertGetId(array(
            'version' => 1,
            'type' => EnumEntityType::Person,
            'deleted' => 0,
            'transactionId' => $transactionId    
        ));
        
        DB::table('entity_people')->insert(array(
            'id' => $personId,
            'firstName' => 'System',
            'lastName' => 'Administrator',
            'userId' => $userId
        ));
	}
}
