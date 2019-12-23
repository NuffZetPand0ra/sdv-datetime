<?php
namespace nuffy\SDV;

class Date extends DateTime{
    public function __tooString() : string
    {
        return $this->format("y-m-d");
    }

    public function diff($input){
        return parent::diffDays($input);
    }
}