<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package App\Controller
 * @Route("/products")
 */
class ProductController extends AbstractController
{

    /**
     * @Route("/", name="product_index", methods={"GET"})
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository) : Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'title' => 'Tous nos produits'
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET"})
     */
    public function new() : Response
    {
        return $this->render('product/new.html.twig');
    }

    /**
     * @Route("/", name="product_create", methods={"POST"})
     */
    public function create(Request $request) : Response
    {

        $token = $request->request->get('token');

        if ($this->isCsrfTokenValid('product-form', $token)) {
            $product = new Product;
            $product->setTitle( $request->request->get('title') );
            $product->setDescription( $request->request->get('description') );
            $product->setPrice( $request->request->get('price') );
            $product->setQuantity( $request->request->get('quantity') );

            $manager = $this->getDoctrine()->getManager();

            $manager->persist($product);
            $manager->flush();
        }

        return $this->redirectToRoute("product_index");
    }

    /**
     * @Route("/search", name="product_search", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function search(Request $request, ProductRepository $productRepository) : Response
    {
        $queryString = $request->query->get('query');
        $products = $productRepository->findBySearch($queryString);

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'title' => 'Résultats de recherche'
        ]);
    }

    /**
     * @Route("/{product}/edit", name="product_edit", methods={"GET"})
     * @param Product $product
     * @return Response
     */
    public function edit(Product $product) : Response
    {
        return  $this->render('product/new.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/{product}", name="product_update", methods={"PATCH"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function update(Request $request, Product $product) : Response
    {
        $token = $request->request->get('token');

        if ($this->isCsrfTokenValid('product-form', $token)) {
            $product->setTitle($request->request->get('title'));
            $product->setDescription($request->request->get('description'));
            $product->setQuantity($request->request->get('quantity'));
            $product->setPrice($request->request->get('price'));

            $manager = $this->getDoctrine()->getManager();
            $manager->flush();
        }


        return $this->redirectToRoute('product_show', ['product' => $product->getId()]);
    }

    /**
     * @Route("/{product}", name="product_delete", methods={"DELETE"})
     * @param Product $product
     * @return Response
     */
    public function delete(Product $product) : Response
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($product);
        $manager->flush();

        return $this->redirectToRoute('product_index');
    }

    /**
     * @Route("/{product}", name="product_show", methods={"GET"})
     * @param Product $product
     * @return Response
     */
    public function show(Product $product) : Response
    {

        return $this->render('product/show.html.twig', [
            'product' => $product
        ] );

    }
}