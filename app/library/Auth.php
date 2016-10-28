<?php

/* Feature: using Namespaces instead of Dirs for Efficiency
 * namespace Fanily\Library;
*/

class Auth {

    protected $_di;
    protected $_session;

    function __construct($di) {
        $this->_di = $di;
        $this->_session = $this->_di->get('session');
    }

    public function getAuthIdentity($field = '')
    {
        $identity = $this->_session->get('auth-identity');
        if ($identity) {
            if ($identity['_timestamp'] + $this->_di->config->app->session->cookie_lifetime < time()) {
                $this->deauth();
                return 408;
            } else {
                $identity['_timestamp'] = time();
                $this->updateAuthIdentity($identity);
            }

            if (!empty($field) && $field[0] !== '_') {
                return $identity[$field];
            } else {
                return $identity;
            }
        }
        return 401;
    }

    public function updateAuthIdentity($ai)
    {
        $this->_session->set('auth-identity', $ai);
    }

    public function setAuthIdentity()
    {
        session_regenerate_id(false);
        $this->_session->set('auth-identity', array(
            '_timestamp' => time()
        ));
    }

    public function deauth()
    {
        $this->_session->remove('auth-identity');
        session_regenerate_id(true);
    }

}

