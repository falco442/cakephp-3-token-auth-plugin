<?php

namespace TokenAuth\Auth;

use Cake\Auth\FormAuthenticate;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;

class TokenAuthenticate extends FormAuthenticate
{
    public function getUser(Request $request){
    	if(!$request->query('token'))
    		return false;
    	$table = TableRegistry::get($this->_config['userModel']);
    	$user = $table->findByToken($request->query('token'))->first();
    	if(!$user)
    		return false;
    	return $user;
    }

    public function authenticate(Request $request, Response $response){
    	$user = parent::authenticate($request,$response);
    	if(!$user){
    		return $user;
    	}

    	$table = TableRegistry::get($this->_config['userModel']);
    	$entity = $table->get($user[$table->primaryKey()]);

    	$entity->token = sha1(Text::uuid());
    	unset($entity->{$this->_config['fields']['password']});

    	if(!$table->save($entity)){
	    		$user = false;
    	}
    	return $user;
    }

}