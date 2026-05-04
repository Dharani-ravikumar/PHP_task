<?php
   
   function validateUsername($username)
   {
    if(empty($username))
        {
            return "enter the username";
        }
   
    else
        {
            return "";  
        }
   }


   function validateEmail($email)
   {
    if(empty($email))
        {
            return "enter the email";
        }
    else
        {
            return "";   
        }
   }


   function validatePassword($password)
   {
    if(empty($password))
        {
            return "enter the password";
        }
   
    else
        {
            return "";   
        }
   }

?>