<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/21
 * Time: 15:30
 */

namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
}