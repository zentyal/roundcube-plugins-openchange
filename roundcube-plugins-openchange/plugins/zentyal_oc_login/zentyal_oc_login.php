<?php
require_once(dirname(__FILE__) . '/../zentyal_lib/OpenchangeConfig.php');

class zentyal_oc_login extends rcube_plugin
{
    public $task = 'login';

    //TODO Should be at a config file?
    private $server = '192.168.56.56'; //should be localhost
    private $port = 389;

    private $handle;

    private function debug_msg($string)
    {
        if (OpenchangeConfig::$debugEnabled) {
            fwrite($this->handle, $string);
        }
    }

    function init()
    {
        $this->handle = fopen(OpenchangeConfig::$logLocation, 'a');
        $this->rc = rcmail::get_instance();
        $this->load_config();

        /* we use login after instead the authenticate hook, because, for the
         * "first login check" we will use IMAP instead of ADirectory
         */
        //$this->add_hook('authenticate', array($this, 'authenticate'));
        $this->add_hook('login_after', array($this, 'checkMapiProfile'));
        //to perform a logout it's only needed a GET request to /?_task=logout
    }

    /* Deprecated */
    function authenticate($args)
    {
        $this->debug_msg("Starting the authenticate function\n");

        $args['user'] = get_input_value('_user', RCUBE_INPUT_POST);
        $args['pass'] = get_input_value('_pass', RCUBE_INPUT_POST);
        $args['cookiecheck'] = false; //do not check the cookie consistencie
        $args['valid'] = true; //do not CSRF check

        $bindingSuccessful = false;

        $ldap_conn = ldap_connect($this->server);

        if ($ldap_conn) {
            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

            $bindingSuccessful = ldap_bind($ldap_conn, $user, $pass);
        }

        if ($bindingSuccessful) {
            $this->debug_msg("Ldap bind was correct\n");
        }

        $args['user'] = $bindingSuccessful ? $args['user'] : "";

        return $args;
    }

    // jkerihuel@zempresa2.example.com
    function checkMapiProfile($args)
    {
        $this->debug_msg("Starting the checkMapiProfile function\n");

        $emailParts = explode('@', get_input_value('_user', RCUBE_INPUT_POST), 2);

        $username = $emailParts[0];
        $realm = $emailParts[1];
        $password = get_input_value('_pass', RCUBE_INPUT_POST);
        $profileName = get_input_value('_user', RCUBE_INPUT_POST);
        $pathDB = OpenchangeConfig::$profileLocation;
        $server = OpenchangeConfig::$openchangeServerIP;
        $domain = OpenchangeConfig::$openchangeServerDomain;

        $mapiDB = new MAPIProfileDB($pathDB);
        $profile = $mapiDB->createAndGetProfile($profileName, $username, $password, $domain, $realm, $server);

        if (!$profile) {
            $args['_task'] = "logout";
            $this->debug_msg("Something went wrong. Redirecting to logout task.\n");
        }

        // As we can set $args['task'] and $args['action'] (and other URL params) we can redirect here to
        // wherever we want
        return $args;
    }

    private function is_localhost()
    {
        return $_SERVER['REMOTE_ADDR'] == '::1' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1';
    }
}