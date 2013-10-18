<?php


namespace Richpolis\CategoriasGaleriaBundle\DataFixtures\ORM;
 
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Richpolis\CategoriasGaleriaBundle\Entity\Categorias;
 
class LoadCategoriasData extends AbstractFixture implements OrderedFixtureInterface
{
  public function load(ObjectManager $em)
  {
    $principal = new Categorias();
    $principal->setCategoria('Galeria Principal');
    $principal->setTipoCategoria(Categorias::$GALERIA_PRINCIPAL);
    $principal->setPosicion(1);
 
    $about = new Categorias();
    $about->setCategoria('Galeria About');
    $about->setTipoCategoria(Categorias::$GALERIA_ABOUT);
    $about->setPosicion(2);
    
    $distribuidores = new Categorias();
    $distribuidores->setCategoria('Galeria Distribuidores Nacionales');
    $distribuidores->setTipoCategoria(Categorias::$GALERIA_DISTRIBUIDORES);
    $distribuidores->setPosicion(3);
    
    $distribuidores2 = new Categorias();
    $distribuidores2->setCategoria('Galeria Distribuidores USA');
    $distribuidores2->setTipoCategoria(Categorias::$GALERIA_DISTRIBUIDORES);
    $distribuidores2->setPosicion(4);
    
    $espadin = new Categorias();
    $espadin->setCategoria('Espadin');
    $espadin->setTipoCategoria(Categorias::$GALERIA_HOWTOMIX);
    $espadin->setPosicion(5);
    
    $primario = new Categorias();
    $primario->setCategoria('Primario Mezcal Joven');
    $primario->setTipoCategoria(Categorias::$GALERIA_HOWTOMIX);
    $primario->setPosicion(6);
    
    $em->persist($principal);
    $em->persist($about);
    $em->persist($distribuidores);
    $em->persist($distribuidores2);
    $em->persist($espadin);
    $em->persist($primario);
    $em->flush();
 

  }
 
  public function getOrder()
  {
    return 1; // the order in which fixtures will be loaded
  }
}