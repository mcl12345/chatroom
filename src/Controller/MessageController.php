<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\MessageFormType;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageController extends AbstractController {

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
   * @Route("/messages", name="message.index")
   * @return Response
  */
  public function index(PaginatorInterface $paginator, Request $request): Response {
  //public function index(Request $request): Response {
      $messages = $paginator->paginate($this->messageRepository->findLatest(), $request->query->getInt('page', 1), 12);
      //$messages = $this->messageRepository->findLatest();

      return $this->render("message/index.html.twig", [
        "current_menu" => "messages",
        "messages" => $messages
      ]);
  }

/**
* @Route("/messages/{slug}-{id}", name="message.show", requirements={"slug": "[a-z0-9\-]*"})
* @return Response
*/
  public function show(Message $message, string $slug) : Response {
      if($message->getSlug() !== $slug) {
          return $this->redirectToRoute("message.show", [
            "id" => $message->getId(),
            "slug" => $message->getSlug()
          ], 301);
      }

      return $this->render("message/show.html.twig", [
        "message" => $message,
        "current_menu" => "messages"
      ]);
  }

  /**
   * @Route("/profile/message/create", name="message.new")
   */
  public function new(UserInterface $user, Request $request)
  {
      $message = new Message();
      $form = $this->createForm(MessageFormType::class, $message);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $message->setAuteur($user->getUsername());
          $this->em->persist($message);
          $this->em->flush();
          $this->addFlash('success', 'Message créé avec succès');
          return $this->redirectToRoute('message.index');
      }

      return $this->render('message/new.html.twig', [
          'message' => $message,
          'form'     => $form->createView()
      ]);
  }

  /**
  * @Route("/profile", name="message.index")
  * @return \Symfony\Component\HttpFoundation\Response;
  public function index() {

      $messages = $this->messageRepository->findAll();
      return $this->render("message/index.html.twig", compact("messages"));
  }*/

  /**
  * @Route("/profile/edit/message/{id}", name="message.edit")
  *
  */
  public function edit(Message $message, Request $request) {
    $form = $this->createForm(MessageFormType::class, $message);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
      $this->em->flush();
      return $this->redirectToRoute("message.index");
    }
    return $this->render("message/edit.html.twig", ["message"=> $message, "form" => $form->createView()]);
  }

  /**
  * @Route("/profile/delete/message/{id}", name="message.delete")
  *
  */
  public function delete(Message $message, Request $request) {
      if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->get('_token'))) {
          $this->em->remove($message);
          $this->em->flush();
          $this->addFlash('success', 'Message supprimé avec succès');
      }
      return $this->redirectToRoute('message.index');
  }
}

?>
