<?php
namespace TokenAuth\Shell;

use Cake\Console\Shell;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\Stub\ConsoleOutput;
/**
 * Token shell command.
 */
class TokenShell extends Shell
{

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
		$parser->addSubcommand('refresh', [
		    'help' => __('Refresh token of the users'),
		    'parser' => [
		        'description' => __('Subcommand refresh'),
		        'options' => [
		            'model' => ['help' => __('The Model to use.'), 'required' => true,'default'=>'Users','short'=>'m'],
		            'time' => ['help' => __('The period for which to keep tokens'), 'required' => true,'default'=>'-15 days','short'=>'t'],
		            'colored' => ['help' => __('Make shell coloured'),'boolean'=>true,'default'=>false,'short'=>'c']
		        ]
		    ]
		]);
        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int Success or error code.
     */
    public function main() 
    {
        $this->out($this->OptionParser->help());
    }

    public function refresh(){
		if(!$this->params['colored']){
    		$this->_io->outputAs(ConsoleOutput::PLAIN);
    	}
    	$time = Time::parse($this->params['time'])->i18nFormat('yyyy-MM-dd HH:mm:ss');
    	$model = $this->params['model'];
    	$this->out('<info>Starting to refresh tokens...</info>');
    	$this->out(sprintf("Minimum date for tokens is %s",$time));
    	$usersTable = TableRegistry::get($model);
    	$result = $usersTable->updateAll(
    		['token'=>null,'token_created'=>null],
    		['token_created <= ' => $time]
    	);
    	if($result){
    		$this->out('<success>Operation succeeded</success>');
    	}
    	else{
    		$this->out('<error>Record could not be updated or no record found</error>');
    	}
    }
}
