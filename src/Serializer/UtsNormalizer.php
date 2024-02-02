<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use App\Entity\Uts;

class UtsNormalizer implements ContextAwareNormalizerInterface
{
    private $camelCaseNormalizer;
    private $dateNormalizer;

    public function __construct()
    {
        //$this->camelCaseNormalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        //$this->dateNormalizer = new DateTimeNormalizer('d/m/Y');
    }

    public function normalize($question, $format = null, array $context = [])
    {
        $data = [
            'id' => $question->getId(),
            'name' => $question->getName(),
            'fiche' => $question->getFiche(),
        ];
        return $data;
    }

    public function supportsNormalization($data, $format = null, array $context = []) : bool
    {
        return $data instanceof Uts;
    }
}