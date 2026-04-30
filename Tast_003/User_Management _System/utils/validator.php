<?php 
namespace Utils;
class Validator
{
   function validateUsername($username)
   {
       if (strlen($username)>=3)
        {
            return "valid";
        }
        else
            {
             return "Invalid" ; 
            }
   }

   function validateEmail($email)
   {
    if (strpos($email, "@")) 
        {
        return "Valid";
        } 

    else
        {
            return "Invalid";
        }
   }

   
   function ValidatePassword($password)
   {
    if(strlen($password)>=6)
        {
             return "valid";
        }
        else
            {
             return "Invalid" ; 
            }


   }
}
?>