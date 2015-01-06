<?php

class OauthclientSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
    {
        // id | secret  | name  | created_at  | updated_at
        $clients = array(

                    array(  'id'        => 'IOS_MOB_APP',
                            'secret'    => '$1$w24Qs3SOZ',
                            'name'      => 'IOS_MOB_APP'
                        ),

                    array(  'id'        => 'ANDROID_MOB_APP',
                            'secret'    => '$Ja4p70Qb8ElhwWs3SOZ',
                            'name'      => 'ANDROID_MOB_APP'
                        )
            );

        foreach ($clients as $client) {
            Oauthclient::create($client);
        }
    }

}