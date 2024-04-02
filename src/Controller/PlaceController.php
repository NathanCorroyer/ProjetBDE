<?php

namespace App\Controller;

use App\Repository\PlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/place', name: 'place_')]
class PlaceController extends AbstractController
{
    #[Route('/informations/{id}', name: 'informations')]
    public function index($id, PlaceRepository $placeRepository): Response
    {
        $place = $placeRepository->find($id);

        if ($place) {
            $adresse = $place->getAdress();;
            $coordinates = $place->getLatitude() . ' | ' . $place->getLongitude();
            $data = array('adresse' => $adresse,
                'coordinates' => $coordinates);
            return new JsonResponse($data);
        } else {
            return new JsonResponse(null);
        }
    }
}
