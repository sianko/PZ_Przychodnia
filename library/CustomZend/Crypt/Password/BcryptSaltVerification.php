<?php

namespace CustomZend\Crypt\Password;

use Zend\Crypt\Password\Bcrypt;

/**
 *  Extension includes new method verifying which includes operation of salt
 */
class BcryptSaltVerification extends Bcrypt
{
    /**
     * Verify if a password (with salt) is correct against an hash value
     *
     * @param  string $password
     * @param  string $hash 
     * @throws Exception\RuntimeException when the hash is unable to be processed
     * @return bool
     */
    public function verify($password, $hash)
    {
        if(empty($this->salt)){
            throw new Exception\InvalidArgumentException(
                    'The salt parameter of bcrypt must be set'
                );
        }
        else {
            $passwordSettings = explode('$', $hash, 4);
            if(!isset($passwordSettings[2])){
                throw new Exception\InvalidArgumentException(
                    'Hash probably is invalid.'
                );
            }
            $this->setCost($passwordSettings[2]);
            
            $bcryptHash = $this->create($password);
            
            if ($bcryptHash === $hash) {
                return true;
            }
          
            return false;
        }
        
        return false;
    }
}