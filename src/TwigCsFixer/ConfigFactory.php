<?php

declare(strict_types=1);

namespace Umanit\DevBundle\TwigCsFixer;

use TwigCsFixer\Config\Config;
use TwigCsFixer\File\Finder;
use TwigCsFixer\Rules\File\FileExtensionRule;
use TwigCsFixer\Ruleset\Ruleset;
use TwigCsFixer\Standard\TwigCsFixer;

/**
 * Standard UmanIT Twig-CS-Fixer configuration, to use from a project's .twig-cs-fixer.dist.php.
 *
 * ```php
 * return \Umanit\DevBundle\TwigCsFixer\ConfigFactory::create(['templates/']);
 * ```
 */
final class ConfigFactory
{
    private function __construct()
    {
    }

    /**
     * @param list<string> $paths Directories to analyze
     */
    public static function create(array $paths = ['templates/']): Config
    {
        $ruleset = new Ruleset();
        $ruleset->addStandard(new TwigCsFixer());
        $ruleset->addRule(new FileExtensionRule());

        $finder = new Finder();
        $finder->in($paths);

        $config = new Config();
        $config->setRuleset($ruleset);
        $config->allowNonFixableRules();
        $config->setFinder($finder);

        return $config;
    }
}
