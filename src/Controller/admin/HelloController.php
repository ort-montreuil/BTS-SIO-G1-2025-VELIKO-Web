<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HelloController extends AbstractController
{
    #[Route('/hello')]
    public function hello(): Response
    {
        $title ="WebAdmin";
        return $this->render('admin/hello.html.twig', [
            'title' => $title
        ]);


    }

}

