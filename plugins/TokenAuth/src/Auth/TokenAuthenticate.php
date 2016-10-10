<?php

namespace TokenAuth\Auth;

use Cake\Auth\FormAuthenticate;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\I18n\Time;

class TokenAuthenticate extends FormAuthenticate
{
    public function getUser(Request $request){
    	if(!$request->query('token'))
    		return false;
    	$table = TableRegistry::get($this->_config['userModel']);
    	$user = $table->findByToken($request->query('token'))->first();
    	if(!$user)
    		return false;
    	return $user->toArray();
    }

    public function authenticate(Request $request, Response $response){
    	$user = parent::authenticate($request,$response);
    	if(!$user){
    		return $user;
    	}

    	$table = TableRegistry::get($this->_config['userModel']);
    	$entity = $table->get($user[$table->primaryKey()]);

    	$entity->token = $token = sha1(Text::uuid());
        $entity->token_created = $token_created = Time::now();
    	unset($entity->{$this->_config['fields']['password']});

    	if(!$table->save($entity)){
    		return false;
    	}
        $user['token'] = $token;
        $user['token_created'] = $token_created;
    	return $user;
    }

    public function unauthenticated(Request $request, Response $response){
        $status = 0;
        $response->statusCode(403);
        $error = "You're not authorized";
        $response->body(json_encode(compact('error','status')));
        $response->type('json');
        return $response;
    }

}