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
use Ob\HighchartsBundle\Highcharts\Highchart;
use Zend\Json\Expr;

class DefaultController extends Controller
{
    /**
     * @return int
     */
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

        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);
        $cityname = $form['name']->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getDoctrine()->getRepository('AppBundle:City')->findOneBy(array('name' => $cityname))) {
                return $this->redirect("/city/" . $this->getDoctrine()->getRepository('AppBundle:City')->findOneBy(array('name' => $cityname))->getId());
            }
            $city->setName($cityname);
            $em = $this->getDoctrine()->getManager();
            $em->persist($city);
            $em->flush();
            return $this->redirect("/");
        }
        return $this->render('weather/search.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param $id
     * @param $value
     * @return array
     * @Route("/graphValues")
     */
    public function graphValues($id, $value)
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
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneBy(array('id' => $id));
        /*
        $client = new Client();
        $res = $client->request('GET','http://api.openweathermap.org/data/2.5/weather?q='.$this->getDoctrine()->getRepository('AppBundle:City')->findOneBy(array('id' => $id))->getName().'&units=metric&appid=18dfe764b05dc2bd3918f4a11980f3b3');
        $data = json_decode($res->getBody()->getContents(), true);


        $temperatures = new Temperatur();
        $temperatures->setMax($data["main"]["temp_max"]);
        $temperatures->setMin($data["main"]["temp_min"]);
        $temperatures->setCurrentTemp($data["main"]["temp"]);
        $temperatures->setHumidity($data["main"]["humidity"]);
        $temperatures->setIcon("http://openweathermap.org/img/w/".$data["weather"][0]["icon"].".png");
        $temperatures->setWindSpeed($data["wind"]["speed"]);



        $temperatures->setCity($city);
        $city->addTemperatur($temperatures);

        $em = $this->getDoctrine()->getManager();
        $em->persist($city);
        $em->persist($temperatures);
        $em->flush();
    */

        $cityTemperaturs = $city->getTemperaturs()->getValues();

        $min = [];
        $max = [];
        $current = [];

        foreach ($cityTemperaturs as $cityTemperatur) {
            array_push($min, (Float)$cityTemperatur->getMin());
            array_push($max, (Float)$cityTemperatur->getMax());
            array_push($current, (Float)$cityTemperatur->getCurrentTemp());
        }


        // Chart
        $series = array(
            array("name" => "Min", "data" => $min),
            array("name" => "Max", "data" => $max),
            array("name" => "Current", "data" => $current)
        );

        $ob = new Highchart();
        $ob->chart->renderTo('linechart');  // The #id of the div where to render the chart
        $ob->title->text('Chart Title');
        $ob->xAxis->title(array('text' => "Time"));
        $ob->yAxis->title(array('text' => "Temperature"));
        $ob->series($series);

        $last_10 = array_slice($cityTemperaturs, -10, 10, true);


        $params = [
            'name' => $city->getName(),
            'temperatures' => $last_10,
            'chart' => $ob
        ];

        if ($cityTemperaturs) {
            $params = [
                'name' => $city->getName(),
                'id' => $city->getId(),
                'temperatures' => $last_10,
                'icon' => end($cityTemperaturs)->getIcon(),
                'chart' => $ob
            ];
        }

        return $this->render('weather/detail.html.twig', $params);
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
            ->redirectToRoute('homepage');
    }
}
