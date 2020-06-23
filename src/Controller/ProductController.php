<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/products", name="product_index", methods={"GET"})
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository) : Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/products/new", name="product_new", methods={"GET"})
     */
    public function new() : Response
    {
        return $this->render('product/new.html.twig');
    }

    /**
     * @Route("/products", name="product_create", methods={"POST"})
     */
    public function create(Request $request) : Response
    {

        $product = new Product;
        $product->setTitle( $request->request->get('title') );
        $product->setDescription( $request->request->get('description') );
        $product->setPrice( $request->request->get('price') );
        $product->setQuantity( $request->request->get('quantity') );

        $manager = $this->getDoctrine()->getManager();

        $manager->persist($product);
        $manager->flush();

        return $this->redirectToRoute("product_index");
    }
}
