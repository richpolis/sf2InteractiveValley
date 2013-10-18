<?php

namespace Richpolis\CategoriasGaleriaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Richpolis\CategoriasGaleriaBundle\Entity\Categorias;

/**
 * CategoriasRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoriasRepository extends EntityRepository
{
    public function getMaxPosicion($tipo=0){
        $em=$this->getEntityManager();
        if($tipo==0){
            $query=$em->createQuery('
                SELECT COUNT(p.posicion) as value 
                FROM CategoriasGaleriaBundle:Categorias p 
                ORDER BY p.posicion ASC
            ');
        }else{
            $query=$em->createQuery('
                SELECT COUNT(p.posicion) as value 
                FROM CategoriasGaleriaBundle:Categorias p 
                WHERE p.tipoCategoria=:tipo 
                ORDER BY p.posicion ASC
            ')->setParameter('tipo', $tipo);
        }
        $max=$query->getResult();
        return $max[0]['value'];
    }
    
    public function getCategoriaActualPorTipoCategoria($tipoCategoria){
        $em=$this->getEntityManager();
        $query=$em->createQuery('
                    SELECT p,g FROM CategoriasGaleriaBundle:Categorias p 
                    LEFT JOIN p.galerias g 
                    WHERE p.tipoCategoria = :tipo 
                    AND g.isActive=:activeGaleria 
                    AND p.isActive = :active 
                    ORDER BY g.posicion ASC
                ')->setParameters(array('tipo'=> $tipoCategoria,'active'=>true,'activeGaleria'=>true));
        $categorias=$query->getResult();
        if(isset($categorias[0])){
            return $categorias[0];
        }else{
            $query=$em->createQuery('
                    SELECT p FROM CategoriasGaleriaBundle:Categorias p 
                    WHERE p.tipoCategoria = :tipo 
                    AND p.isActive = :active
                ')->setParameters(array('tipo'=> $tipoCategoria,'active'=>true));
            $categorias=$query->getResult();
            return $categorias[0];
        }
    }
    
    public function getCategoriaConGaleriaPorId($categoria_id,$active=true){
        $em=$this->getEntityManager();
        $query=$em->createQuery('
               SELECT p,g 
               FROM CategoriasGaleriaBundle:Categorias p 
               JOIN p.galerias g 
               WHERE p.id = :categoria 
               AND g.isActive = :active 
               ORDER BY g.posicion DESC
        ')->setParameters(array('categoria'=> $categoria_id,'active'=>$active));
        
        $categorias=$query->getResult();
        if(isset($categorias[0])){
            return $categorias[0];
        }else{
            return null;
        }
    }
    public function getQueryCategoriasPorTipoCategoria($tipoCategoria,$categoria_actual=0,$active=true){
        $em=$this->getEntityManager();
        $query=$em->createQuery('
                    SELECT p FROM CategoriasGaleriaBundle:Categorias p
                    WHERE p.tipoCategoria = :tipo 
                    AND p.id <> :actual 
                    AND p.isActive = :active 
                    ORDER BY p.posicion DESC 
                ')->setParameters(array(
                    'tipo'=> $tipoCategoria,
                    "actual"=>$categoria_actual,
                    'active'=>$active
                ));
        return $query;
    }
    
    public function getCategoriasPorTipoCategoria($tipoCategoria,$categoria_actual=0,$active=true){
        $query=$this->getQueryCategoriasPorTipoCategoria($tipoCategoria,$categoria_actual,$active);
        return $query->getResult();
    }
    
    public function getQueryCategoriasPorTipoYActivas($tipoCategoria,$todas=false){
        $query= $this->getEntityManager()->createQueryBuilder();
                $query->select('c,g')
                    ->from('Richpolis\CategoriasGaleriaBundle\Entity\Categorias', 'c')
                    ->leftJoin('c.galerias', 'g')
                    ->where('c.tipoCategoria=:tipo')
                    ->setParameter('tipo', $tipoCategoria)
                    ->orderBy('c.posicion', 'DESC')
                    ->addOrderBy('g.posicion', 'DESC'); 
        if(!$todas){
            $query->andWhere('c.isActive=:active')
                  ->setParameter('active', true);
        }
        return $query->getQuery();
    }
    
    public function getCategoriasPorTipoYActivas($tipo,$todas=false){
        $query=$this->getQueryCategoriasPorTipoYActivas($tipo, $todas);
        return $query->getResult();
    }
    
    public function getQueryCategoriasGaleriaActivas($tipoCategoria,$todas=false){
        $query=$this->createQueryBuilder('c')
                    //->leftJoin('c.galerias', 'g') 
                    ->where('c.tipoCategoria=:tipo')
                    ->setParameter('tipo', $tipoCategoria)
                    ->orderBy('c.posicion', 'DESC');
                    //->addOrderBy('g.posicion','DESC'); 
        if(!$todas){
            $query->andWhere('c.isActive=:active')
                  ->setParameter('active', true);
        }
        return $query->getQuery();
    }
    
    public function getCategoriasGaleriaActivas($tipo,$todas=false){
        $query=$this->getQueryCategoriasGaleriaActivas($tipo, $todas);
        return $query->getResult();
    }
    
    public function getCategoriasActuales(){
        $em=$this->getEntityManager();
        $query=$em->createQuery('
                    SELECT DISTINCT c,g 
                    FROM CategoriasGaleriaBundle:Categorias c 
                    LEFT JOIN c.galerias g 
                    WHERE c.isActive=:active 
                    AND g.isActive=:activeGaleria 
                    ORDER BY c.tipoCategoria,c.posicion DESC, g.posicion DESC 
                ')->setParameters(array('active'=>true,'activeGaleria'=>true));
        return $query->getResult();
    }
    
    public function getCategoriasValidas($registros){
        $categorias=array();
        $categorias['tipo'.Categorias::$GALERIA]=null;
        $categorias['tipo'.Categorias::$RECOMENDACIONES]=null;
        $lugar=0;
        
        foreach($registros as $registro){
            $lugar=$registro->getTipoCategoria();
            $categorias['tipo'.$lugar]=$registro;
        }
        
        return $categorias;        
    }
    
    public function getRegistroUpOrDown($posicionRegistro,$up=true){
        // $up = true, $up = false is down
        if($up){
            //up
            $query=$this->createQueryBuilder('p')
                    ->where('p.posicion>:posicion')
                    ->setParameter('posicion', $posicionRegistro)
                    ->orderBy('p.posicion', 'DESC');
        }else{
            //down
            $query=$this->createQueryBuilder('p')
                    ->where('p.posicion<:posicion')
                    ->setParameter('posicion', $posicionRegistro)
                    ->orderBy('p.posicion', 'DESC');
        }
        
        return $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }
    
}