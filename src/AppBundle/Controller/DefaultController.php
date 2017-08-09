<?php

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use AppBundle\Form\CityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{


    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $cities = $this->getDoctrine()->getRepository('AppBundle:City')->findAll();
        if ($cities) {
            return $this->render('default/index.html.twig', ['cities' => $cities]);
        }
        return $this->redirect("/search");
    }

    /**
     * @Route("/search",name="search")
     */
    public function cityAction(Request $request)
    {
        $cities = $this->getDoctrine()->getRepository('AppBundle:City')->findAll();
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);
        $cityname = $form['name']->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getDoctrine()->getRepository('AppBundle:City')
                ->findOneBy(array('name' => $cityname))
            ) {
                return $this->redirect(
                    "/city/" .
                    $this->getDoctrine()
                        ->getRepository('AppBundle:City')
                        ->findOneBy(array('name' => $cityname))->getId()
                );
            }
            $city->setName($cityname);
            $em = $this->getDoctrine()->getManager();
            $em->persist($city);
            $em->flush();
            return $this->redirect("/");
        }
        return $this->render('weather/search.html.twig', ['form' => $form->createView(),'cities'=>$cities,'url'=>""]);
    }

    /**
     * @param $id
     * @param $value
     * @return array
     * @Route("/graphValues")
     */
    public function graphValues($id, $value)
    {
        $city = $this->getDoctrine()
            ->getRepository('AppBundle:City')->findOneBy(array('id' => $id));
        $temperaturs = $city->getTemperaturs()->getValues();
        $min = [];
        $max = [];
        $current = [];
        foreach ($temperaturs as $temp) {
            array_push($min, (Float)$temp->getMin());
            array_push($max, (Float)$temp->getMax());
            array_push($current, (Float)$temp->getCurrentTemp());
        }
        switch ($value) {
            case "min":
                return $min;
                break;
            case "max":
                return $max;
                break;
            case "current":
                return $current;
                break;
        }
    }

    /**
     * @param Request $request
     * @Route("/city/{id}" , name="cityDetail")
     */
    public function cityDetailAction(Request $request, $id)
    {
        $cities = $this->getDoctrine()->getRepository('AppBundle:City')->findAll();
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneBy(array('id' => $id));
        $cityTemperaturs = $city->getTemperaturs()->getValues();
        $last_10 = array_slice($cityTemperaturs, -10, 10, true);
        $params = [
            'name' => $city->getName(),
            'temperatures' => $last_10,
            'cities'=>$cities
        ];
        if ($cityTemperaturs) {
            $params = [
                'name' => $city->getName(),
                'id' => $city->getId(),
                'temperatures' => $last_10,
                'icon' => end($cityTemperaturs)->getIcon(),
                'cities'=> $cities
            ];
        }

        return $this->render('weather/detail.html.twig', $params);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/delete/{id}")
     */
    public function deleteCity($id)
    {
        $em = $this->getDoctrine()->getManager();
        $city = $em->getRepository("AppBundle:City")->find($id);

        $em->remove($city);
        $em->flush();
        return $this
            ->redirectToRoute('homepage');
    }
}
