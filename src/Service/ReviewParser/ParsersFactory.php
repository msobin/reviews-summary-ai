<?php

declare(strict_types=1);

namespace App\Service\ReviewParser;

use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ParsersFactory
{
    /** @var array <array-key, class-string> */
    private array $parsers = [];

    public function __construct(#[AutowireLocator('app.review_parser')] private readonly ServiceLocator $serviceLocator)
    {
        foreach ($this->serviceLocator->getProvidedServices() as $parser) {
            $this->parsers[] = $parser;
        }
    }

    public function getParser(string $url): ReviewParserInterface
    {
        foreach ($this->parsers as $parser) {
            if ($parser::isSupports($url)) {
                return $this->serviceLocator->get($parser);
            }
        }

        throw new \RuntimeException('Url not supported');
    }

    public function hasParser(string $url): bool
    {
        foreach ($this->parsers as $parser) {
            if ($parser::isSupports($url)) {
                return true;
            }
        }

        return false;
    }
}
