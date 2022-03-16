<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\Admin1Type;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private $passwordEncoder;

public function __construct(UserPasswordHasherInterface $passwordEncoder)
{
    $this->passwordEncoder = $passwordEncoder;        
}

    /**
     * @Route("/admin", name="app_admin1_index")
     */
    public function index(): Response
    {
        return $this->render('admin1/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
  

       /**
     * @Route("/new", name="admin_new", methods={"GET","POST"})
     */
    public function new(Request $request,UserPasswordHasherInterface $userPasswordHasher,ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $admin = new Admin();
        $form = $this->createForm(Admin1Type::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
 // encode the plain password
 $admin->setPassword(
    $userPasswordHasher->hashPassword(
        $admin,
        $form->get('plainPassword')->getData()
    )
);
            

            $entityManager = $doctrine->getManager();

           
            $entityManager->persist($admin);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('admin1/new.html.twig', [
            'admin' => $admin,
            'form' => $form->createView(),
        ]);
    }
    /**
 *@Route("/ListAdmin",name="Admins_list")
 */
public function listAdmin(ManagerRegistry $doctrine)
{
//récupérer tous les articles de la table article de la BD //et les mettre dans le tableau 
$admins= $doctrine->getRepository(Admin::class)->findAll();
return $this->render('admin1/index.html.twig',['admins'=> $admins]);
}
     /**
     * @Route("/{id}", name="admin_show", methods={"GET"})
     */
    public function show(Admin $admin): Response
    {
        return $this->render('admin1/show.html.twig', [
            'admin' => $admin,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Admin $admin,ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(UserType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();

            $plainpwd = $admin->getPassword();
           // $encoded = $this->passwordEncoder->encodePassword($admin,$plainpwd);
            //$admin->setPassword($encoded);
            
            $entityManager->persist($admin);
            $entityManager->flush();            

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin1/edit.html.twig', [
            'admin' => $admin,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Admin $admin ,ManagerRegistry $doctrine): Response
    {
        if ($this->isCsrfTokenValid('delete'.$admin->getId(), $request->request->get('_token'))) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($admin);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_index');
    }
}
