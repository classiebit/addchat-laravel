<?php

use Illuminate\Database\Seeder;

class AcSettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $setting = \DB::table('ac_settings')->where('id', 1)->first();
        if(!$setting)
        {
            \DB::table('ac_settings')->insert(array (
                0 => 
                array (
                    'id' => 1,
                    's_name' => 'admin_user_id',
                    's_value' => '1',
                    'dt_updated' => '2019-09-19 10:22:49',
                ),
                1 => 
                array (
                    'id' => 2,
                    's_name' => 'pagination_limit',
                    's_value' => '5',
                    'dt_updated' => '2019-09-19 10:22:49',
                ),
                2 => 
                array (
                    'id' => 3,
                    's_name' => 'img_upload_path',
                    's_value' => 'upload',
                    'dt_updated' => '2019-03-06 00:00:00',
                ),
                3 => 
                array (
                    'id' => 4,
                    's_name' => 'assets_path',
                    's_value' => 'assets',
                    'dt_updated' => '2019-09-19 10:22:49',
                ),
                4 => 
                array (
                    'id' => 5,
                    's_name' => 'users_table',
                    's_value' => 'users',
                    'dt_updated' => '2019-09-19 10:22:49',
                ),
                5 => 
                array (
                    'id' => 6,
                    's_name' => 'users_col_id',
                    's_value' => 'id',
                    'dt_updated' => '2019-09-19 10:22:49',
                ),
                6 => 
                array (
                    'id' => 7,
                    's_name' => 'users_col_email',
                    's_value' => 'email',
                    'dt_updated' => '2019-09-19 10:22:49',
                ),
                7 => 
                array (
                    'id' => 8,
                    's_name' => 'site_name',
                    's_value' => 'AddChat',
                    'dt_updated' => '2019-09-19 10:22:49',
                ),
                8 => 
                array (
                    'id' => 9,
                    's_name' => 'site_logo',
                    's_value' => NULL,
                    'dt_updated' => '2019-09-06 08:25:52',
                ),
                9 => 
                array (
                    'id' => 10,
                    's_name' => 'chat_icon',
                    's_value' => NULL,
                    'dt_updated' => '2019-09-06 08:24:20',
                ),
                10 => 
                array (
                    'id' => 11,
                    's_name' => 'notification_type',
                    's_value' => '0',
                    'dt_updated' => '2019-09-19 10:22:49',
                ),
                11 => 
                array (
                    'id' => 12,
                    's_name' => 'footer_text',
                    's_value' => 'AddChat | by Classiebit',
                    'dt_updated' => '2019-09-19 10:22:49',
                ),
                12 => 
                array (
                    'id' => 13,
                    's_name' => 'footer_url',
                    's_value' => 'https://classiebit.com/addchat-laravel-pro',
                    'dt_updated' => '2019-09-19 10:22:49',
                ),
            ));
        }
        
    }
}