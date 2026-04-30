<?php 
namespace Utils;

class User {
    public $username;
    public $email;
    public $password;
    
     function __construct($username, $email, $password) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

     function getProfile() {
        return "User: " . $this->username . "<br>" .
               "Email: " . $this->email . "<br>" .
               "Password: " . $this->password;
    }
}
?>