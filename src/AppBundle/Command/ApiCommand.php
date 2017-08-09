<?php

namespace AppBundle\Command;

use AppBundle\Entity\City;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use GuzzleHttp\Client;
use AppBundle\Entity\Temperatur;

/**
 * Class ApiCommand -> can be called on terminal via php bin/console app:run_api
 *
 * @package AppBundle\Command
 */
class ApiCommand extends ContainerAwareCommand
{
    /**
     * Intial configuration for the command
     * Name and description are given
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:run_api')
            // the short description shown while running "php bin/console list"
            ->setDescription('Sends requests to an api and saves results on db.')
        ;
    }

    /**
     * Makes the executing of the command possible
     *
     * @param  InputInterface  $input  takes user input
     * @param  OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cities();
        sleep(10);
    }

    /**
     * Calls the api for every city and adds data in the databank
     */
    protected function cities()
    {
        $manager = $this->getContainer()->get('doctrine');
        $cities = $manager->getRepository('AppBundle:City')->findAll();
        foreach ($cities as $city) {
            $client = new Client();
            $res = $client->
                    request(
                        'GET',
                        'http://api.openweathermap.org/data/2.5/weather?q='
                        .$city->getName().'&units='
                        .$this->getContainer()->getParameter('units')[0].'&appid='
                        .$this->getContainer()->getParameter('open_weather_api')
                    );
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

            $em = $manager->getManager();
            $em->persist($city);
            $em->persist($temperatures);
            $em->flush();
        }
    }
}
