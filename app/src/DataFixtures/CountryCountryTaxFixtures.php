<?php

namespace App\DataFixtures;

use App\Entity\Country;
use App\Entity\CountryTax;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CountryCountryTaxFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $country = new Country();
        $country->setName('Германия');
        $country->setCode('DE');
        $manager->persist($country);

        $countryTax = new CountryTax();
        $countryTax->setCountry($country);
        $countryTax->setValue(19);
        $countryTax->setRule('/^(DE|de)\d{9}$/');
        $manager->persist($countryTax);

        $country = new Country();
        $country->setName('Италия');
        $country->setCode('IT');
        $manager->persist($country);

        $countryTax = new CountryTax();
        $countryTax->setCountry($country);
        $countryTax->setValue(22);
        $countryTax->setRule('/^(IT|it)\d{11}$/');
        $manager->persist($countryTax);

        $country = new Country();
        $country->setName('Франция');
        $country->setCode('FR');
        $manager->persist($country);

        $countryTax = new CountryTax();
        $countryTax->setCountry($country);
        $countryTax->setValue(20);
        $countryTax->setRule('/^(FR|fr)\[A-Za-z]{2}\d{9}$/');
        $manager->persist($countryTax);

        $country = new Country();
        $country->setName('Греция');
        $country->setCode('GR');
        $manager->persist($country);

        $countryTax = new CountryTax();
        $countryTax->setCountry($country);
        $countryTax->setValue(24);
        $countryTax->setRule('/^(GR|gr)\d{9}$/');
        $manager->persist($countryTax);

        $manager->flush();
    }
}
