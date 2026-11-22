<?php


declare(strict_types=1);

namespace Controllers;

use Core\BaseController;
use Core\Auth;

class HomeController extends BaseController
{
   public function index(): void
{
    if (!Auth::check()) {
        $this->redirect('/login');
    }

   $this->render('home', [
    'title'   => 'LeadFlow CRM',
    'message' => 'Welcome! Your CRM is ready.',
]);

}

}