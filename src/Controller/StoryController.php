<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Story;
use App\Form\StoryType;
use App\Repository\StoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class StoryController extends AbstractController
{
    /**
     * @Route("/story", name="home_story")
     *
     * @param StoryRepository $storyRepository
     *
     * @return Response
     */
    public function index(StoryRepository $storyRepository): Response
    {
        $stories = $storyRepository->findAll();

        return $this->render('story/index.html.twig', [
            'stories' => $stories,
        ]);
    }

    /**
     * @Route("/story/add", name="add_story")
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     *
     * @return Response
     */
    public function add(EntityManagerInterface $manager, Request $request): Response
    {
        $storyForm = $this->createForm(StoryType::class);
        $storyForm->handleRequest($request);

        if ($storyForm->isSubmitted() && $storyForm->isValid()) {

            $path = $this->getParameter('kernel.project_dir') . '/public/images';

            // Get form values from story form
            $story = $storyForm->getData();

            /**
             * @var Image $image
             * get the image
             */
            $image = $story->getImage();

            // get the image sends by user
            $file = $image->getFile();

            // create an unique name for the file
            $name = md5(uniqid('', true)) . '.' . $file->guessExtension();

            // move the file
            $file->move($path, $name);
            $image->setName($name);

            $manager->persist($story);
            $manager->flush();

            $this->addFlash(
                "success",
                "L'histoire à bien été cré"
            );

            return $this->redirectToRoute('home_story');
        }

        return $this->render('story/add.html.twig', [
            'storyForm' => $storyForm->createView()
        ]);
    }

    /**
     * @Route("/story/edit/{id}", name="edit_story", requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Story $story
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function edit(Request $request, Story $story, EntityManagerInterface $manager): Response
    {
        $storyForm = $this->createForm(StoryType::class, $story);
        $storyForm->handleRequest($request);

        if ($storyForm->isSubmitted() && $storyForm->isValid()) {

            $story = $storyForm->getData();

            $manager->flush();

            $this->addFlash(
                "success",
                "L'histoire à bien été modifé"
            );

            return $this->redirectToRoute('home_story');
        }

        return $this->render('story/edit.html.twig', [
            'story' => $story,
            'storyForm' => $storyForm->createView()
        ]);
    }

    /**
     * @Route("/story/delete/{id}", name="story_delete", requirements={"id"="\d+"})
     *
     * @param Story $story
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function delete(Story $story, EntityManagerInterface $manager): Response
    {
        $manager->remove($story);
        $manager->flush();

        return $this->redirectToRoute('home_story');
    }

    /**
     * @Route("/story/show/{id}", name="story_show", requirements={"id"="\d+"})
     *
     * @param Story $story
     *
     * @return Response
     */
    public function show(Story $story): Response
    {
        return $this->render('story/show.html.twig', [
            'story' => $story
        ]);
    }
}
