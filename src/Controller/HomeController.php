<?php

namespace App\Controller;

use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;


class HomeController extends AbstractController {
  /**
  * @var PropertyRepository
  */
  private $messageRepository;

  /**
  * @var ObjectManager
  */
  private $em;

  public function __construct(MessageRepository $messageRepository, ObjectManager $em ) {
      $this->messageRepository = $messageRepository;
      $this->em = $em;
  }

  /**
  * @Route("/", name="home")
  * @param MessageRepository $repository
  * @return Response
  */
  public function index(PaginatorInterface $paginator, Request $request) : Response {

    $messages = $paginator->paginate($this->messageRepository->findLatest(), $request->query->getInt('page', 1), 12);

    return $this->render("message/index.html.twig", [
      "current_menu" => "messages",
      "messages" => $messages
    ]);
  }
}

?>
