<?php

namespace App\Controller;

use App\Entity\Comic;
use App\Entity\HotBox;
use App\Entity\Rotation;
use App\Enumerations\RoleEnumeration;
use App\Enumerations\RotationFrequencyEnumeration;
use App\Exceptions\HotBoxException;
use App\Form\HotBoxCreateFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HotBoxController extends AbstractController
{
    #[Route('/hotbox/create', name: 'app_createhotbox')]
    #[Route('/hotbox/edit/{id}', name: 'app_edithotbox')]
    public function create(Request $request, EntityManagerInterface $entityManager, ?int $id = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);

        $hotbox = new HotBox();
        if (!empty($id)) {
            $hotbox = $entityManager->getRepository(HotBox::class)->find($id);
        }
        $form = $this->createForm(HotBoxCreateFormType::class, $hotbox);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('name')->getData();
            $code = $form->get('code')->getData();
            $active = $form->get('active')->getData();
            $freq = $form->get('rotationFrequency')->getData();

            $hotbox
                ->setName($name)
                ->setCode($code)
                ->setActive($active)
                ->setRotationFrequency($freq)
            ;
            $this->retimeRotations($hotbox->getId(), $entityManager);
            return new RedirectResponse("/hotbox/edit/{$hotbox->getId()}");
        }


        $comics = $entityManager->getRepository(Comic::class)->findAll();
        $comics = $this->orderByRotation($comics, $hotbox);

        return $this->render('hotbox/create.html.twig', [
            'hotboxform' => $form->createView(),
            'comics' => $comics
        ]);
    }

    private function orderByRotation(array $comics, HotBox $hotBox)
    {
        $inHotBox = [];
        $outHotBox = [];

        /**
         * @var Comic $comic
         */
        foreach ($comics as $comic) {
            $seen = false;
            foreach ($comic->getRotations() as $rotation) {
                if ($rotation->getHotbox()->getId() === $hotBox->getId()) {
                    $inHotBox[] = $comic;
                    $seen = true;
                }
            }
            if (!$seen) {
                $outHotBox[] = $comic;
            }
        }

        usort($inHotBox, function (Comic $a, Comic $b) use ($hotBox) {
            /**
             * @var HotBox $hotBox
             */
            $rotA = null;
            $rotB = null;
            foreach ($a->getRotations() as $arot) {
                if ($arot->getHotbox()->getId() === $hotBox->getId()) {
                    $rotA = $arot;
                    break;
                }
            }
            foreach ($b->getRotations() as $brot) {
                if ($brot->getHotbox()->getId() === $hotBox->getId()) {
                    $rotB = $brot;
                    break;
                }
            }
            if ($rotA->getStart() === $rotB->getStart()) {
                return 0;
            }
            return ($rotA->getStart() < $rotB->getStart()) ? -1 : 1;
        });
        usort($outHotBox, function(Comic $a, Comic $b){
            return strcmp($a->getName(), $b->getName());
        });

        $out = array_merge($inHotBox, $outHotBox);
        return $out;
    }

    #[Route('/hotbox/addrotation/{hotboxid}/{comicid}', name: 'app_addrotation')]
    public function addRotation(Request $request, EntityManagerInterface $entityManager, int $hotboxid, int $comicid): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);
        /**
         * @var HotBox $hotbox
         */
        $hotbox = $entityManager->getRepository(HotBox::class)->find($hotboxid);
        /**
         * @var Comic $comic
         */
        $comic = $entityManager->getRepository(Comic::class)->find($comicid);
        $rotation = $entityManager->getRepository(Rotation::class)->findOneBy(['hotbox' => $hotbox, 'comic' => $comic]);

        if (!empty($rotation)) {
            throw new HotBoxException("Cannot create a rotation, rotation already exists");
        }

        $rotation = new Rotation();
        $rotation->setHotbox($hotbox)->setComic($comic)->calculateNextStart()->calculateExpire();
        $entityManager->persist($rotation);
        $entityManager->flush();
        $this->retimeRotations($hotbox->getId(), $entityManager);

        return new RedirectResponse("/hotbox/edit/{$hotboxid}");
    }

    protected function retimeRotations(int $id, EntityManagerInterface $entityManager): static
    {
        $hotbox = $entityManager->getRepository(HotBox::class)->find($id);
        $rotations = $hotbox->getRotations()->toArray();
        usort($rotations, function(Rotation $a, Rotation $b){
            if ($a->getStart() === $b->getStart()) {
                return 0;
            }
            return ($a->getStart() < $b->getStart()) ? -1 : 1;
        });
        $started = null;
        $now = new \DateTime();
        /**
         * @var Rotation $rotation
         */
        foreach ($rotations as &$rotation) {
            if ($now >= $rotation->getExpire()) {
                $rotation->calculateNextStart()->calculateExpire();
                $entityManager->persist($rotation);
                continue;
            }
            if (!empty($started)) {
                $rotation->setStart(RotationFrequencyEnumeration::getNextStart($hotbox->getRotationFrequency(), $started));
            }
            $rotation->calculateExpire();
            $entityManager->persist($rotation);
            $started = $rotation->getStart();
        }
        $entityManager->flush();
        return $this;
    }

    #[Route('/hotbox/delrotation/{hotboxid}/{comicid}', name: 'app_delrotation')]
    public function delRotation(Request $request, EntityManagerInterface $entityManager, int $hotboxid, int $comicid): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(RoleEnumeration::ROLE_ADMIN);
        /**
         * @var HotBox $hotbox
         */
        $hotbox = $entityManager->getRepository(HotBox::class)->find($hotboxid);
        /**
         * @var Comic $comic
         */
        $comic = $entityManager->getRepository(Comic::class)->find($comicid);
        /**
         * @var Rotation $rotation
         */
        $rotation = $entityManager->getRepository(Rotation::class)->findOneBy(['hotbox' => $hotbox, 'comic' => $comic]);

        if (empty($rotation)) {
            throw new HotBoxException("Cannot delete a rotation that doesn't exist");
        }

        $entityManager->remove($rotation);
        $entityManager->flush();

        $this->retimeRotations($hotbox->getId(), $entityManager);

        return new RedirectResponse("/hotbox/edit/{$hotboxid}");
    }
}