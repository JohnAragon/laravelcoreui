<?php


namespace App\Libraries;
use App\Libraries\Postmark;
use Illuminate\Support\Facades\Config;

class Email {

	public static function send_email($data){

		Email::validate_email($data);

		$config = config('mail.postmark.BelpiApp');

        $postmark   = new Postmark();
        $postmark->initialize($config);
        $postmark->to($data['to'], $data['name_to']);
        $postmark->subject($data['subject']);
        $postmark->message_html($data['html']);
        $postmark->send();
        
       return $postmark->response_code;
	}

	private static function validate_email($data){
		if(!isset($data['to'])){
			Postmark::show_error('To is no set!');
		}
		if(!isset($data['name_to'])){
			Postmark::show_error('Name To is no set!');
		}
		if(!isset($data['subject'])){
			Postmark::show_error('Subject is no set!');
		}
		if(!isset($data['html'])){
			Postmark::show_error('Html is no set!');
		}
	}

	public static function send_email_with_template($data){

		Email::validate_email_with_template($data);

		$config = config('mail.postmark.BelpiApp');

        $postmark   = new Postmark();
        $postmark->initialize($config);

        $postmark->to($data['to']);
        $postmark->template_id($data['id']);
        $postmark->template_model($data['model']);
        $postmark->send_with_template();

        return $postmark->response_code;
	}

	private static function validate_email_with_template($data){
		if(!isset($data['to'])){
			Postmark::show_error('To is no set!');
		}
		if(!isset($data['id'])){
			Postmark::show_error('Template Id is no set!');
		}
		if(!isset($data['model']) || !is_array($data['model']) || count($data['model']) == 0){
			Postmark::show_error('Data Model is no set!');
		}
	}

}