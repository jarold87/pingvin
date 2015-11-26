<?php

namespace TestBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductInformation;
use Doctrine\ORM\EntityManager;

class AssocationTest extends Controller
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var Product */
    protected $object;

    /** @var array */
    protected $informationObjects = array();

    /**
     * @Route("/assocation_test", name="front")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->entityManager = $this->getDoctrine()->getManager('customer1');

        $objects = $this->entityManager->getRepository('AppBundle:Product')->findBy(
            array('productId' => 1)
        );
        if ($objects) {
            $this->object = $objects[0];
        } else {
            $this->object = new Product();
            $this->object->setOuterId(1);
        }
        $this->setObjects();
        $this->saveObjects();

        return $this->render('CronBundle::message.html.twig', array(
            'message' => '...',
        ));
    }

    protected function setObjects()
    {
        $this->object->setSku('');
        $this->object->setName('');
        $this->object->setPicture('');
        $this->object->setUrl('');
        $this->object->setManufacturer('');
        $this->object->setCategory('');
        $this->object->setProductCreateDate(new \DateTime());

        $object = new ProductInformation();
        $object->setInformationKey('i1');
        $object->setInformationValue('v4');
        $object->setProduct($this->object);
        $this->informationObjects[] = $object;

        $object = new ProductInformation();
        $object->setInformationKey('i2');
        $object->setInformationValue('v4');
        $object->setProduct($this->object);
        $this->informationObjects[] = $object;
    }

    protected function saveObjects()
    {
        // Product information korábbi elemeinek törlése
        $oldItems = $this->object->getInformation()->toArray();
        if ($oldItems) {
            foreach ($oldItems as $item) {
                $this->entityManager->remove($item);
                $this->object->removeInformation($item);
            }
        }

        // Product information-be új elemek hozzá adása
        if ($this->informationObjects) {
            foreach ($this->informationObjects as $item) {
                $this->object->addInformation($item);
            }
        }

        // Product persist
        $this->entityManager->persist($this->object);

        // Product information persistek
        if ($this->informationObjects) {
            foreach ($this->informationObjects as $object) {
                $this->entityManager->persist($object);
            }
        }

        //Flush
        $this->entityManager->flush();
    }
}