<?php

class UsersTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		DB::table('users')->truncate();

        $users = array(
            array(
                'user_name'  => 'root@no',
                'password'   => Hash::make('root'),
                'name'       => '喜柚',
                'mobile'     => '13000000000',
                'head'       => '0',
                'disabled'   => FALSE,
                'created_at' => new DateTime,
                'updated_at' => new DateTime,
                'remember_token' => NULL,
                'enclosures_path' => NULL,
                'level' => '0',
                'message' => '0',
                'audio' => '0',
                'firm' => NULL,
            )
        );

		// Uncomment the below to run the seeder
		DB::table('users')->insert($users);
	}

}
