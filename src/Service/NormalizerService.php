<?php


namespace App\Service;


use App\Component\PropertyNameConverter;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class NormalizerService
{
	private ?SerializerInterface $serializer;

	private ?NormalizerInterface $normalizer;

	private ?NameConverterInterface $nameConverter;

	public function __construct(
		SerializerInterface $serializer = null,
		NormalizerInterface $normalizer = null,
		NameConverterInterface $nameConverter = null)
	{
		$this->serializer = $serializer;
		$this->normalizer = $normalizer;
		$this->nameConverter = $nameConverter;
	}

	/**
	 * Method construct serializer of objects.
	 * @param array $convertionRules where key is name of property in obj, value is name will should returned
	 */
	public function constructSerializer(array $convertionRules = null) : Serializer
	{
		$normalizer = new ObjectNormalizer(
			null,
			new PropertyNameConverter($convertionRules)
		);
		return new Serializer([$normalizer]);
	}

	/**
	 * Transform collection of classes(with private properties) in array
	 * @param array $collection array of classes
	 * @param array|null $needleAttrs structure of needle array. Example: ['id', 'name']
	 * @param array|null $convertionRules array of rules for convertion values from $needleAttrs. Example: ['id' => 'Identifier']
	 * @param string $sortProperty
	 * @param null $format
	 * @return array|array[]
	 * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
	 */
	public function serializeCollection(array $collection, array $needleArray = null, array $convertionRules = null, string $sortProperty = AbstractNormalizer::ATTRIBUTES, $format = null) : array
	{
		$serializer = $this->constructSerializer($convertionRules);

		return array_map(function($object) use ($serializer, $needleArray, $sortProperty, $format) {
			return $serializer->normalize($object, $format, [$sortProperty => $needleArray]);
		}, $collection);
	}
}
