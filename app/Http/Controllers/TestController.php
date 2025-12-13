<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public $student;
    public $status;

public function __construct($student ,$status){
    $this->student=$student;
     $this->status=$status;
}

public function test(){

return true;
}



}
