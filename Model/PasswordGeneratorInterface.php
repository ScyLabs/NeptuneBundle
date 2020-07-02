<?php

namespace ScyLabs\NeptuneBundle\Model;


interface PasswordGeneratorInterface {

    public function generate(array $opts = []) : string;
}