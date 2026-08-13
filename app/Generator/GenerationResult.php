<?php
namespace Maker\MakerMaker\Generator;

final class GenerationResult
{
    public function __construct(
        public readonly string $directory,
        public readonly string $entryFile,
        public readonly PluginDefinition $definition,
        public readonly string $galaxyLauncher = '',
        public readonly string $galaxyConfig = ''
    ) {
    }
}
