<?php
 
namespace Application\Sonata\UserBundle\Security\Http\Authentication;
 
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Widop\PHPBBBundle\Model\AuthenticationManager;
 
/**
 * Custom authentication success handler
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
 
   private $router;
   //private $em;
   private $am;
 
   /**
    * Constructor
    * @param RouterInterface   $router
    * @param EntityManager     $em
    */
   public function __construct(RouterInterface $router, AuthenticationManager $am)
   {
      $this->am = $am;
      $this->router = $router;
      //$this->em = $em;
   }
 
   /**
    * This is called when an interactive authentication attempt succeeds. This
    * is called by authentication listeners inheriting from AbstractAuthenticationListener.
    * @param Request        $request
    * @param TokenInterface $token
    * @return Response The response to return
    */
   function onAuthenticationSuccess(Request $request, TokenInterface $token)
   {
      $user = $token->getUser();
      return new RedirectResponse($this->router->generate('sonata_admin_dashboard'));
   }
}