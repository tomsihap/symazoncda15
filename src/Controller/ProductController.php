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

    /**
     * @Route("/products/search", name="product_search", methods={"POST"})
     */
    public function search(Request $request, ProductRepository $productRepository) : Response
    {
        $search = $request->request->get('query');
        $products = $productRepository->findBySearch($search);

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/products/{product}/edit", name="product_edit", methods={"GET"})
     * @param Product $product
     * @return Response
     */
    public function edit(Product $product) : Response
    {
        return  $this->render('product/new.html.twig', ['product' => $product]);
    }

    /**
     * @Route("/products/{product}/edit", name="product_update", methods={"POST"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function update(Request $request, Product $product) : Response
    {
        $product->setTitle($request->request->get('title'));
        $product->setDescription($request->request->get('description'));
        $product->setQuantity($request->request->get('quantity'));
        $product->setPrice($request->request->get('price'));

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($product);
        $manager->flush();

        return $this->redirectToRoute('product_show', ['product' => $product->getId()]);
    }

    /**
     * @Route("/products/{product}/delete", name="product_delete", methods={"POST"})
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
     * @Route("/products/{product}", name="product_show", methods={"GET"})
     */
    public function show(Product $product) : Response
    {
        /**
         * Dans cette méthode, vous voyez qu'on injecte un objet Product $product (qui correspond au {product}
         * de l'URL, on passe en fait un ID).
         * Grâce à ce typage, Symfony est capable de comprendre qu'on passe un ID de... Produit !
         * Du coup, pas besoin d'utiliser le Repository, l'objet $product recherché est directement là.
         */

        return  $this->render('product/show.html.twig', ['product' => $product]);
    }
}
