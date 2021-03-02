<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="home_category")
     *
     * @param CategoryRepository $categoryRepository
     *
     * @return Response
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/add", name="add_category")
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     *
     * @return Response
     */
    public function add(EntityManagerInterface $manager, Request $request): Response
    {
        $categoryForm = $this->createForm(CategoryType::class);
        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid())
        {
            $category = $categoryForm->getData();

            $manager->persist($category);
            $manager->flush();

            $this->addFlash(
                'success',
                'La catégorie à été créé'
            );

            return $this->redirectToRoute('home_category');
        }

        return $this->render('category/add.html.twig', [
            'categoryForm' => $categoryForm->createView()
        ]);
    }
}
