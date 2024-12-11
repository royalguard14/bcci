<?php 
require_once 'BaseController.php'; 
 
class EnrollmentController extends BaseController { 
    public function __construct($db) { 
        parent::__construct($db, ['13']);  
    } 
 


public function register(){

    include 'views/enrollment/register.php';
}








}//end 
