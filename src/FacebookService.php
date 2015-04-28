<?php

namespace MyFrameWork;

require_once FACEBOOK_SDK_V4_SRC_DIR . 'autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;

class FacebookService {
    /**
     *
     * @var Session
     */
    protected $session;
    /**
     *
     * @var FacebookRedirectLoginHelper
     */
    protected $loginHelper;
    /**
     *
     * @var FacebookSession 
     */
    protected $fbsession;
    /**
     *
     * @var string 
     */
    protected $appId = '1510316385897144';
    /**
     *
     * @var string 
     */
    protected $appSecret = 'e03a3c372cd2dce9e46f1d34bd3551bd';
    /**
     *
     * @var string 
     */
    protected $redirectUrl;
    /**
     * Last Error description
     * @var string 
     */
    protected $error;
    /**
     * Required dependencies
     * @param Session $session
     */
    public function __construct(Session $session) {
        $this->session = $session;
    }
    
    /**
     * Set some required settings
     */
    public function init() {
        FacebookSession::setDefaultApplication($this->appId, $this->appSecret);
        $this->loginHelper = new FacebookRedirectLoginHelper($this->getRedirectURL(), $this->appId, $this->appSecret);
    }
    
    /**
     * 
     * @param FacebookSession $session
     * @return \FacebookService
     */
    public function setFbSession(FacebookSession $session = null) {
        FacebookSession::setDefaultApplication($this->appId, $this->appSecret);
        $this->fbsession = $session ?: FacebookSession::newAppSession($this->appId, $this->appSecret);
        return $this;
    }
    
    /**
     * Returns the URL to send the user in order to log out of Facebook.
     * @return string|bool The url to log out
     */
    public function end() {
        if(is_null($this->fbsession)) {
            return false;
        }
        return $this->fbsession->getFacebookLoginHelperInstance()->getLogoutUrl(FacebookSession::newAppSession(), "/");
    }
    
    /**
     * Ask for a facebook session 
     * @return boolean
     */
    public final function hasLoggedIn() {
        if(is_null($this->fbsession) and !is_null($this->session)) {
            return $this->session->getData("_facebookSession") ?: false;  
        }
        return $this->fbsession->validate();
    }
    
    /**
     * Attempt get a facebook session from a redirect
     * It should be called after a redirect in to facebook.com 
     * @return boolean
     */
    public function canGetASession() {
        try {
            $session = $this->loginHelper->getSessionFromRedirect();
            $this->fbsession = $session;
        } catch(FacebookRequestException $ex) {
            // When Facebook returns an error
            $this->error = $ex->getMessage();
            return false;
        } catch(\Exception $ex) {
            // When validation fails or other local issues
            $this->error = $ex->getMessage();
            return false;
        }
        //Only get here case the loginHelper return NULL
        if (empty($this->fbsession)) {
            $this->error = "An error has ocurred on attempt sing in on the facebook.com, try again later";
            return false;
        }
        $this->session->setData("_facebookSessionCode", filter_input(INPUT_GET, "code"));
        //$_SESSION['_userid'] = $id;
        //$_SESSION['_groups'] = $groups;
        return true;
    }
    
    /**
     * Redirect to generated url 
     */
    public function redirectToGeneratedUrl() {
        //permissions
        $scopes = array(
            "read_friendlists",
            "manage_friendlists",
            "publish_actions",
            "user_groups",
            "user_friends",
            "user_about_me",
            "user_location",
            "user_birthday",
            "email"
        );
        $loginUrl = $this->loginHelper->getLoginUrl($scopes);
        redirect($loginUrl);
    }
    
    /**
     * 
     * @param array $params
     * @return mixed In success should return a FacebookResponse, else either message error or false boolean value
     */
    public function postInTimeLine(array $params) {
        if($this->fbsession) {
            try {
                $request = new FacebookRequest(
                    $this->fbsession,
                    'POST',
                    '/me/feed',
                    $params
                );
                return $request->execute();
            }catch(FacebookSDKException $e) {
                $this->error = $e->getMessage();
            }
        }
        return false;
    }
    
    /**
     * 
     * @param array $params
     * @throws Exception If fbsession is null
     */
    public function friendList($params = array()) {
        if(empty($this->fbsession)) {
            throw new Exception("fbsession should not be empty");
        }
        $response = $this->executeFbRequest($this->fbsession, "/me?fields=friends", $params);
        return $response->getGraphObject()->asArray();
    }
    
    public function groups() {
        if(empty($this->fbsession)) {
            throw new Exception("fbsession should not be empty");
        }
        $response = $this->executeFbRequest($this->fbsession, "/me/groups");
        return $response->getGraphObjectList();
    }
    
    /**
     * 
     * @return mixed In success should return GraphUser else a string error message
     * @throws Exception
     */
    public function getUser() {
        if(empty($this->fbsession)) {
            throw new Exception("fbsession should not be empty");
        }
        try{
            $response = $this->executeFbRequest($this->fbsession, "/me?fields=id,name,email,gender,picture,birthday,location");
            if(empty($response)) {
                throw new Exception("An error has occurred on facebook request, try again later!");
            }
            return $response->getGraphObject(GraphUser::className());
        }catch(\Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        } 
    }
    
    /**
     * The base method for all requests
     * @param FacebookSession $session
     * @param string $node
     * @return boolean|FacebookResponse
     */
    private function executeFbRequest($session, $node = "/me", $params = array()) {
        // Make a new request and execute it.
        try {
            return (new FacebookRequest($session, 'GET', $node, $params))->execute();
        } catch (FacebookRequestException $ex) {
            $this->error = $ex->getMessage();
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
        }
        return false;
    }
    
    /**
     * The string access token
     * @return string
     */
    public function getLastAcessToken() {
        return $this->session->getData("_facebookAccessToken");
    }
    
    /**
     * The generated session code by the last login
     * @return string
     */
    public function getFacebookSessionCode() {
        return $this->session->getData("_facebookSessionCode");
    }
    
    /**
     * The URL which shoud be redirected after facebook login process, usually some url from your application
     * @param string $url
     * @return \FacebookService
     */
    public function setRedirectURL($url) {
        $this->redirectUrl = $url;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getRedirectURL() {
        return $this->redirectUrl;
    }
    
    /**
     * Last error occurred
     * @return string
     */
    public function getError() {
        return $this->error;
    }
    
    /**
     * 
     * @return FacebookLoginHelper
     */
    public function getFacebookLoginHelperInstance() {
        return $this->loginHelper;
    }
    
    /**
     * 
     * @return FacebookSession
     */
    public function getActiveFacebookSession() {
        return $this->fbsession;
    }
}


