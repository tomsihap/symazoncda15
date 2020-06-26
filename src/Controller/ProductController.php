<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/new", name="product_new", methods={"GET", "POST"})
     */
    public function new(Request $request) : Response
    {

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/form.html.twig', [
            'form' => $form->createView()
        ]);
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
     * @Route("/{product}/edit", name="product_edit", methods={"GET", "POST"})
     * @param Product $product
     * @return Response
     */
    public function edit(Request $request, Product $product) : Response
    {

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();

            return $this->redirectToRoute('product_show', ['product' => $product->getId()]);
        }

        return  $this->render('product/form.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
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