<?php


namespace Richpolis\PublicacionesBundle\DataFixtures\ORM;
 
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Richpolis\PublicacionesBundle\Entity\CategoriasPublicacion;
 
class LoadPublicacionesData extends AbstractFixture implements OrderedFixtureInterface
{
  public function load(ObjectManager $em)
  {
    $principal = new CategoriasPublicacion();
    $principal->setCategoria('Publicaciones about');
    $principal->setTipoCategoria(CategoriasPublicacion::$ABOUT);
    $principal->setPosicion(1);
 
    $distribuidores = new CategoriasPublicacion();
    $distribuidores->setCategoria('Where To Find Mexico');
    $distribuidores->setTipoCategoria(CategoriasPublicacion::$DISTRIBUIDORES);
    $distribuidores->setPosicion(2);
    
    
    $distribuidores2 = new CategoriasPublicacion();
    $distribuidores2->setCategoria('Where To Find USA');
    $distribuidores2->setTipoCategoria(CategoriasPublicacion::$DISTRIBUIDORES);
    $distribuidores2->setPosicion(3);
    
    $distribuidores3 = new CategoriasPublicacion();
    $distribuidores3->setCategoria('Where To Find Distribuidores');
    $distribuidores3->setTipoCategoria(CategoriasPublicacion::$DISTRIBUIDORES);
    $distribuidores3->setPosicion(4);
    
    
    $em->persist($principal);
    $em->persist($distribuidores);
    $em->persist($distribuidores2);
    $em->persist($distribuidores3);

    
    $em->flush();
 

  }
 
  public function getOrder()
  {
    return 2; // the order in which fixtures will be loaded
  }
}