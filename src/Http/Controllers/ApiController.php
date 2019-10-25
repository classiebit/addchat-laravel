<?php

namespace Classiebit\Addchat\Http\Controllers;


use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;

use App\Http\Controllers\Controller; 

use Illuminate\Http\Request;
use Facades\Classiebit\Addchat\Addchat;
use Auth;
use Classiebit\Addchat\Models\AddchatModel;
use Validator;


class ApiController extends Controller
{
	private $AC_LIB;
	private $AC_SETTINGS;

    public function __construct()
    {
        // call Addchat model constructor first
        $this->AC_LIB 					= new \stdClass();
        $this->AC_LIB->addchat_db_lib 	= new AddchatModel();
	}

	/*
    * Get configurations
    */
    public function get_config()
    { 
		
		// init config in each method
		$this->init_config();

		$data['config'] 						= array();
		$data['config']['site_name'] 			= $this->AC_SETTINGS->site_name;
		$data['config']['site_logo'] 			= $this->AC_SETTINGS->site_logo;
		$data['config']['chat_icon'] 			= $this->AC_SETTINGS->chat_icon;
		$data['config']['logged_user_id'] 		= $this->AC_SETTINGS->logged_user_id;
		$data['config']['img_upld_pth']			= $this->AC_SETTINGS->img_upload_path;
		$data['config']['assets_path']			= $this->AC_SETTINGS->assets_path;
		$data['config']['is_admin']				= $this->AC_SETTINGS->is_admin;
		$data['config']['admin_user_id']		= $this->AC_SETTINGS->admin_user_id;
		$data['config']['pagination_limit']		= $this->AC_SETTINGS->pagination_limit;
		$data['config']['users_table']			= $this->AC_SETTINGS->users_table;
		$data['config']['users_col_id']			= $this->AC_SETTINGS->users_col_id;
		$data['config']['users_col_email']		= $this->AC_SETTINGS->users_col_email;
		$data['config']['notification_type']	= $this->AC_SETTINGS->notification_type;
		$data['config']['footer_text'] 			= $this->AC_SETTINGS->footer_text;
		$data['config']['footer_url'] 			= $this->AC_SETTINGS->footer_url;
		
		return $this->format_json($data);
    }

    /*
	*	Get user's profile 
	*/
	public function get_profile($is_return = false)
    {
		// init config in each method
		$this->init_config();

		// check authentication
        $this->check_auth();

		$data					= array();
		$data['status'] 		= true;
		$data['profile'] 		= $this->AC_LIB->addchat_db_lib->get_profile($this->AC_SETTINGS->logged_user_id);

		if($is_return)
			return $data;

		return $this->format_json($data);
	}

    /**
	 * Get buddy
	 */
	public function get_buddy(Request $request)
	{
		// init config in each method
		$this->init_config();

		// check authentication
        $this->check_auth();
	
		// input and validate
		$validator = Validator::make($request->all(), [
			'user'    => 'required|numeric|min:1|regex:^[1-9][0-9]*$^',
			'limit'   => 'numeric',
		]);
		
		if($validator->fails())
		{
			$data = array('status' => false, 'response'=> $validator->errors()->all());
			return $this->format_json($data);
		}
		   
		$data				= array();
		$buddy 				= (int) $request->user;

		$params = [
			'user_id' => $buddy,
		];

		$chatbuddy 			= $this->AC_LIB->addchat_db_lib->get_user($this->AC_SETTINGS->logged_user_id, $params);

		$c_buddy = array(
			'name' 		 	=> ucwords($chatbuddy->fullname),
			'status' 	 	=> $chatbuddy->online,
			'avatar'		=> $chatbuddy->avatar,
			'id' 		 	=> $chatbuddy->id,
			'email'			=> $chatbuddy->email,
			);
		$data['buddy']		=	$c_buddy;
		$data['status']		=	true;
	
		return $this->format_json($data);
	}

    /*
    * Get users list get_users
    */
    public function get_users($offset = 0)
    {
		// init config in each method
		$this->init_config();

		// check authentication
        $this->check_auth();
		
		$filters					= [];
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']          = (int) $offset;
		$filters['search']          = !empty($_POST['search'])  ? $_POST['search'] : null;
		
        $params = [
            'filters'           => $filters,
			'is_admin'          => $this->AC_SETTINGS->is_admin,
		];
	
		$users  = $this->AC_LIB->addchat_db_lib->get_users($this->AC_SETTINGS->logged_user_id, $params);
		if(empty($users))
	    {
			$data       = array(
                            'users'  	=> array(),
                            'offset'    => 0,
							'more'      => 0,  // to stop load more process
							'status'    => true,
                        );
                        
          return  $this->format_json($data);
        }
        
        $data                       = array();
        $data['users'] 				= $users;
		$data['offset']             = $filters['offset'] == 0 ? $filters['limit'] : $filters['limit']+$filters['offset'];
		$data['more']               = 1;  // to continue load more process
		$data['status'] 			= true;

		return $this->format_json($data);
        
	}

    /*
	* Get messages get_messages
	*/
	public  function get_messages($buddy_id = null, $offset = 0)
	{
		// init config in each method
		$this->init_config();

		// check authentication
        $this->check_auth();

		$buddy_id         			= (int) $buddy_id;
		
		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']          = (int) $offset;

		$params = [
			'chat_user'  => $buddy_id,
			'filters'    => $filters,
			'count'      => false,
		];

		$total_messages 			= $this->AC_LIB->addchat_db_lib->get_messages($this->AC_SETTINGS->logged_user_id, $params);
 		
		// 1st case
		if($filters['offset'] == 0)
			$filters['offset']		= $total_messages > $filters['limit'] ? $total_messages - $filters['limit'] :	0;

			
		else
			$filters['offset']		= $filters['offset'] - $filters['limit'];

		// last case
		$more = 1;
		if($filters['offset'] < 0 || $filters['offset']==0)
		{
			$filters['limit']  		= $filters['limit'] - $filters['offset'];
			$filters['offset'] 		= 0;
			$more = 0;
		}
		
		$params = [
			'chat_user'  => $buddy_id,
			'filters'    => $filters,
			'count'      => true,
		];

		
		$messages 					= $this->AC_LIB->addchat_db_lib->get_messages($this->AC_SETTINGS->logged_user_id, $params);
	
		if(empty($messages))
        {
			$data       = array(
				'messages'  => array(),
				'offset'    => 0,
				'more'      => 0,  // to stop load more process
				'status'    => true,
			);
        	return  $this->format_json($data);
		}

		$params = [
			'buddy_id' => $this->AC_SETTINGS->logged_user_id, 
			'users_id' => $buddy_id
		];
		// remove notification
		$this->AC_LIB->addchat_db_lib->remove_notification($params);

		$data 					= array();
		$data['messages'] 		= array();
		foreach ($messages as $key => $message) 
		{
			$data['messages'][$key]['message_id'] 			= $message->id;
			$data['messages'][$key]['sender'] 				= $message->m_from;
			$data['messages'][$key]['recipient'] 			= $message->m_to;
			$data['messages'][$key]['message'] 				= $message->message;
			$data['messages'][$key]['is_read'] 				= $message->is_read;
			$data['messages'][$key]['dt_updated'] 			= $message->dt_updated;
			
		}
		
		$data['offset']				= $filters['offset'];			
		$data['more']               = $more;  // to continue load more process
		$data['status'] 			= true;
		
		return  $this->format_json($data);
	}


    /*
	* Send message send_message
	*/
	public function send_message(Request $request)
	{
		// init config in each method
		$this->init_config();

		// check authentication
        $this->check_auth();

		// input and validate
		$validator = Validator::make($request->all(), [
			'user'         	=> 'required|numeric|min:1|regex:^[1-9][0-9]*$^',
			'message'       => 'required|max:2000',
		]);
		
		if($validator->fails())
		{
			$data = array('status' => false, 'response'=> $validator->errors()->all());
			return $this->format_json($data);
		}
		
		$buddy 				= (int) $validator->valid()['user'];
		$message 			= nl2br($validator->valid()['message']);

        // return null if buddy or message is empty
        if(!$message || !$buddy)
            return $this->format_json(['status' => false, 'response' => 'N/A']);

        $params    = [
            "m_from"		=> $this->AC_SETTINGS->logged_user_id,
            "m_to" 			=> $buddy,
            "message" 		=> $message,
            "dt_updated" 	=> date('Y-m-d H:i:s'),
        ];
        
        $msg_id = $this->AC_LIB->addchat_db_lib->send_message($params);

        $chat = array(
            'message_id' 		=> $msg_id,
            'sender' 			=> $params['m_from'], 
            'recipient' 		=> $params['m_to'],
            'message' 			=> $params['message'],
            'dt_updated' 		=> $params['dt_updated'],
            'is_read' 			=> 0,
        );

        //  set_notification
        $params  = [
            'users_id' => $this->AC_SETTINGS->logged_user_id, 
            'buddy_id' => $buddy
        ];
        $this->AC_LIB->addchat_db_lib->set_notification($params);

		$data = array(
            'status' 	=> true,
            'message' 	=> $chat 	  
        );

		return $this->format_json($data);
	}

    /*
	* Delete chat history delete_chat
	*/
	public function delete_chat($user_id = null)
	{
		// init config in each method
		$this->init_config();

		// check authentication
        $this->check_auth();

		$user_id = (int) $user_id;
        if(empty($user_id))
        	return $this->format_json(array('status' => false, 'response'=> __('addchat::ac.user').' '.__('addchat::ac.not_found')));
		
		
		$params = [
			'user_id' => $user_id,
		];

		$data					= array();
		$data['status'] 		= $this->AC_LIB->addchat_db_lib->delete_chat($this->AC_SETTINGS->logged_user_id, $params);

		return $this->format_json($data);
	}

	/*
	* Update profile profile_update
	*/
    public function profile_update(Request $request)
    {
		// init config in each method
		$this->init_config();

		// check authentication
        $this->check_auth();
		
		// input and validate
		$validator = Validator::make($request->all(), [
			'status'        => 'required',
			'fullname'   	=> 'required',
			'user_id'       => 'required|numeric|min:1|regex:^[1-9][0-9]*$^',
			'image'         => 'image|mimes:jpg,JPG,jpeg,JPEG,png,PNG|max:5000000|nullable',
		]);
		
		if($validator->fails())
		{
			$data = array('status' => false, 'response'=> $validator->errors());
			return $this->format_json($data);
		}
		
		// upload user image
		$filename               = null;
		if(!empty($validator->valid()['image'])) // if image 
        {
			$file  				= $validator->valid()['image'];
			$filename           = time().rand(1,988).".".$file->getClientOriginalExtension();
			$file->move($this->AC_SETTINGS->img_upload_path."/", $filename);
        }

		$params					= array();
		$params['status']		= $validator->valid()['status'];
		$params['fullname']		= $validator->valid()['fullname'];
		$params['user_id']		= $validator->valid()['user_id'];
		$params['dt_updated'] =  date("Y-m-d H:i:s");


		if(!empty($filename))
			$params['avatar'] = $filename;
		
		// update user status
		$status           =  $this->AC_LIB->addchat_db_lib->update_user($this->AC_SETTINGS->logged_user_id, $params);	

		if($status)
			return $this->format_json($this->get_profile(true));
	}
    
    /*
    * Get realtime updates of messages get_updates
    */
    public function get_updates(Request $request)
	{
		// init config in each method
		$this->init_config();

		// check authentication
        $this->check_auth();

		$notification 	= $this->AC_LIB->addchat_db_lib->get_updates($this->AC_SETTINGS->logged_user_id);
		
		// stop sending notification if in case of same notification
		// get POST data
		$post  = $request->all();
		$is_same = false;
		if(!empty($post['notification']))
			if($post['notification'] == json_encode($notification))
				$is_same = true;

				
		// if no messages then do nothing
	    if(empty($notification) || $is_same)
	   		return	$this->format_json(array('status' => false, 'response'=> 'N/A'));

		return  $this->format_json(array('status' => true, 'notification' => $notification));
	}
	
	/*
    * Get latest message of active buddy
    */
    public function get_latest_message($buddy_id = null)
	{
		// init config in each method
		$this->init_config();

		// check authentication
        $this->check_auth();

		$buddy_id = (int) $buddy_id;
		$messages 	= array();
		if($buddy_id)
		{
			$params  =  [
				'buddy_id'	=> $buddy_id,
			];
			$messages 	= $this->AC_LIB->addchat_db_lib->get_latest_message($this->AC_SETTINGS->logged_user_id, $params);

			// if any new message then remove the specific notification
			// remove notification
			$params = [
				'buddy_id' => $this->AC_SETTINGS->logged_user_id, 
				'users_id' => $buddy_id
			];
			$this->AC_LIB->addchat_db_lib->remove_notification($params);

		}

		// if no messages then do nothing
	    if(empty($messages))
	   		return  $this->format_json(array('status' => false, 'response'=> 'N/A'));

		return  $this->format_json(array('status' => true, 'messages' => $messages));
	}
    
    
    /**
	 *  message delete
	 */
	public function message_delete($message_id = null)
	{
		// init config in each method
		$this->init_config();

		// check authentication
        $this->check_auth();

		$message_id = (int) $message_id;
		if(empty($message_id))
			return $this->format_json(array('status' => false));
            
		$status  	= $this->AC_LIB->addchat_db_lib->message_delete($message_id, $this->AC_SETTINGS->logged_user_id);
		if($status)
			return $this->format_json(array('status' => true, 'message' => __('addchat::ac.message').' '.__('addchat::ac.deleted')));
		
		return $this->format_json(array('status' => false, 'message'=> __('addchat::ac.delete').' '.__('addchat::ac.fail')));
	}



    /* -------- Admin APIs -------- */

	/*
 	 * check admin authentication
     * 
     */
    public function check_admin($is_return = false)
    {
		// init config in each method
		$this->init_config();
		
		if($this->AC_SETTINGS->is_admin !== $this->AC_SETTINGS->logged_user_id)
			return $this->format_json(array('status' => false));

		if(!$is_return)
			return $this->format_json(array('status' => true));

		return true;
	}
	

    /**
	*	Save settings
	*/
	public function save_settings(Request $request)
	{
		// init config in each method
		$this->init_config();

		//check admin authentication
		$this->check_admin(true);

        if(env('DEMO_MODE', 0))
        {
            $data = array('status' => false, 'response'=> 'DEMO MODE');
			return $this->format_json($data);
        }

		// input and validate
		$validator = Validator::make($request->all(), [
			'site_name'				=> 'required',
			'admin_user_id' 		=> 'required|numeric|min:1|regex:^[1-9][0-9]*$^',
			'pagination_limit'      => 'required|numeric|min:1|regex:^[1-9][0-9]*$^',
			'img_upload_path'		=> 'string|required',
			'assets_path'			=> 'string|required',
			'users_table'			=> 'string|required',
			'users_id'				=> 'string|required',
			'users_email'			=> 'string|required',
			'notification_type'     => 'numeric',
			'footer_text'			=> 'string|nullable',
			'footer_url'			=> 'string|nullable',
		]);
		
		if($validator->fails())
		{
			$data = array('status' => false, 'response'=> $validator->errors()->all());
			return $this->format_json($data);
		}

		// upload site log	
		$filename               = null;
		if(!empty($request->image)) // if image 
        {
			$file  				= $request->image;
			$filename           = time().rand(1,988).".".$file->getClientOriginalExtension();
			$file->move($this->AC_SETTINGS->img_upload_path."/", $filename);
		}
		
		// site logo
		if(!empty($filename))
			$params['site_logo'] = $filename;

		// upload site log	
		$chat_icon               = null;
		if(!empty($request->chat_icon)) // if image 
        {
			$file  				= $request->chat_icon;
			$chat_icon          = time().rand(1,988).".".$file->getClientOriginalExtension();
			$file->move($this->AC_SETTINGS->img_upload_path."/", $chat_icon);
		}
		
		$data 						= array();

		$data['site_name']			= $validator->valid()['site_name'];
		$data['footer_text']	    = $request->footer_text;
		$data['footer_url']	        = $request->footer_url;
		$data['admin_user_id']		= $validator->valid()['admin_user_id'];
		$data['pagination_limit']	= $validator->valid()['pagination_limit'];
		$data['img_upload_path']	= $validator->valid()['img_upload_path'];
		$data['assets_path']		= $validator->valid()['assets_path'];
		$data['users_table']		= $validator->valid()['users_table'];
		$data['users_col_id']		= $validator->valid()['users_id'];
		$data['users_col_email']	= $validator->valid()['users_email'];
		$data['notification_type'] 	= $request->notification_type; 
		
		// chat icon
		if(!empty($chat_icon))
			$data['chat_icon'] = $chat_icon;

		// site logo
		if(!empty($filename))
			$data['site_logo'] = $filename;
		
		$params     				= $data;
		$status    					= $this->AC_LIB->addchat_db_lib->save_settings($params);

		return $this->format_json(array('status' => $status));
	}

	/**
	 *  get chat users who chat with each other means between users
	 * 
	 */
	public function a_chat_between($offset = 0)
	{
		// init config in each method
		$this->init_config();

		//check admin authentication
		$this->check_admin(true);

		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']    		= (int) $offset;

		$params	=	[
			'filters' => $filters,
		];

		$chat_betweens 	= $this->AC_LIB->addchat_db_lib->a_chat_between($params);
		
		
		if(empty($chat_betweens))
		{
			$data       = array(
				'chat_betweens'  	=> array(),
				'offset'    		=> 0,
				'more'      		=> 0,  // to stop load more process
				'status'    		=> true,
			);
			return $this->format_json($data);
		}

		$data = array(
			'status' 				=> true,
			'offset'    			=> $filters['offset'] == 0 ? $filters['limit'] : $filters['limit']+$filters['offset'],
			'more'      			=> 1,  // to stop load more process
			'chat_betweens' 		=> $chat_betweens,
		);

		return $this->format_json($data);
	}

	/**
	 *   get conversation of between to  users
	 */
	public function a_get_conversations($m_from = null, $m_to = null, $offset = 0)
	{
		// init config in each method
		$this->init_config();	
		//check admin authentication
		$this->check_admin(true);

		$m_from			= (int) $m_from;
		$m_to			= (int)	$m_to;

		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']          = (int) $offset;

		$params	=	[
			'm_from'  => $m_from, 
			'm_to'    => $m_to, 
			'filters' => $filters, 
			'count'   => true,
		];

		$total_messages 			= $this->AC_LIB->addchat_db_lib->a_get_conversations($params);
	
		// 1st case
		if($filters['offset'] == 0)
			$filters['offset']		= $total_messages > $filters['limit'] ? $total_messages - $filters['limit'] :	0;

			
		else
			$filters['offset']		= $filters['offset'] - $filters['limit'];

		// last case
		$more = 1;
		if($filters['offset'] < 0 || $filters['offset']==0)
		{
			$filters['limit']  		= $filters['limit'] - $filters['offset'];
			$filters['offset'] 		= 0;
			$more = 0;
		}

		$params	=	[
			'm_from'  => $m_from, 
			'm_to'    => $m_to, 
			'filters' => $filters, 
			'count'   => false,
		];

		$conversations 	= $this->AC_LIB->addchat_db_lib->a_get_conversations($params);

		if(empty($conversations))
        {
			$data       = array(
				'conversations'  => array(),
				'offset'    	 => 0,
				'more'      	 => 0,  // to stop load more process
				'status'         => true,
			);
          	return  $this->format_json($data);
		}
	
		$data       = array(
			'conversations'  	=> $conversations,
			'status'    		=> true,
			'more'				=> $more,	// to continue load more process
			'offset'			=> $filters['offset'],
		);
		return $this->format_json($data);
	}

	
	

     


    /* ========== PRIVATE HELPER FUNCTIONS ==========*/

    private function init_config()
	{
		// get settings
		$this->AC_SETTINGS  				= session('ac_session');

        // get the logged-in user
		$this->AC_SETTINGS->logged_user_id  = Auth::id();
		
		// get the admin user
		$this->AC_SETTINGS->admin_user_id 	= (int) $this->AC_SETTINGS->admin_user_id;
		$this->AC_SETTINGS->is_admin 		= $this->AC_SETTINGS->admin_user_id === $this->AC_SETTINGS->logged_user_id ? 1 : 0;
	}
    
    // response in json
	private function format_json($data = array())
	{
		return response($data, Response::HTTP_OK);
	}

    /**
     * Check if user logged in
    */
    private function check_auth()
    {
        if(!$this->AC_SETTINGS->logged_user_id) 
    		return $this->format_json(array('status' => false, 'response'=> __('addchat::ac.access_denied')));

        return true;
    }

}

