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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PostController extends Controller
{
    /**
     * @Route("/post/", name="post_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $this->getDoctrine()->getRepository("AppBundle:Post")->findAll();

        return $this->render("post/index.html.twig", [
            "posts" => $posts
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
            return $this->redirectToRoute("post_index");
        }

        return $this->render("post/edit.html.twig", [
            "form" => $form->createView(),
            "post" => $post
        ]);
    }
}