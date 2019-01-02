<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends AbstractController {
    /**
    * @var UserPasswordEncoderInterface
    */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
    * @Route("/login", name="login")
    */
    public function login(AuthenticationUtils $authenticationUtils) : Response {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render("security/login.html.twig", [
          "last_username" => $lastUsername,
          "error" => $error
        ]);
    }

    /**
    * //@Route("/register", name="register")
    */
    /*public function register(ObjectManager $em) {

          $user = new User();
          $user->setUsername($_POST[""]);
          $user->setPassword($this->encoder->encodePassword($user, "demo"));
          $user->setEmail("demo@demo.com");
          $user->setRoles("ADMIN");
          $em->persist($user);
          $em->flush();
        return $this->render("security/register.html.twig");
    }*/
}

?>
