<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;
use App\Repository\CharacterRepository;

#[AsCommand(
    name: 'app:create-sitemaps',
    description: 'Add a short description for your command',
)]
class CreateSitemapsCommand extends Command
{
    public const BASE_URL = 'https://la-guilde-des-seigneurs.com';
    public const FOLDER = __DIR__ . '/../../public';

    public function __construct(
        private readonly CharacterRepository $characterRepository,
        private readonly Environment $env,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    protected function execute2(InputInterface $input, OutputInterface $output): int
    {
        $this->createSitemapIndex(); // Appelle la création du fichier d'index
        $this->createSitemapSite();// Appelle la création du fichier de sitemap
        // Ouputs message
        $output->writeln('Sitemaps created!');
        return Command::SUCCESS;
    }

    // Creates sitemap index
    public function createSitemapIndex()
    {
        // Defines sitemaps
        $sitemaps = [
            self::BASE_URL . '/sitemap-site.xml',
        ];
        // Writes file
        $sitemapIndexContent = $this->env->render(
            'sitemaps/sitemap-index.xml.twig',
            ['sitemaps' => $sitemaps]
        );
        $sitemapIndexFile = self::FOLDER . '/sitemap-index.xml';
        file_put_contents($sitemapIndexFile, $sitemapIndexContent);
    }

    // Creates the sitemap for pages specific to site
    public function createSitemapSite()
    {
        // Defines pages to be used
        $pagesList = $this->getPages();
        // Creates content
        $pages = [];
        foreach ($pagesList as $url => $value) {
            $values = explode(',', $value);
            $pages[] = [
                'loc' => self::BASE_URL . '/' . $url,
                'lastmod' => null,
                'changefreq' => trim($values[0]),
                'priority' => trim($values[1])
            ];
        }
        // Writes file
        $sitemapContent = $this->env->render(
            'sitemaps/sitemap.xml.twig',
            ['pages' => $pages]
        );
        $sitemapFile = self::FOLDER . '/sitemap-site.xml';
        file_put_contents($sitemapFile, $sitemapContent);
    }

    // Returns the list of pages
    public function getPages(): array
    {
        // Static pages
        $pagesList = [
            '' => 'weekly, 1.0',
            'character' => 'weekly, 0.9',
        ];
        // Pages for Characters from DB
        $characters = $this->characterRepository->findAll();
        foreach ($characters as $character) {
            $pagesList['character/' . $character->getId()] = 'weekly, 0.8';
        }
        return $pagesList;
    }
}
