<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Composer\Autoload\ClassLoader;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DoctrineBootstrap
{
    private $entityManager;
    public function __construct()
    {
        $config = Setup::createAnnotationMetadataConfiguration([], true);
        $conn=[
            'dbname'=>'dpeuraca_19',
            //'user'=>'root',
            //'password'=>'',
            'user'=>'dpeuraca_19',//, koristiti za server (nemojte zaboraviti zakomentirati gornji user i password)
            'password'=>'FVX8dEyj', //koristiti za server i promijeniti u svoje; stavio sam krivi pass da mi slučajno ne ubacujete stvari u bazu :P
            'host'=>'localhost',
            'driver'=>'pdo_mysql',
            'charset'=>'utf8',
            'driverOptions'=>[1002 => 'set names utf8']
        ];
    $this->entityManager = EntityManager::create($conn,$config);
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function getJson($podaci)
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        return $serializer->serialize($podaci,'json');
    }
}


?>
