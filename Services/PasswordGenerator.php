<?php

namespace ScyLabs\NeptuneBundle\Services;

use ScyLabs\NeptuneBundle\Model\PasswordGeneratorInterface;

class PasswordGenerator implements PasswordGeneratorInterface{
    public function generate(array $opts = []) : string
    {
        $opts['lenght'] = $opts['lenght'] ?? 8;
        $opts['minCapital'] = $opts['minCapital'] ?? 1;
        $opts['minDigit'] = $opts['minDigit'] ?? 1;
        $opts['minSpecial'] = $opts['minSpecial'] ?? 1;

        $chars = "abcdefghijklmnopqrstuvwxyz";
        $caps = strtoupper($chars);
        $nums = "0123456789";
        $syms = "!@#$%^&*()-+?";
        $out = '';
        $out .= $this->select($chars, $opts['lenght'] - $opts['minCapital'] - $opts['minDigit'] - $opts['minSpecial']); // sélectionne aléatoirement les lettres minuscules
        $out .= $this->select($caps, $opts['minCapital']); // sélectionne aléatoirement les lettres majuscules
        $out .= $this->select($nums, $opts['minDigit']); // sélectionne aléatoirement les chiffres
        $out .= $this->select($syms, $opts['minSpecial']); // sélectionne aléatoirement les caractères spéciaux
        
        // Tout est là, on mélange le tout
        return str_shuffle($out);

    }
    private function select($src, $l) : string
    {
        $out = '';
        for($i = 0; $i < $l; $i++){
           $out .= substr($src, mt_rand(0, strlen($src)-1), 1);
        }
        return $out;
     }
}