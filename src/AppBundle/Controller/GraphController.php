<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\City;
use AppBundle\Command\ApiCommand;
use AppBundle\Entity\Temperatur;
use AppBundle\Form\CityType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use GuzzleHttp\Client;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Zend\Json\Expr;

class GraphController extends Controller
{
    /**
     * @Route("/graph/{id}")
     */
    public function indexAction($id)
    {
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneBy(array('id' => $id));
        $temps = $city->getTemperaturs()->getValues();
        $min = [];
        $max = [];
        $current = [];
        foreach ($temps as $temp) {
            array_push($min, (Float)$temp->getMin());
            array_push($max, (Float)$temp->getMax());
            array_push($current, (Float)$temp->getCurrentTemp());
        }
        $data = [
            'min' => implode(",", $min),
            'max' => implode(",", $max),
            'current' => implode(",", $current)
        ];
        return new JsonResponse($data);
    }
}
