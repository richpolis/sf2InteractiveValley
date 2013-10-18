<?php

namespace Richpolis\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="backend_homepage")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    
    
    /**
     * @Route("/navegar", name="backend_navegar")
     * @Template()
     */
    public function navegarAction()
    {
        return array();
    }
    
    /**
     * @Route("/seleccionar/galeria", name="backend_categorias")
     */
    public function categoriasAction()
    {
        return $this->forward('CategoriasGaleriaBundle:Categorias:select');
    }
    
    /**
     * @Route("/galeria/principal", name="backend_galerias_principal")
     */
    public function galeriaPrincipalAction()
    {
        return $this->forward('CategoriasGaleriaBundle:Categorias:galeriasPrincipal');
    }
    
    /**
     * @Route("/galeria/about", name="backend_galerias_about")
     */
    public function galeriaAboutAction()
    {
        return $this->forward('CategoriasGaleriaBundle:Categorias:galeriasAbout');
    }
    
    /**
     * @Route("/galeria/distribuidores", name="backend_galerias_distribuidores")
     */
    public function galeriaDistribuidoresAction()
    {
        return $this->forward('CategoriasGaleriaBundle:Categorias:galeriasDistribuidores');
    }

    /**
     * @Route("/galeria/howtomix", name="backend_galerias_howtomix")
     */
    public function galeriaHowToMixAction()
    {
        return $this->forward('CategoriasGaleriaBundle:Categorias:galeriasHowToMix');
    }
    
    
    /**
     * @Route("/configuraciones", name="backend_configuraciones")
     */
    public function configuracionesAction()
    {
        return $this->forward('BackendBundle:Configuraciones:index');
    }
    
    /**
     * @Route("/publicacion/about", name="publicaciones_about")
     */
    public function publicacionesAboutAction()
    {
        return $this->forward('PublicacionesBundle:Publicacion:publicacionesAbout');
    }
    
    /**
     * @Route("/where/to/find/mexico", name="publicaciones_wheretofindmexico")
     */
    public function whereToFindMexicoAction()
    {
        return $this->forward('PublicacionesBundle:Publicacion:whereToFindMexico');
    }
    
    /**
     * @Route("/where/to/find/usa", name="publicaciones_wheretofindusa")
     */
    public function whereToFindUsaAction()
    {
        return $this->forward('PublicacionesBundle:Publicacion:whereToFindUsa');
    }
    
    /**
     * @Route("/where/to/find/distribuidores", name="publicaciones_wheretofinddistribuidores")
     */
    public function whereToFindDistribuidoresAction()
    {
        return $this->forward('PublicacionesBundle:Publicacion:whereToFindDistribuidores');
    }
    
    /**
     * @Route("/login", name="backend_login")
     * @Template()
     */
    
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // obtiene el error de inicio de sesión si lo hay
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'BackendBundle:Default:login.html.twig',
            array(
                // último nombre de usuario ingresado
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            )
        );
    }
    
    /**
     * @Route("/login_check", name="backend_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="backend_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }
    
    
    
}
