<?php
namespace application\installer\component;
/**
 * Clay Installer
 *
 * @copyright (C) 2007-2012 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Clay Installer
 */

/**
 * Clay Installer Package Admin Component
 * @author David
 * @todo Move a lot of this to the Setup API or at least split some of it into supplemental apis
 */
class admin extends \Clay\Application\Component {
	
	/**
	 * Security Checkpoint - Login
	 * @return login|redirect
     * @TODO Fetch the sentry config once?
	 */
	public function authenticate(){
		
		# Import our Javascript handler
		\Library('Clay/Scripts');
		# Add jquery to the js handler queue
		\clay\scripts::addApplication('common','jquery/jquery.js','head');
	
		# If an attempt to login, set the security session variable.		
		if(!empty($_POST['passcode'])){
		
			$sentry = \clay::config('sites/installer/sentry');
			$sentry = $sentry['passkey'];
			# We don't verify the passcode, we simply set it. \installer\sentry API verifies authentication
			$_SESSION['csi'] = $this->encrypt(\clay\data\post('passcode','string','string'),$sentry);			
			# Redirect to the main page, the \installer\sentry API will verify the passcode
			\installer\application::redirect('main','view');
		}
		
		if(!empty($_POST['answer'])){
			
			$sentry = \clay::config('sites/installer/sentry');
			$attempt = $this->encrypt($sentry['question'],\clay\data\post('answer','string','string'));			
			if($attempt == $sentry['recovery']) $_SESSION['csi'] = $sentry['token'];
			\installer\application::redirect('admin','view');
		}
		
		# If there is an error or any other message to the User saved, grab it.
		$data['message'] = !empty($_SESSION['msg']) ? $_SESSION['msg'] : '';
		unset($_SESSION['msg']);
		# No menu, specify the page template. FIXME: rename this page template
		$this->page = 'single-column';
		$this->pageTitle = 'Please Authenticate';
		# Fetch the 'question' for password reset option.
		$sentry = \clay::config('sites/installer/sentry');
		# Make the 'question' string available to the template
		$data['question'] = $sentry['question'];
		# Mute the sentry data.
		unset($sentry);
		return $data;
	}
	
	/**
	 * Admin View Action
	 */
	public function view(){
		
		$this->pageTitle = 'Clay Installer Setup';
		$data['data_dir_status'] = is_writable(\clay\DATA_PATH) ? '<span class="inst-accent">is writable</span>' : '<span class="inst-error">is not writable</span>';
		$data['msg'] = !empty($_SESSION['msg']) ? $_SESSION['msg'] : '';
		unset($_SESSION['msg']);
		
		if(!file_exists(\clay\CFG_PATH.'sites/installer/config.php')) {
			
			\installer\application::redirect('admin','setup');
		}
		
		# Fetch the 'question' for password reset option.
		$sentry = \clay::config('sites/installer/sentry');
		# Make the 'question' string available to the template
		$data['question'] = $sentry['question'];
		# Mute the sentry data.
		unset($sentry);
		return $data;
	}
	
	/**
	 * Installer Setup
	 */
	public function setup(){
		
		if(!empty($_POST['initiate'])){
			
			return $this->initiate();
		}
		
		if(!empty($_GET['update'])){

		}
		
		$this->page = 'single-column';
		//$data['data_writable'] =
		$data['error'] = array();
		
		if(!is_writable(\clay\DATA_PATH)){
			
			$data['error']['data'] = 1;
			$data['data_dir_status'] = '<span class="inst-error">is not writable</span>';
			
		} else {
			
			$data['data_dir_status'] = '<span class="inst-accent">is writable</span>';
		}
		
		if(file_exists(\clay\CFG_PATH)) {
			
			if(!is_writable(\clay\CFG_PATH)) {
				
				$data['error']['config'] = 1;
				$data['cfg_dir_status'] = '<span class="inst-error">is not writable</span>';
				
			} else {
				
				$data['cfg_dir_status'] = '<span class="inst-accent">is writable</span>';
			}
			
		} else {
			
			$data['cfg_dir_status'] = '<span class="inst-accent">will be created by the Installer</span>';
		}
		
		$data['msg'] = !empty($_SESSION['msg']) ? $_SESSION['msg'] : '';
		unset($_SESSION['msg']);
		
		if(!file_exists(\clay\CFG_PATH.'sites/installer/config.php')){
			
			$data['initial'] = true;
		}
		
		return $data;
	}
	
	/**
	 * Proxy function for directing post requests
	 * @return requested private method
	 */
	public function edit($option){
		
		# Form should specify the method with a hidden input field
		$option = \clay\data\post('edit','string','base');
		$method = 'edit'.$option;
		return $this->$method();
	}
	
	/**
	 * Edit the Passcode - API
	 * @return redirect to previous page
	 */
	private function editPasscode(){
		
		$pass = \clay\data\post('pass','string','string');
		$key = \clay\data\post('key','string','string',md5(time()));
    	$passconf = \clay\data\post('passconf','string','string');    	
    	$sentry = \clay::config('sites/installer/sentry');    	
    	$sentry['passkey'] = $key;
    	$sentry['token'] = $this->encrypt($pass,$key);
    	
		if(!empty($pass) && !empty($passconf) && $pass == $passconf){
			
	    	if(!file_exists(\clay\CFG_PATH)){
	    		
	    		mkdir(\clay\CFG_PATH);
	    	}
	    	
			if(!file_exists(\clay\CFG_PATH.'sites')){
				
				mkdir(\clay\CFG_PATH.'sites');
			}
			
			if(!file_exists(\clay\CFG_PATH.'sites/installer')){
				
				mkdir(\clay\CFG_PATH.'sites/installer');
			}
			
			if(\clay::setConfig('sites/installer/sentry',$sentry)){
				
				$_SESSION['msg'] = 'Password has been successfully set.';
				
			} else {
				
				$_SESSION['msg'] = 'Unable to set a password.';
			}

    	} else {
    		
    		$_SESSION['msg'] = 'Passwords did not match. Please enter the same password in each field.';
    	}
    	
    	\clay::redirect($_SERVER['HTTP_REFERER']);
	}
	
	/**
	 * Edit the Passcode Recovery - API
	 * @return redirect to previous page
	 */
	private function editPasscodeRecovery(){
		
		$question = \clay\data\post('question','string','string');
		$answer = \clay\data\post('answer','string','string');
    	$sentry = \clay::config('sites/installer/sentry');
    	
		if(!empty($question) && !empty($answer)){
			
			$sentry['question'] = $question;
			$sentry['recovery'] = $this->encrypt($question,$answer);
			
	    	if(!file_exists(\clay\CFG_PATH)){
	    		
	    		mkdir(\clay\CFG_PATH);
	    	}
	    	
			if(!file_exists(\clay\CFG_PATH.'sites')){
				
				mkdir(\clay\CFG_PATH.'sites');
			}
			
			if(!file_exists(\clay\CFG_PATH.'sites/installer')){
				
				mkdir(\clay\CFG_PATH.'sites/installer');
			}
			
			if(\clay::setConfig('sites/installer/sentry',$sentry)){
				
				$_SESSION['msg'] = 'Password Recovery has been successfully set.';
				
			} else {
				
				$_SESSION['msg'] = 'Unable to set Password Recovery.';
			}

    	} else {
    		
    		$_SESSION['msg'] = 'Please do not use a blank Question or Answer.';
    	}
    	
    	\clay::redirect($_SERVER['HTTP_REFERER']);
	}

	private function initiate(){
		
		# We're redirecting, so we don't want template() to load anything
		$this->template = NULL;
		# TODO: move this to Setup API
		if(!file_exists(\clay\CFG_PATH)){
		
			mkdir(\clay\CFG_PATH);
		}
		
		if(!file_exists(\clay\DATA_PATH.'backups')){
			
			mkdir(\clay\DATA_PATH.'backups');
		}
		
		if(!file_exists(\clay\DATA_PATH.'backups/restore')){
			
			mkdir(\clay\DATA_PATH.'backups/restore');
		}
		
		if(!file_exists(\clay\DATA_PATH.'backups/restore/sites')){
			
			mkdir(\clay\DATA_PATH.'backups/restore/sites');
		}
		
		if(!file_exists(\clay\CFG_PATH.'sites')){
			
			mkdir(\clay\CFG_PATH.'sites');
		}
		
		if(!file_exists(\clay\CFG_PATH.'sites/installer')){
			
			mkdir(\clay\CFG_PATH.'sites/installer');
		}
		
		\clay\application_library('installer','setup');
		$config = \Clay\Application::API('installer','setup','config');
		
		if(!\clay::setConfig('sites/installer/config',$config)){
			
			throw new \Exception('The Installer was unable to write its configuration data file. Please ensure the server can write to '.\clay\CFG_PATH);
		}
		
		$pass = \clay\data\post('pass','string','string');
		$key = \clay\data\post('key','string','string',md5(time()));
    	$passconf = \clay\data\post('passconf','string','string');    	
    	$question = \clay\data\post('question','string','string');
    	$answer = \clay\data\post('answer','string','string');
    	$array = array('passkey' => $key, 'token' => $this->encrypt($pass,$key), 'question' => $question, 'recovery' => $this->encrypt($question,$answer));
		
		if(!empty($pass) && !empty($passconf) && $pass == $passconf){
			
			if(\clay::setConfig('sites/installer/sentry',$array)){
				
				$_SESSION['msg'] = 'Password has been successfully set.';
				
			} else {
				
				$_SESSION['msg'] = 'Unable to set a password.';
			}

    	} else {
    		
    		$_SESSION['msg'] = 'Passwords did not match. Please enter the same password in each field.';
    	}
    	
    	\installer\application::redirect('admin','authenticate');
	}
	
	# TODO: Check for algo availability and choose the strongest available.
	private function encrypt($data,$key){
		
		return hash_hmac('sha512', $data , $key);
	}
	
	/**
	 * Admin Logout
	 * @return logout|redirect
	 */
	# XXX: Perhaps we should just terminate the session?
	public function logout(){
		
		if(!empty($_POST['logout_confirm'])){
			
			unset($_SESSION['csi']);
			\installer\application::redirect('admin','authenticate');
		}
		
		$this->pageTitle = 'Signing out?';
	}
}