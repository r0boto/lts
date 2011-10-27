<?php

class UsersController extends AppController {

	var $name = 'Users';

var $uses = array('User');


	var $components = array('Session','Email','RequestHandler','Uploader.Uploader','Cookie');

function beforeFilter() {
  $this->Cookie->name = 'teacher';
  $this->Cookie->time =  3600;  // or '1 hour'
  $this->Cookie->path = '/';
  //$this->Cookie->domain = 'localhost';
  $this->Cookie->secure = true;  //i.e. only sent if using secure HTTPS
  $this->Cookie->key = 'qSI232qs*&sXOw!';
}

	//REGISTER A NEW USER, SEND CONFIRMATION EMAIL AND RETURN VALUE
	function register() {
		//$this->autoLayout = false;
		//$this->view = 'Media';
		$userEmail  = $this->params['url']['email'];
		$userPassword  = $this->params['url']['password'];
		$deviceId = $this->params['url']['deviceid'];
		$appLanguage = $this->params['url']['applanguage'];

		//IS USER ALREDY EXIST IN DB?
		$isAlreadyRegistred =
		$this->User->find('count',
		array('conditions' => array('User.email' => $userEmail,'User.deviceid'=> $deviceId)));
       debug($isAlreadyRegistred);

		//IF NOT
		if($isAlreadyRegistred==0)
		{

			//PREPARE DATA FOR SAVE
			$this->data['User']['email'] =  $userEmail;
			$this->data['User']['password'] =  $userPassword;
			$this->data['User']['verifistring'] = sha1($userEmail);
			$this->data['User']['isverified'] =  0;
			$this->data['User']['deviceid'] = $deviceId;
			$this->data['User']['blocked'] = 0;
			$this->data['User']['countofsign'] = 0;
			$this->data['User']['dateofcreate'] = date("Y-m-d H:i:s");
			$this->data['User']['questionsanswered'] = 0;
			$this->data['User']['applanguage'] = $appLanguage;






			if ($this->User->save($this->data)) {
						debug($this->data);

						//GET USER ID
						$userId = $this->User->find('first',array('conditions' => array('User.email' => $userEmail)));

						//GENERATE AND SEND REGISTRATION MAIL
        				$this->Email->from = Configure::read('App.registerEmail');
        				$this->Email->to = $this->data['User']['email'];
        				$this->Email->subject = __('Potvrzení registrace',true);
        				//Template from app/views/elements/email/
        				$this->Email->template = 'registrationConfirmation';
        				$this->Email->sendAs = 'both';
        				//Set values to layout
        				$this->set('email',$this->data['User']['email']);
        				$this->set('hash',$this->data['User']['verifystring']);
						$this->set('userId',$this->data['User']['id']);
						$this->set('appLanguage',$this->data['User']['applanguage']);



						/* SMTP Options */
						$this->Email->smtpOptions = array(
								        'port'=>Configure::read('App.smtpPort'),
								        'timeout'=>Configure::read('App.mailTimeout'),
								        //'host' => Configure::read('App.mailHost')
								        );
								        /* Set delivery method */
								        $this->Email->delivery = 'smtp';




								        $this->Email->send();
								        $smtpError = $this->Email->smtpError;
								        //END SEND MAIL

								        debug($smtpError);


				//AND SET STAUTUS FOR SCRIPT
				$this->set('status', 'true' );
			}

		}

		else {

			//USER ALREADY EXIST
			$this->set('status', 'false' );

		}

	}



	function verifyRegistration()
	{


		$userId  = $this->params['url']['userid'];
		$userEmail  = $this->params['url']['email'];
		$userHash  = $this->params['url']['hash'];
		$deviceId  = $this->params['url']['deviceid'];
		$appLang  = $this->params['url']['applanguage'];

			//IS USER ALREDY EXIST IN DB?
		$isVerified =
		$this->User->find('count',
		array('conditions' => array('User.email' => $userEmail,'User.deviceid'=> $deviceId, 'User.verifistring'=>$userHash, 'User.isverified'=>0)));





       	//IF VERIFY CREDENTIALS ARE TRUE SET THE isverified in DB on 1
        if($isVerified==1)
        {


        $this->data = $isVerified;
		$this->data['User']['isverified'] ="1";

      	if ($this->User->save($this->data))
      	{


      		//debug(APP.Configure::read('App.fileDir').DS.$userId);
      		//CREATE A USER DIR WHRE USER FILES WILL BE STORED
         	mkdir(APP.Configure::read('App.fileDir').DS.$userId,0777);
			//COPY DEFAULT FILES
			copy(APP.Configure::read('App.loginCookieLife').DS.$appLang.'.csv',APP.Configure::read('App.fileDir').DS.$userId.DS.'test.csv');


			//USER UPDATETD
			$this->set('status', 'true' );
      	}


        }

        else
        {




        	//USER ALREADY EXIST
			$this->set('status', 'false' );
        }
	}


	function login ()
	{

		$userEmail  = $this->params['url']['email'];
		$userPassword  = $this->params['url']['password'];

		$isVerified =
		$this->User->find('count',array('conditions' => array('User.email' => $userEmail, 'User.password'=>$userPassword, 'User.isverified'=>1)));

		$userId =
		$this->User->find('all',array('conditions' => array('User.email' => $userEmail, 'User.password'=>$userPassword, 'User.isverified'=>1)));


		if ($isVerified===1)
		{


			debug($userId[0]['User']['id']);
			//LOGIN OK
			debug ($this->Cookie->read('userid'));

			//SET COOKIE
			$this->Cookie->write('userid',$userId[0]['User']['id'],true,3600);
			$this->Cookie->write('loginstatus',0,false);
			$this->Cookie->write('deviceid',$userEmail,false);

			//$this->redirect(array('action' => 'account',$userId[0]['User']['id']));


			//$this->set('status', 'true' );

		}

		else
		{
			//USER NOT EXIST OR BAD E-MAIL OR PASSSWORD
			$this->set('status', 'false' );
		}


	}


	function account($id)
	{

		//VERIFY THAT USER IS is SUCCESSFULLY LOGED IN
		debug($_COOKIE);

		$userInfo =
		$this->User->find('all',array('conditions' => array('User.id' => $id)));
		$this->set('status', 'true' );
	}


	//DEFAULT CAKEPHP FUNCTIONS
	//DEFAULT FUNCT
	function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}


	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for user', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	function admin_index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for user', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
