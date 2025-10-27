<?php

class MyLogin {
    private $ses;
    private const SESSION_KEY = 'user_id';
    private const KEY_NAME = 'user_name';
    private const KEY_DATE = 'user_date';



    public function _construct() {
        require_once("MySessions.class.php");
        $this->ses = new MySession();
    }

    public function isUserLoggedIn(){
        return $this->ses->isSessionSet(self::SESSION_KEY);
    }

    public function login(string $username) {
        $data = [self::KEY_NAME => $username, self::KEY_DATE => date('d. m. Y, G:i:s')];
        $this->ses->setSession(self::SESSION_KEY, $data);
    }

    public function logout() {
        $this->ses->removeSession(self::SESSION_KEY);
    }


    public function getUserInfo() {
        if(!$this->isUserLoggedIn()) {
            return null;
        }
        $d = $this->ses->readSession(self::SESSION_KEY);
        return "Jméno:". $d[self::KEY_NAME]. "<br>"
            . "Datum:" . $d[self::KEY_DATE] . "<br>";
    }
}