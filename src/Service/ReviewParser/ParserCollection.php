<?php

declare(strict_types=1);

namespace App\Service\ReviewParser;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class ParserCollection
{
    /**
     * @var array<ReviewParserInterface>
     */
    private array $parsers = [];

    /**
     * @param iterable<int, ReviewParserInterface> $parsers
     */
    public function __construct(#[AutowireIterator('app.review_parser')] iterable $parsers)
    {
        foreach ($parsers as $parser) {
            if (!$parser instanceof ReviewParserInterface) {
                throw new \InvalidArgumentException('Invalid parser');
            }
        }

        $this->parsers = iterator_to_array($parsers);
    }

    public function getParser(string $url): ReviewParserInterface
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($url)) {
                return $parser;
            }
        }

        throw new \InvalidArgumentException('Url not supported');
    }
}
