<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/21
 * Time: 15:30
 */

namespace AppBundle\Controller;
use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class PostController
 * @package AppBundle\Controller
 *
 * @Route("/admin")
 */
class PostController extends Controller
{
    /**
     * @Route("/post/", name="post_index")
     */
    public function indexAction(Request $request)
    {
        $word = $request->query->get('word');

        $em = $this->getDoctrine()->getManager();
       // $posts = $this->getDoctrine()->getRepository("AppBundle:Post")->findAll();
        /** @var QueryBuilder $qb */
//        $qb = $this->getDoctrine()->getRepository("AppBundle:Post")->createQueryBuilder("p");
//        $query = $qb->leftJoin("p.createdBy", "c")
//            ->where("c.id = :id")
//            ->setParameter("id", $this->getUser()->getId())
//            ->getQuery()
//        ;

        $qb = $this->getDoctrine()->getRepository("AppBundle:Post")->getQbByUserAndKeyword(
            $this->getUser()->getId(),
            $word
        );

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb->getQuery(), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );


        return $this->render("post/index.html.twig", [
            "pagination" => $pagination,
            "searchForm" => $this->createSearchForm()->createView()
        ]);
    }

    /**
     * @Route("/post/new", name="post_new")
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm("AppBundle\Form\PostType", $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $post->setCreatedBy($this->getUser());
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute("post_index");
        }

        return $this->render("post/new.html.twig", [
            "form" => $form->createView(),
            "post" => $post
        ]);
    }

    /**
     * @Route("/post/{id}/edit", name="post_edit")
     */
    public function editAction(Request $request, Post $post)
    {
        $form = $this->createForm("AppBundle\Form\PostType", $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute("post_show", [ "id" => $post->getId()]);
        }

        return $this->render("post/edit.html.twig", [
            "form" => $form->createView(),
            "post" => $post
        ]);
    }

    /**
     * @Route("/post/{id}/publish", name="post_publish")
     *
     * @Method("PATCH")
     */
    public function publishAction(Request $request, Post $post)
    {

        $form = $this->createPublishForm($post);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $post->setStatus(POst::STATUS_PUBLISHED);
            //$em->persist($post);
            $em->flush();
        }

        return $this->redirectToRoute("post_index");
//        $form = $this->createFormBuilder()
//            ->setAction($this->generateUrl("post_publish", array( "id" => $post->getId())))
//            ->setMethod("post")
//            ->getForm();

    }

    /**
     *
     * @Route("/post/{id}/show", name="post_show")
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function showAction(Request $request, Post $post)
    {

        return $this->render("post/show.html.twig", [
            "post" => $post,
            "deleteForm" => $this->createDeleteForm($post)->createView(),
            "publishForm" => $this->createPublishForm($post)->createView()
        ]);
    }

    /**
     * @Route("/post/{id}/delete", name="post_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param Post $post
     */
    public function deleteAction(Request $request, Post $post)
    {
        $form = $this->createDeleteForm($post);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirect($this->generateUrl("post_index"));
    }

    /**
     * @param Post $post
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    protected function createDeleteForm(Post $post)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl("post_delete", ["id" => $post->getId()]))
            ->setMethod("DELETE")
            ->getForm()
        ;

        return $form;
    }

    protected function createPublishForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl("post_publish", ["id" => $post->getId()]))
            ->setMethod("PATCH")
            ->getForm()
            ;
    }

    protected function createSearchForm()
    {
        return $this->createFormBuilder()
            ->add("word", TextType::class)
            ->setAction($this->generateUrl("post_index"))
            ->setMethod("GET")
            ->getForm();
    }

}