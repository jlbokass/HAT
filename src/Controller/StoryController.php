<?php

namespace App\Controller;

use App\Entity\Story;
use App\Form\StoryType;
use App\Repository\StoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * <p> Add a Story
     *
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function add(EntityManagerInterface $manager): Response
    {
        $storyForm = $this->createForm(StoryType::class);

        return $this->render('story/add.html.twig', [
            'storyForm' => $storyForm->createView()
        ]);
    }

    /**
     * @Route("/story/edit/{id}", name="edit_story", requirements={"id"="\d+"})
     *
     * @param Story $story
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function edit(Story $story, EntityManagerInterface $manager): Response
    {
        $story->setTitle('An edit story');
        $story->setContent('This is a content for an edit story');

        $manager->flush();

        return $this->render('story/show.html.twig', [
            'story' => $story
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
