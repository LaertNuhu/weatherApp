<?php

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use AppBundle\Command\ApiCommand;
use AppBundle\Entity\Temperatur;
use AppBundle\Form\CityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use GuzzleHttp\Client;

class DefaultController extends Controller
{


    public function apiAction()
    {
        $command = new ApiCommand();
        $command->setContainer($this->container);
        $input = new ArrayInput(array('interval' => 3));
        $output = new NullOutput();
        $resultCode = $command->run($input, $output);
        return $resultCode;
    }


    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        //$this->apiAction();
        $cities = $this->getDoctrine()->getRepository('AppBundle:City')->findAll();
        if ($cities){
            return $this->render('default/index.html.twig',['cities'=>$cities]);
        }
        return $this->redirect("/search");
    }

    /**
     * @Route("/search",name="search")
     */
    public function  cityAction(Request $request){

        $city = new City();
        $form = $this->createForm(CityType::class , $city);
        $form->handleRequest($request);
        $cityname = $form['name']->getData();

        if($form->isSubmitted() && $form->isValid()){
            if($this->getDoctrine()->getRepository('AppBundle:City')->findOneBy(array('name' => $cityname))){
                return $this->redirect("/city/".$this->getDoctrine()->getRepository('AppBundle:City')->findOneBy(array('name' => $cityname))->getId());
            }
            $city->setName($cityname);
            $em = $this->getDoctrine()->getManager();
            $em->persist($city);
            $em->flush();
            return $this->redirect("/");
        }
        return $this->render('weather/search.html.twig',['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @Route("/city/{id}" , name="cityDetail")
     */
    public function cityDetailAction(Request $request, $id){
        /*$client = new Client();
        $res = $client->request('GET','http://api.openweathermap.org/data/2.5/weather?q='.$this->getDoctrine()->getRepository('AppBundle:City')->findOneBy(array('id' => $id))->getName().'&units=metric&appid=18dfe764b05dc2bd3918f4a11980f3b3');
        $data = json_decode($res->getBody()->getContents(), true);
        */
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneBy(array('id' => $id));
      /*  $temperatures = new Temperatur();
        $temperatures->setMax($data["main"]["temp_max"]);
        $temperatures->setMin($data["main"]["temp_min"]);

        $temperatures->setCity($city);
        $city->addTemperatur($temperatures);

        $em = $this->getDoctrine()->getManager();
        $em->persist($city);
        $em->persist($temperatures);
        $em->flush();
*/
        $params = [
            'name'=> $city->getName(),
            'temperatures'=>$city->getTemperaturs()->getValues()
        ];
        dump($params);
        return $this-> render('weather/detail.html.twig',$params);
    }

    /**
     * @Route("/delete/{id}")
     */
    public function deleteCity($id)
    {
        $em = $this->getDoctrine()->getManager();
        $city = $em->getRepository("AppBundle:City")->find($id);

        $em->remove($city);
        $em->flush();
        return $this
            -> redirectToRoute('homepage');
    }
}
