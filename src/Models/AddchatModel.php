<?php

namespace Classiebit\Addchat\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class AddchatModel extends Model
{
    private $AC_SETTINGS;
    protected $guarded = [];

    public function __construct()
    {
        // Chatcie tables
        $this->profiles_tb                  = 'ac_profiles';
        $this->ac_messages_tb               = 'ac_messages';
        $this->ac_users_messages_tb         = 'ac_users_messages';
        $this->ac_settings_tb               = 'ac_settings';
        
        // fetch settings
        if(empty(session('ac_session')))
		{
            $settings 			  	  			= 	$this->get_settings();
        	$tmp 					            = 	new \stdClass();
			foreach ($settings as $setting)
			{
				$tmp->{$setting->s_name} = $setting->s_value;
			}
            // insert config in session
            session(['ac_session'=>$tmp]);
            $this->AC_SETTINGS  		= 	session('ac_session');
		}
		else
		{
            $this->AC_SETTINGS  		= 	session('ac_session');
        }

        // External tables
        // users table
        $this->users_tb                     = $this->AC_SETTINGS->users_table;
        $this->users_tb_id                  = $this->AC_SETTINGS->users_col_id;
        $this->users_tb_email               = $this->AC_SETTINGS->users_col_email;
    }


    /* ----- Messages ----- */

    // send message
    public function send_message($params = []) 
    {
       return DB::table($this->ac_messages_tb)->insertGetId($params);
        
    }
    
    // get messages between two users
    public function get_messages($login_user_id = null, $params)
    {
        $query = DB::table($this->ac_messages_tb);

        $query
        ->select(array(
            "$this->ac_messages_tb.id",
            "$this->ac_messages_tb.m_from",
            "$this->ac_messages_tb.m_to",
            "$this->ac_messages_tb.message",
            "$this->ac_messages_tb.is_read",
            "$this->ac_messages_tb.dt_updated",
        ));
        
        $query
        ->whereRaw("( (`$this->ac_messages_tb`.`m_from` = '$login_user_id' AND `$this->ac_messages_tb`.`m_to` = '".$params['chat_user']."')")
        ->orWhereRaw("(`$this->ac_messages_tb`.`m_from` = '".$params['chat_user']."' AND `$this->ac_messages_tb`.`m_to` = '$login_user_id') )")
        
        //removing deleted messages and unsend
        ->whereRaw("( (IF(`$this->ac_messages_tb`.`m_from` = '$login_user_id', `$this->ac_messages_tb`.`m_from_delete`, `$this->ac_messages_tb`.`m_to_delete`) = 0) AND (IF(`$this->ac_messages_tb`.`m_to` = '$login_user_id', `$this->ac_messages_tb`.`m_to_delete`, `$this->ac_messages_tb`.`m_from_delete`) = 0) )");

        if(!$params['count'])
            return $query->count();
        
        $messages   = $query
                    ->orderBy("$this->ac_messages_tb.id")
                    ->limit($params['filters']['limit'])
                    ->offset($params['filters']['offset'])
                    ->get()
                    ->toArray();
        
        DB::table($this->ac_messages_tb)
        ->where("$this->ac_messages_tb.m_to", $login_user_id)
        ->where("$this->ac_messages_tb.m_from", $params['chat_user'])
        ->update(array("$this->ac_messages_tb.is_read"=>'1'));

        return $messages;
    }
    
    public function message_delete($message_id = null, $login_user_id = null)
	{
        $query      = DB::table($this->ac_messages_tb);
        $message    = $query
                    ->select('*')
                    ->where("id", $message_id)
                    ->first();
        
        if(empty($message))
            return false;

        $query
            ->where(array("id" => $message_id));
            
        if($message->m_from == $login_user_id)
            return  $query->update(["m_from_delete" => '1']);

        if($message->m_to == $login_user_id)
            return  $query->update(["m_to_delete" => '1']);    

	}

    // delete all message on user from one side
    public function delete_chat($login_user_id = null, $params = [])
    {
        
        $query = DB::table($this->ac_messages_tb);

         $query
        ->where(array("$this->ac_messages_tb.m_from"=>$login_user_id, "$this->ac_messages_tb.m_to"=>$params['user_id']))
        ->update(array("m_from_delete"=>1));

        $query = DB::table($this->ac_messages_tb);
        
        $query
        ->where(array("$this->ac_messages_tb.m_to"=>$login_user_id, "$this->ac_messages_tb.m_from"=>$params['user_id']))
        ->update(array("m_to_delete"=>1));

        return TRUE;
    }

    
    
    /* ----- Users ----- */
    
    // update user profile
    public function update_user($login_user_id = 0, $params = [])
    {
           
        $result =  DB::table($this->profiles_tb)
                    ->select()
                    ->where('user_id', $login_user_id)
                    ->first();
                    
                    
        // insert data in profile table if user have not exist 
        if(empty($result))
        {
            return  DB::table($this->profiles_tb)
                            ->insert($params);
        }
        else
        {
          // if user have exist then update user data  
            return  DB::table($this->profiles_tb)
                            ->where("user_id", $login_user_id)
                            ->update($params);
        }        

        
        
    }

    // get logged in user profile
    public function get_profile($login_user_id = null)
    {
        $select = array(
            "$this->users_tb.$this->users_tb_id",
            "$this->users_tb.$this->users_tb_email",
            "$this->profiles_tb.fullname",
            "$this->profiles_tb.avatar",
            "$this->profiles_tb.status as online",
        );
        
        return  DB::table($this->users_tb)
                ->select($select)
                ->leftjoin($this->profiles_tb, "$this->profiles_tb.user_id", "=" ,"$this->users_tb.$this->users_tb_id")
                ->where("$this->users_tb.$this->users_tb_id", $login_user_id)
                ->first();
    }

    // get specific user by id
    public function get_user($login_user_id  = 0, $params = [])
    {
        $select = array(
            "$this->users_tb.$this->users_tb_id",
            "$this->users_tb.$this->users_tb_email",

            "$this->profiles_tb.fullname",
            "$this->profiles_tb.avatar",
            "$this->profiles_tb.status as online",
        );
        
        return  DB::table($this->users_tb)
                    ->select($select)
                    ->leftjoin($this->profiles_tb, "$this->profiles_tb.user_id", "=" ,"$this->users_tb.$this->users_tb_id")
                    ->where("$this->users_tb.$this->users_tb_id", $params['user_id'])
                    ->get()
                    ->first();
    }

    // get users
    public function get_users($login_user_id = null, $params = [])
    {
        $query = DB::table($this->users_tb);
        
        $query
        ->select(array(
            "$this->users_tb.$this->users_tb_id",
            "$this->users_tb.$this->users_tb_email",
            "$this->profiles_tb.avatar",
            "$this->profiles_tb.fullname as username",
            "$this->profiles_tb.status as online",

            DB::raw("(SELECT IF(COUNT(ACM.id) > 0, COUNT(ACM.id), null) FROM $this->ac_messages_tb ACM WHERE ACM.m_to = '$login_user_id' AND ACM.m_from = '$this->users_tb.$this->users_tb_id' AND ACM.is_read = '0') as unread"),
        ))
        ->leftJoin($this->profiles_tb, "$this->profiles_tb.user_id",  '=' ,"$this->users_tb.$this->users_tb_id")
        ->where("$this->users_tb.$this->users_tb_id", "!=" , $login_user_id);

        // in case of search, search amongst all users
        if(!empty($params['filters']['search']) )
        {
            // admin can seach all users
            // and if have  is_groups off then user can search all users
            $query
            ->whereRaw("($this->profiles_tb.fullname LIKE '%".$params['filters']['search']."%' 
                    OR $this->users_tb.$this->users_tb_email LIKE '%".$params['filters']['search']."%')");
        }
        
        return  $query
                ->limit($params['filters']['limit'])
                ->offset($params['filters']['offset'])
                ->get()
                ->toArray();
    }

    /* -------- Notifications -------- */

    // add notification 
    public function set_notification($params = [])
    {
        
        $query  = DB::table($this->ac_users_messages_tb);
        $result =  $query
                    ->select()
                    ->where($params)
                    ->first();
        
        // insert
        if(empty($result))
        {            
            $query->insert($params);
        }
        else // update messages_count
        {
            
            $query
            ->where($params)
            ->increment('messages_count', 1);

        }

        return true;
        
    }
     
    // Remove notification
    public function remove_notification($params = [])
    {
        return DB::table($this->ac_users_messages_tb)
                ->where($params)
                ->delete(); 
        
    }
    
    //  get notification
    public function get_updates($login_user_id = null)
    {

        $query = DB::table($this->ac_users_messages_tb);
        
        $query
        ->select(array(
            "$this->ac_users_messages_tb.users_id",
            "$this->ac_users_messages_tb.buddy_id",
            "$this->ac_users_messages_tb.messages_count",
        ))
        ->where("buddy_id", $login_user_id);
        
        return $query
                ->get()
                ->toArray();
    }
    
    //  get latest message
    public function get_latest_message($login_user_id = null, $params = [])
    {
        $query = DB::table($this->ac_messages_tb);

        $result =    $query
                    ->select(array(
                        "$this->ac_messages_tb.id",
                        "$this->ac_messages_tb.m_from",
                        "$this->ac_messages_tb.m_to",
                        "$this->ac_messages_tb.message",
                        "$this->ac_messages_tb.is_read",
                        "$this->ac_messages_tb.dt_updated",
                    ))
                    ->where(array("$this->ac_messages_tb.m_from" => $params['buddy_id'], "$this->ac_messages_tb.m_to" => $login_user_id, "$this->ac_messages_tb.is_read" => '0'))
                    
                    //group query for removing unsend messages
                    ->where(["$this->ac_messages_tb.m_from_delete" => "0", "$this->ac_messages_tb.m_to_delete" => "0"])
                    ->orderBy("$this->ac_messages_tb.id")
                    ->get()
                    ->toArray();

        // delete notification
        $query = DB::table($this->ac_messages_tb);

        $query
        ->where("$this->ac_messages_tb.m_to", $login_user_id)
        ->where("$this->ac_messages_tb.m_from", $params['buddy_id'])
        ->update(array("$this->ac_messages_tb.is_read"=>'1'));

        return $result;
    }


    /* ------- Admin Panel ------- */
    
    // get chat users
    public function a_chat_between($params = [])
    {
        $mode   = config('database.connections.mysql.strict');
        $query  = DB::table($this->ac_messages_tb);
        if(!$mode)
        {
            // safe mode is off
            $select = array(
                "$this->ac_messages_tb.id",
                "$this->ac_messages_tb.m_to",
                "$this->ac_messages_tb.m_from",
                "$this->ac_messages_tb.dt_updated",
                "$this->ac_messages_tb.message",

                DB::raw("(SELECT PR.fullname  FROM $this->profiles_tb  PR  WHERE PR.user_id  = $this->ac_messages_tb.m_from) m_from_username"),
                DB::raw("(SELECT PR2.fullname FROM $this->profiles_tb  PR2 WHERE PR2.user_id = $this->ac_messages_tb.m_to) m_to_username"),
                DB::raw("(SELECT UR.$this->users_tb_email  FROM $this->users_tb UR WHERE UR.$this->users_tb_id = $this->ac_messages_tb.m_from)
                    m_from_email"),
                DB::raw("(SELECT UR2.$this->users_tb_email FROM $this->users_tb UR2 WHERE UR2.$this->users_tb_id = $this->ac_messages_tb.m_to) 
                    m_to_email"),
            );
        }
        else
        {
            // safe mode is on
            $select = array(
                DB::raw("ANY_VALUE($this->ac_messages_tb.id) as id"),
                DB::raw("ANY_VALUE($this->ac_messages_tb.m_to) as m_to"),
                "$this->ac_messages_tb.m_from",
                DB::raw("ANY_VALUE($this->ac_messages_tb.dt_updated) as dt_updated"),
                DB::raw("ANY_VALUE($this->ac_messages_tb.message) as message"),
                
                DB::raw("ANY_VALUE((SELECT PR.fullname  FROM $this->profiles_tb  PR  WHERE PR.user_id  = $this->ac_messages_tb.m_from)) as m_from_username"),
                DB::raw("ANY_VALUE((SELECT PR2.fullname FROM $this->profiles_tb  PR2 WHERE PR2.user_id = $this->ac_messages_tb.m_to)) as m_to_username"),
                DB::raw("ANY_VALUE((SELECT UR.$this->users_tb_email  FROM $this->users_tb UR WHERE UR.$this->users_tb_id = $this->ac_messages_tb.m_from)) as m_from_email"),
                DB::raw("ANY_VALUE((SELECT UR2.$this->users_tb_email FROM $this->users_tb UR2 WHERE UR2.$this->users_tb_id = $this->ac_messages_tb.m_to)) as m_to_email"),
            );
        }

        return  $query
                ->select($select)
                ->where([array('m_to', '!=', '0'), array('m_from', '!=', '0')])
                ->groupBy(array("$this->ac_messages_tb.m_from", "m_to"))
                ->orderBy("id", 'DESC')
                ->limit($params['filters']['limit'])
                ->offset($params['filters']['offset'])  
                ->get()
                ->toArray();
                 
    }

    // get conversations between two users
    public function a_get_conversations($params = [])
    {
        $query      = DB::table($this->ac_messages_tb);

        $query
        ->select(array(
            "$this->ac_messages_tb.id",
            "$this->ac_messages_tb.m_from",
            "$this->ac_messages_tb.m_to",
            "$this->ac_messages_tb.message",
            "$this->ac_messages_tb.is_read",
            "$this->ac_messages_tb.dt_updated",
            "$this->ac_messages_tb.m_to_delete",
            "$this->ac_messages_tb.m_from_delete",
            DB::raw("(SELECT PR.avatar  FROM $this->profiles_tb PR WHERE PR.user_id    = $this->ac_messages_tb.m_from) m_from_image"),
            DB::raw("(SELECT PR2.avatar FROM $this->profiles_tb PR2 WHERE PR2.user_id  = $this->ac_messages_tb.m_to)   m_to_image"),
        ));

        // group query for removing deleted messages
        $query
        ->whereRaw("((`$this->ac_messages_tb`.`m_from` = '".$params['m_from']."' AND `$this->ac_messages_tb`.`m_to` = '".$params['m_to']."'))")
        ->orWhereRaw("((`$this->ac_messages_tb`.`m_from` = '".$params['m_to']."'  AND `$this->ac_messages_tb`.`m_to` = '".$params['m_from']."') )");
        
        if($params['count'])
            return $query->count();

        return  $query
                    ->orderBy("$this->ac_messages_tb.id")
                    ->limit($params['filters']['limit'])
                    ->offset($params['filters']['offset'])
                    ->get()
                    ->toArray();
    }

    // save settings
    public function save_settings($params = [])
    {
        
        if (!empty($params))
        {
            $saved = FALSE;

            foreach ($params as $key => $value)
            {
                $sql = DB::raw("
                    UPDATE {$this->ac_settings_tb}
                    SET s_value = '" . $value . "',
                        dt_updated = '" . date('Y-m-d H:i:s') . "'
                    WHERE s_name = '" . $key . "'
                ");
                
                
                $affected_rows = DB::statement($sql);

                if($affected_rows)
                {
                    $saved = TRUE;
                }
            }

            if ($saved)
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    // get settings
    public function get_settings()
    {
        return  DB::table($this->ac_settings_tb)
                ->select("$this->ac_settings_tb.*")
                ->get()
                ->toArray();
    }

   
}