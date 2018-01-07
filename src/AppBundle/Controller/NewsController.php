<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use AppBundle\Entity\User;
use AppBundle\Form\Type\NewsType;
use AppBundle\Security\NewsVoter;
use AppBundle\Service\FileManager\FileManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends Controller
{
    /**
     * @Route("/news/add", name="add_news")
     * @Security("is_granted('ROLE_USER') and is_granted('publish', 'news')")
     */
    public function addAction(Request $request)
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news, array(
            'fileManager' => $this->get('app.file_manager'),
            'tokenStorage' => $this->get('security.token_storage')
        ));

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($news);
            $em->flush();

            $this->get('session')->getFlashbag()->add('success', "News has been successfully created.");

            return $this->redirectToRoute('user_news_list');
        }

        return $this->render('/news/add.html.twig', array(
             'form' => $form->createView()
        ));
    }

    /**
     * @Route("/news/user-list", name="user_news_list")
     * @Security("is_granted('ROLE_USER')")
     */
    public function userListAction()
    {
        /* @var $user User */
        $user = $this->getUser();

        return $this->render('/news/list.html.twig', array(
            'newsCollection' => $user->getNewsCollection()->toArray()
        ));
    }

    /**
     * @Route("/news/view/{id}", name="news_view")
     */
    public function viewAction(News $news)
    {
        return $this->render('/news/view.html.twig', array(
            'news' => $news
        ));
    }

    /**
     * @Route("/news/delete/{id}", name="delete_news")
     * @Security("is_granted('ROLE_USER')")
     */
    public function deleteAction(News $news)
    {
        $this->denyAccessUnlessGranted(NewsVoter::DELETE, $news);

        $em = $this->getDoctrine()->getManager();
        $em->remove($news);
        $em->flush();

        $this->get('session')->getFlashbag()->add('success', "The news has been removed successfully.");

        return $this->redirectToRoute("user_news_list");
    }

    /**
     * @Route("/news/pdf/{id}", name="pdf_news")
     */
    public function pdfAction(News $news)
    {
        $html = $this->renderView('news/pdf.html.twig', array('news' => $news));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="news.pdf"'
            )
        );
    }

    /**
     * @Route("/news/feed", name="feed_news")
     */
    public function feedAction()
    {
        /* @var $newsRepo \AppBundle\Repository\NewsRepository */
        $newsRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:News');
        $newsCollection = $newsRepo->findLatest10();

        $feed = $this->get('eko_feed.feed.manager')->get('news');
        $feed->addFromArray($newsCollection);

        return new Response($feed->render('rss'));
    }
}
