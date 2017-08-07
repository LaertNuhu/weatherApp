<?php

namespace AppBundle\Command;

use AppBundle\Entity\City;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use GuzzleHttp\Client;
use AppBundle\Entity\Temperatur;

class ApiCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:run_api')

            // the short description shown while running "php bin/console list"
            ->setDescription('Sends requests to an api and saves results on db.')
            ->addArgument('interval', InputArgument::REQUIRED, 'In which interval will the api request be sended')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        while (true){
            $this->cities();
            sleep($input->getArgument("interval"));
        }
    }

    protected function cities(){
        $manager = $this->getContainer()->get('doctrine');
        $cities = $manager->getRepository('AppBundle:City')->findAll();

        foreach ($cities as $city){
            $client = new Client();
            $res = $client->request('GET','http://api.openweathermap.org/data/2.5/weather?q='.$city->getName().'&units=metric&appid=18dfe764b05dc2bd3918f4a11980f3b3');
            $data = json_decode($res->getBody()->getContents(), true);

            $temperatures = new Temperatur();
            $temperatures->setMax($data["main"]["temp_max"]);
            $temperatures->setMin($data["main"]["temp_min"]);
            $temperatures->setCity($city);
            $city->addTemperatur($temperatures);

            $em = $manager->getManager();
            $em->persist($city);
            $em->persist($temperatures);
            $em->flush();
        }
    }
}