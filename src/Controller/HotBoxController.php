<?php

namespace App\Controller;

use App\Entity\Comic;
use App\Entity\HotBox;
use App\Entity\Rotation;
use App\Enumerations\RoleEnumeration;
use App\Enumerations\RotationFrequencyEnumeration;
use App\Form\HotBoxCreateFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

            $checkHB = $entityManager->getRepository(HotBox::class)->findBy(['name' => $form->get('name')->getData()]);
            if (empty($id) && !empty($checkHB)) {
                // This hotbox already exists
                $hotbox = $checkHB;
            }

            $name = $form->get('name')->getData();
            $code = $form->get('code')->getData();
            $active = $form->get('active')->getData();
            $freq = $form->get('rotationFrequency')->getData();
            $wid = $form->get('imageWidth')->getData();
            $hei = $form->get('imageHeight')->getData();

            $hotbox
                ->setName($name)
                ->setCode($code)
                ->setActive($active)
                ->setRotationFrequency($freq)
                ->setImageWidth($wid)
                ->setImageHeight($hei)
            ;
            $entityManager->persist($hotbox);
            $entityManager->flush();
            $this->retimeRotations($entityManager, $hotbox);
            return new RedirectResponse($this->generateUrl('app_edithotbox', ['id' => $hotbox->getId()]));
        }


        $comics = $entityManager->getRepository(Comic::class)->findBy(['active' => true, 'approved' => true]);
        $comics = $this->orderByRotation($entityManager, $comics, $hotbox);
        /**
         * @var Comic $comic
         */
        foreach ($comics as $comic) {
            $comic->imageSizeMatch($hotbox);
        }
        return $this->render('hotbox/create.html.twig', [
            'hotboxform' => $form->createView(),
            'comics' => $comics
        ]);
    }

    #[Route('/hotbox/toggleRotation/{hotboxid}/{comicid}', name: 'app_addrotation')]
    public function toggleRotation(EntityManagerInterface $entityManager, int $hotboxid, int $comicid): Response
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
            // Remove the rotation
            $entityManager->remove($rotation);
        } else {
            $rotation = new Rotation();
            $rotation->setHotbox($hotbox)->setComic($comic)->calculateNextStart()->calculateExpire();
            $entityManager->persist($rotation);
        }

        $entityManager->flush();
        $this->retimeRotations($entityManager, $hotbox);

        return new RedirectResponse($this->generateUrl('app_edithotbox', ['id' => $hotboxid]));
    }



    protected function retimeRotations(EntityManagerInterface $entityManager, HotBox $hotbox): static
    {
        if(empty($hotbox)) {
            return $this;
        }

        // First sort rotations by date
        $rotations = $hotbox->getRotations()->toArray();
        usort($rotations, function(Rotation $a, Rotation $b){
            if ($a->getStart() === $b->getStart()) {
                return 0;
            }
            return ($a->getStart() < $b->getStart()) ? -1 : 1;
        });
        $now = new \DateTime();
        /**
         * @var Rotation $rotation
         */
        foreach ($rotations as &$rotation) {
            // Move any expired rotations to the back of the line
            if ($now >= $rotation->getExpire()) {
                $rotation->calculateNextStart()->calculateExpire();
                $entityManager->persist($rotation);
            }
        }
        // Resort rotations by date
        usort($rotations, function(Rotation $a, Rotation $b){
            if ($a->getStart() === $b->getStart()) {
                return 0;
            }
            return ($a->getStart() < $b->getStart()) ? -1 : 1;
        });

        $started = RotationFrequencyEnumeration::getStarting($hotbox->getRotationFrequency());
        $startedflag = false;
        foreach ($rotations as &$rotation) {
            if (!$startedflag) {
                $rotation->setStart($started);
                $startedflag = true;
            } else {
                $rotation->setStart(RotationFrequencyEnumeration::getNextStart($hotbox->getRotationFrequency(), $started));
            }
            $rotation->calculateExpire();
            $entityManager->persist($rotation);
            $started = $rotation->getStart();
        }
        $entityManager->flush();
        return $this;
    }


    protected function orderByRotation(EntityManagerInterface $entityManager, array $comics, HotBox $hotBox)
    {
        $inHotBox = [];
        $outHotBox = [];

        $reset = false;

        /**
         * @var Comic $comic
         */
        foreach ($comics as $comic) {
            $seen = false;
            if (!$comic->imageSizeMatch($hotBox) && !$comic->getRotations()->isEmpty()) {

                // Get rotations that belong to this comic and hotbox, then remove them from both comic and hotbox

                $comic->clearRotationsFromHotBox($hotBox);

                $outHotBox[] = $comic;
                continue;
            }
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
        $this->retimeRotations($entityManager, $hotBox);

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

}