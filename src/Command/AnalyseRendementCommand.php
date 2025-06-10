<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Culture;

#[AsCommand(
    name: 'app:analyse-rendement',
    description: 'Analyse et prédit le rendement des cultures',
)]
class AnalyseRendementCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title(' Analyse du rendement des cultures');

        $cultures = $this->entityManager->getRepository(Culture::class)->findAll();

        if (!$cultures) {
            $io->warning('Aucune culture trouvée.');
            return Command::SUCCESS;
        }

        $io->table(
            ['Nom', 'Surface (ha)', 'Densité', 'Eau (m3)', 'Coût (€)', 'Rendement Moyen (T/ha)', 'Rendement Estimé (T/ha)'],
            array_map(fn($culture) => [
                $culture->getNom(),
                $culture->getSurface(),
                $culture->getDensitePlantation(),
                $culture->getBesoinsEau(),
                $culture->getCoutMoyen(),
                $culture->getRendementMoyen(),
                $this->estimerRendement($culture),
            ], $cultures)
        );

        $io->success('Analyse terminée avec succès.');
        return Command::SUCCESS;
    }

    private function estimerRendement(Culture $culture): float
    {
        $coefDensite = 0.02;
        $coefEau = 0.001;
        $coefCout = 0.005;

        return round(
            ($culture->getDensitePlantation() * $coefDensite) +
            ($culture->getBesoinsEau() * $coefEau) +
            ($culture->getCoutMoyen() * $coefCout),
            2
        );
    }
}
