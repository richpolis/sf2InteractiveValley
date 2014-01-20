<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\BackendBundle\Entity\Contacto;
use Richpolis\BackendBundle\Form\ContactoType;
use Richpolis\PublicacionesBundle\Entity\CategoriasPublicacion;
use Richpolis\CategoriasGaleriaBundle\Entity\Categorias;
use Richpolis\FrontendBundle\Entity\DiageoUsuarios;
use Richpolis\FrontendBundle\Form\DiageoUsuariosType;

/**
 * Frontend controller.
 *
 * @Route("/")
 */
class DefaultController extends Controller {
    
    /**
     * Lists all Frontend entities.
     *
     * @Route("/", name="construccion")
     */
    public function construccionAction()
    {
        return $this->render("FrontendBundle:Default:construccion.html.twig",array(
            
        ));

    }
    
    /**
     * Lists all Frontend entities.
     *
     * @Route("/inicio", name="homepage")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
       
        $galerias = $em->getRepository('CategoriasGaleriaBundle:Galerias')
                       ->getGaleriaPorTipoCategoria(Categorias::$GALERIA_PRINCIPAL);
        
        return $this->render("FrontendBundle:Default:index.html.twig",array(
          "galerias"=>$galerias,
        ));

    }
    
    /**
     * Lists all Frontend projects.
     *
     * @Route("/projects", name="proyectos")
     */
    public function proyectosAction()
    {
        $em = $this->getDoctrine()->getManager();
        $pgs = $em->getRepository('PublicacionesBundle:PublicacionGalerias')
                  ->findProyectosAndGalerias();  
        return $this->render("FrontendBundle:Default:vcells.html.twig",array(
            'pgs'=>$pgs,
        ));

    }
    
    /**
     * Show only project with all images in the gallery.
     *
     * @Route("/projects/show/{id}", name="proyectos_show")
     */
    public function proyectosShowAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $pg = $em->getRepository('PublicacionesBundle:PublicacionGalerias')
                  ->findOneProyectoAndGaleria($id);
        
        return $this->render("FrontendBundle:Default:vsingle.html.twig",array(
            'pg'=>$pg,
        ));

    }

    /**
     * @Route("/contacto", name="frontend_contacto")
     * @Method({"GET", "POST"})
     */
    public function contactoAction() {
        $contacto = new Contacto();
        $form = $this->createForm(new ContactoType(), $contacto);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $datos=$form->getData();
                
                
                $message = \Swift_Message::newInstance()
                        ->setSubject('Contacto desde pagina')
                        ->setFrom($datos->getEmail())
                        ->setTo($this->container->getParameter('richpolis.emails.to_email'))
                        ->setBody($this->renderView('BackendBundle:Default:contactoEmail.html.twig', array('datos' => $datos)), 'text/html');
                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('noticia', 'Gracias por enviar tu correo, nos comunicaremos a la brevedad posible!');

                // Redirige - Esto es importante para prevenir que el usuario
                // reenvíe el formulario si actualiza la página
                $ok=true;
                $error=false;
                $mensaje="El mensaje ha sido enviado";
                $contacto = new Contacto();
                $form = $this->createForm(new ContactoType(), $contacto);
            }else{
                $ok=false;
                $error=true;
                $mensaje="El mensaje no se ha podido enviar";
            }
        }else{
            $ok=false;
            $error=false;
            $mensaje="Violacion de acceso";
        }
        
        $em = $this->getDoctrine()->getManager();
        $contacto = $em->getRepository('BackendBundle:Configuraciones')->findOneBySlug('contacto'); 
        return $this->render("FrontendBundle:Default:contacto.html.twig",array(
              'contacto'=>$contacto,
              'form' => $form->createView(),
              'ok'=>$ok,
              'error'=>$error,
              'mensaje'=>$mensaje,
        ));
    }
    
    
    /**
     * @Route("/diageo/users/create", name="frontend_diageo_users_create")
     * @Method({"POST"})
     */
    public function postDiageoUsersCreateAction() {
        $usuario = new DiageoUsuarios();
        $form = $this->createForm(new DiageoUsuariosType(), $usuario);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            $datos=$form->getData();
            $em=$this->getDoctrine()->getManager();
            $user=$em->getRepository('FrontendBundle:DiageoUsuarios')->findOneByEmail($datos->getEmail());
            if($user==null){
                //if($form->isValid()){
                    $usuario->setPassword("123Admin!");
                    $em->persist($usuario);
                    $em->flush();

                    $message = \Swift_Message::newInstance()
                            ->setSubject('Confirmacion de alta de usuario Diageo')
                            ->setFrom($datos->getEmail())
                            ->setTo("phrenesis@gmail.com")
                            ->setBody($this->renderView('FrontendBundle:Default:confirmacionDiageoUsuariosCreateEmail.html.twig', array('datos' => $datos)), 'text/html');
                    $status=$this->get('mailer')->send($message);

                    $response = new Response(json_encode(array(
                        "result"=>"Gracias, se le enviara un aviso del status de su solicitud",
                        "status"=>$status,
                        "send"=>true
                        )));
                    
                /*}else{
                    $response = new Response(json_encode(array(
                        "result"=>"El mensaje no se ha podido enviar",
                        "send"=>false
                        )));
                }*/
            }else{
                $response = new Response(json_encode(array(
                    "result"=>"usuario ya existe",
                    "send"=>false
                    )));
            }
        }else{
            // create a JSON-response with a 200 status code
            $response = new Response(json_encode(array(
                "result"=>"no post",
                "send"=>false
                )));
        }
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/diageo/users/acept/{slug}", name="frontend_diageo_users_acept")
     */
    public function getDiageoUsersAceptAction($slug) {
            $em=$this->getDoctrine()->getManager();
            $user=$em->getRepository('FrontendBundle:DiageoUsuarios')->findOneBySlug($slug);
            if($user!=null){
                $user->setIsActive(true);
                $em->flush();

                    $message = \Swift_Message::newInstance()
                            ->setSubject('Alta de usuario Diageo')
                            ->setFrom("phrenesis@gmail.com")
                            ->setTo($user->getEmail())
                            ->setBody($this->renderView('FrontendBundle:Default:respuestaDiageoUsuariosCreateEmail.html.twig', array('datos' => $user)), 'text/html');
                    $this->get('mailer')->send($message);

                    $response = new Response("Alta de usuario aceptada");
                    
            }else{
                    $response = new Response("No se pudo realizar el envio");
            }
            
        $response->headers->set('Content-Type', 'application/html');
        return $response;
    }
}

?>
