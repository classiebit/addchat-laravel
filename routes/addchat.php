<?php


use Classiebit\Addchat\Facades\Addchat;

/*
|--------------------------------------------------------------------------
| Package Routes
|--------------------------------------------------------------------------
|
*/

$namespace = '\Classiebit\Addchat\Http\Controllers';
Route::group([
    'namespace' => $namespace,
    'as'    => 'addchat.'
], function() {

    $namespace = '\Classiebit\Addchat\Http\Controllers';
    // API ROUTES
    Route::prefix('addchat_api')->group(function () use($namespace) {
        $controller = $namespace."\ApiController";

        // set & get lang
        Route::post('get_lang', function() {

            // default lang
            $lang = config('app.locale');

            // user lang
            if(!empty($_POST['lang']))
            {
                $lang = $_POST['lang'];    
                \App::setLocale($lang);
            }

            $data = [
                'lang' => Lang::get('addchat::ac'),
            ];

            return response()->json($data);
        });

        /* ------ Front-End ------ */
        Route::post('get_config', "$controller@get_config");
        Route::post('get_users/{offset?}', "$controller@get_users");
        Route::post('get_profile', "$controller@get_profile");
        Route::post('get_buddy', "$controller@get_buddy");
        Route::post('profile_update', "$controller@profile_update");
        
        Route::post('get_messages/{buddy_id?}/{offset}', "$controller@get_messages");
        Route::post('send_message', "$controller@send_message");
        Route::post('message_delete/{message_id}', "$controller@message_delete");
        Route::post('delete_chat/{user_id?}', "$controller@delete_chat");
        
        Route::post('get_updates', "$controller@get_updates");
        Route::post('get_latest_message/{buddy_id?}', "$controller@get_latest_message");

        
        /* ------ Back-End ------ */
        Route::post('check_admin/{is_return?}', "$controller@check_admin");
        Route::post('a_chat_between/{offset?}', "$controller@a_chat_between");
        Route::post('save_settings', "$controller@save_settings");
        Route::post('a_get_conversations/{m_from?}/{m_to?}/{offset?}', "$controller@a_get_conversations");
    });


});