<?php

declare(strict_types=1);

namespace App\MessageBus\Handler;

use App\MessageBus\Message\AnalyzeCommand;
use App\MessageBus\Message\AnalyzerResult;
use App\Service\ReviewAnalyzer\ChatGptReviewAnalyzer;
use App\Service\ReviewAnalyzer\ReviewAnalyzerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class AnalyzeCommandHandler
{
    private ReviewAnalyzerInterface $analyzer;

    public function __construct(
        #[Autowire(env: 'ANALYZER')] string $analyzerName,
        #[AutowireLocator('app.review_analyzer')] private ServiceLocator $serviceLocator,
        private MessageBusInterface $messageBus
    ) {
        if (!$analyzerName) {
            throw new \InvalidArgumentException('Analyzer must be configured');
        }

        $this->analyzer = $this->serviceLocator->get($analyzerName);
    }

    public function __invoke(AnalyzeCommand $analyzeCommand): void
    {
        $result = $this->analyzer->analyze($analyzeCommand->reviews);

        $this->messageBus->dispatch(new AnalyzerResult($analyzeCommand->uuid, $result));
    }
}
