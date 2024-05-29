<?php

declare(strict_types=1);

namespace App\MessageBus\Handler;

use App\MessageBus\Message\AnalyzeCommand;
use App\MessageBus\Message\AnalyzerResult;
use App\Service\ReviewAnalyzer\BaseReviewsAnalyzer;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class AnalyzeCommandHandler
{
    public function __construct(
        #[AutowireLocator('app.reviews_analyzer')] private ServiceLocator $serviceLocator,
        private MessageBusInterface $messageBus
    ) {
    }

    public function __invoke(AnalyzeCommand $analyzeCommand): void
    {
        $analyzer = $this->getAnalyzerService($analyzeCommand->analyzer);
        $result = $analyzer->analyze($analyzeCommand->reviews);

        $this->messageBus->dispatch(new AnalyzerResult($analyzeCommand->uuid, $result));
    }

    private function getAnalyzerService(string $analyzerName): BaseReviewsAnalyzer
    {
        foreach ($this->serviceLocator->getProvidedServices() as $service) {
            if (!is_subclass_of($service, BaseReviewsAnalyzer::class)) {
                throw new \RuntimeException('Service must extends ' . BaseReviewsAnalyzer::class);
            }

            if ($service::getName() === $analyzerName) {
                return $this->serviceLocator->get($service);
            }
        }

        throw new \RuntimeException('Analyzer not found');
    }
}
