<?php


namespace App\Component;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class PropertyNameConverter implements NameConverterInterface
{
	private $attributes;


	public function __construct(array $attributes = null)
	{
		$this->attributes = $attributes;
	}


	public function normalize($propertyName) : string
	{
		return isset($this->attributes[$propertyName]) && $this->attributes[$propertyName] ? $this->attributes[$propertyName] : $propertyName;
	}


	public function denormalize($propertyName)
	{
	}
}
