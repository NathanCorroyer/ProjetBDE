<?php

namespace App\Controller;

use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/city', name: 'city_')]
class CityController extends AbstractController
{
    #[Route('/zipcode/{id}', name: 'zipcode')]
    public function index($id, CityRepository $cityRepository): Response
    {
        $city = $cityRepository->find($id);

        if($city) {
            $zipcode = $city->getZipCode();;
        }
        return new Response($zipcode);
    }
}
