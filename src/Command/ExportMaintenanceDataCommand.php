<?php
namespace App\Command;

 use App\Entity\Maintenance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use League\Csv\Writer;

class ExportMaintenanceDataCommand extends Command
{
    protected static $defaultName = 'app:export-maintenance-data';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Exports maintenance data to a CSV file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $maintenances = $this->entityManager->getRepository(Maintenance::class)->findAll();

        $csv = Writer::createFromPath('maintenance_data.csv', 'w+');
        $csv->insertOne(['dateEntretien', 'cout', 'temperature', 'humidite', 'consoCarburant', 'consoEnergie', 'Status', 'idMachine']);

        foreach ($maintenances as $maintenance) {
            $csv->insertOne([
                $maintenance->getDateEntretien()->format('Y-m-d'),
                $maintenance->getCout(),
                $maintenance->getTemperature(),
                $maintenance->getHumidite(),
                $maintenance->getConsoCarburant(),
                $maintenance->getConsoEnergie(),
                $maintenance->getStatus()->value,
                $maintenance->getIdMachine()->getId(),
            ]);
        }

        $io->success('Maintenance data exported to maintenance_data.csv');
        return Command::SUCCESS;
    }
}
//The script will:

//Load the data from maintenance_data.csv.

//Train a RandomForestClassifier model.

//Evaluate the model's accuracy on a test set.

//Save the trained model as maintenance_model.pkl.
//Install Flask:
//Create a Flask API:
//Run the Flask API: