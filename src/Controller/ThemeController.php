<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use App\Services\StringToUppercase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemeController extends AbstractController
{
    /**
     * @Route("/theme", name="home_theme")
     *
     * @param ThemeRepository $themeRepository
     *
     * @return Response
     */
    public function index(ThemeRepository $themeRepository): Response
    {
        $themes = $themeRepository->findAll();

        return $this->render('theme/index.html.twig', [
            'themes' => $themes
        ]);
    }

    /**
     * @Route("/theme/show/{id}", name="show_theme", requirements={"id"="\d+"})
     *
     * @param Theme $theme
     *
     * @return Response
     */
    public function show(Theme $theme): Response
    {
        return $this->render('theme/show.html.twig', [
            'theme' => $theme
        ]);
    }

    /**
     * @Route("/theme/add", name="add_theme")
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param StringToUppercase $stringToUppercase
     * @return Response
     */
    public function add(EntityManagerInterface $manager, Request $request, StringToUppercase $stringToUppercase): Response
    {
        $themeForm = $this->createForm(ThemeType::class);
        $themeForm->handleRequest($request);

        if ($themeForm->isSubmitted() && $themeForm->isValid())
        {
            /** @var Theme $theme */
            $theme = $themeForm->getData();

            $theme->setName($stringToUppercase->stringToUppercase($theme->getName()));

            $manager->persist($theme);
            $manager->flush();

            $this->addFlash(
                'success',
                'le thème à bien été crée'
            );

            return $this->redirectToRoute('home_theme');
        }

        return $this->render('theme/add.html.twig', [
            'themeForm' => $themeForm->createView()
        ]);
    }


    /**
     * @Route("/theme/edit/{id}", name="edit_theme", requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Theme $theme
     *
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Theme $theme): Response
    {
        $themeForm = $this->createForm(ThemeType::class, $theme);
        $themeForm->handleRequest($request);

        if ($themeForm->isSubmitted() && $themeForm->isValid())
        {
            $themeForm->getData();
            $manager->flush();

            $this->addFlash(
                'success',
                'Thème crée'
            );

            return $this->redirectToRoute('home_theme');
        }

        return $this->render('/theme/edit.html.twig', [
            'theme' => $theme,
            'themeForm' => $themeForm->createView()
        ]);
    }

}
