<?php
namespace Maker\MakerMaker\Generator;

final class ResourceGenerationResult
{
    /** @param list<string> $files */
    public function __construct(
        public readonly string $pluginDirectory,
        public readonly ResourceDefinition $definition,
        public readonly array $files
    ) {
    }
}
