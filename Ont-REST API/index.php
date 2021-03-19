<?php
require 'vendor/autoload.php';
require 'bootstrap.php';

use Peuraca\Ontologija; 
use Peuraca\Projekt;
use Composer\Autoload\ClassLoader;

Flight::route('/', function(){

  $foaf = \EasyRdf\Graph::newAndLoad('https://oziz.ffos.hr/nastava20192020/dpeuraca_19/ontologija/peuraca.rdf'); // ovdje ide link na vašu oziz rdf datoteku (kad je stavite preko filezille)
  $info = $foaf->dump();
  echo "<h2>Ontologija P3 zadatka:</h2> <br/><br/>" . $info;
});

Flight::route('GET /search', function(){

  $doctrineBootstrap = Flight::entityManager();
  $em = $doctrineBootstrap->getEntityManager();
  $repozitorij=$em->getRepository('Peuraca\Ontologija');
  $zapisi = $repozitorij->findAll();
  echo $doctrineBootstrap->getJson($zapisi);
});


Flight::route('GET /fill_table', function(){

  //$foaf
  $foaf = \EasyRdf\Graph::newAndLoad('https://oziz.ffos.hr/nastava20192020/dpeuraca_19/ontologija/peuraca.rdf');
  foreach ($foaf->resources() as $resource) {
    if($foaf->get($resource, 'dc:hasFormat') != ''){
 
    
    $i = 0;
      $types[] = [];
      $annotations = "";
     
   
      $format = $foaf->get($resource, 'dc:hasFormat');
      $formaturl = parse_url($format);
$format2 = $formaturl["fragment"];
      echo $format2;

      $author = $foaf->get($resource, 'dc11:author');
      echo $author;
      $duration = $foaf->get($resource, 'dc11:duration');
      echo $duration;
      $title = $foaf->get($resource, 'rdfs:label');
      echo $title;
      
   
         $description = $foaf->get($resource, 'dc:description'); // kako izvući dc:description resursa
  // sve ispisuje
        foreach ($resource->properties() as $key) {
            $annotations .= $key . ': ' . $foaf->get($resource, $key) . "\n"; // kako izvući sve anotacije resursa i spojiti u jedan string; "\n" služi da bi u androidu radio newline za svaku anotaciju
         }
       
         $ontologija = new Ontologija();
           $ontologija->setPodaci(Flight::request()->data);
    
           $ontologija->setNaslov($title); //tu stavljate svoje varijable.
           $ontologija->setTip($format2);
           $ontologija->setDuzina($duration);
           $ontologija->setAutor($author);
     
           $doctrineBootstrap = Flight::entityManager();
            $em = $doctrineBootstrap->getEntityManager();
     
           $em->persist($ontologija);
            $em->flush();


    }
  }

}



);

Flight::route('POST /search', function(){
  $ontologija = new Ontologija();
  $ontologija->setPodaci(Flight::request()->data);

  $doctrineBootstrap = Flight::entityManager();
  $em = $doctrineBootstrap->getEntityManager();

  $em->persist($ontologija);
  $em->flush();

  $poruka=new stdClass();
  $poruka->tekst="OK";
  $poruka->greska=false;
  $odgovor=new stdClass();
  $odgovor->poruka=$poruka;

  Flight::json($odgovor);

  header("HTTP/1.1 201 Created");

});


Flight::route('PUT /search/@sifra', function($sifra){
    
    $doctrineBootstrap = Flight::entityManager();
    $em = $doctrineBootstrap->getEntityManager();
    $repozitorij=$em->getRepository('Peuraca\Ontologija');
    $ontologija = $repozitorij->find($sifra);

    $ontologija->setPodaci(Flight::request()->data);
    $em->persist($ontologija);
    $em->flush();
  
    $poruka=new stdClass();
    $poruka->tekst="OK";
    $poruka->greska=false;
    $odgovor=new stdClass();
    $odgovor->poruka=$poruka;
  
    Flight::json($odgovor);
  
  });

  Flight::route('DELETE /search/@sifra', function($sifra){
    
    $doctrineBootstrap = Flight::entityManager();
    $em = $doctrineBootstrap->getEntityManager();
    $repozitorij=$em->getRepository('Peuraca\Ontologija');
    $ontologija = $repozitorij->find($sifra);

    $ontologija->setPodaci(Flight::request()->data);
    $em->remove($ontologija);
    $em->flush();
  
    $poruka=new stdClass();
    $poruka->tekst="OK";
    $poruka->greska=false;
    $odgovor=new stdClass();
    $odgovor->poruka=$poruka;
  
    Flight::json($odgovor);
  
  });

Flight::route('GET /search/@naslov', function($title){

  $doctrineBootstrap = Flight::entityManager();
  $em = $doctrineBootstrap->getEntityManager();
  $repozitorij=$em->getRepository('Peuraca\Ontologija');
  //$zapisi = $repozitorij->findBy(array('naslov' => $title)); // prvo što sam probao ali nije radilo parcijalno traženje, morala bi se podudarat cijela riječ
  $zapisi = $repozitorij->createQueryBuilder('p')
                        ->where('p.naslov LIKE :naslov')
                        ->setParameter('naslov', '%'.$title.'%')
                        ->getQuery()
                        ->getResult();  // https://stackoverflow.com/questions/12706337/doctrine-2-query-with-like/12706380
  echo $doctrineBootstrap->getJson($zapisi);

});

$cl = new ClassLoader('Peuraca', __DIR__, '/src');
$cl->register();
require_once 'bootstrap.php';
Flight::register('entityManager', 'DoctrineBootstrap');

Flight::start();

// Ontologije su nam svima slične, ali će trebati neku drugu kombinaciju metoda uvjeta i stringova, evo potencijalne linije koje će vam pomoći
/* Sva svojstva (tj. anotacije prema Protegeu)
foreach ($resource->properties() as $key) {
    echo $i++ . ' ' . $key . ' <br/>';
}
*/

/* Kako maknuti sve iz linka ispred # hashtaga
$url = parse_url($type);
$type2 = $url["fragment"];
*/

/* Svi tipovi
foreach ($resource->types() as $key) {
    echo $i++ . ' ' . $key . ' <br/>';
}
*/
