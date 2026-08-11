<?php
namespace Maker\MakerMaker\Generator;

final class PluginDefinition
{
    public readonly string $name;
    public readonly string $slug;
    public readonly string $namespace;
    public readonly string $vendor;

    public function __construct( string $name, string $slug, string $namespace, string $vendor = 'maker' )
    {
        $name = trim( $name );
        $slug = trim( $slug );
        $namespace = trim( trim( $namespace ), '\\' );
        $vendor = trim( $vendor );

        if ( $name === '' || strlen( $name ) > 80 || preg_match( '/[\\x00-\\x1F\\x7F]/', $name ) ) {
            throw new GeneratorException( 'Plugin name must contain 1–80 printable characters.' );
        }
        if ( ! preg_match( '/^[a-z][a-z0-9]*(?:-[a-z0-9]+)*$/', $slug ) ) {
            throw new GeneratorException( 'Slug must be lowercase kebab case and start with a letter.' );
        }
        if ( ! preg_match( '/^[A-Za-z_][A-Za-z0-9_]*(?:\\\\[A-Za-z_][A-Za-z0-9_]*)+$/', $namespace ) ) {
            throw new GeneratorException( 'Namespace must contain at least two valid PHP namespace segments.' );
        }
        if ( ! preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $vendor ) ) {
            throw new GeneratorException( 'Package vendor must be lowercase kebab case.' );
        }

        $this->name = $name;
        $this->slug = $slug;
        $this->namespace = $namespace;
        $this->vendor = $vendor;
    }

    public function key(): string
    {
        return str_replace( '-', '_', $this->slug );
    }

    public function className(): string
    {
        return implode( '', array_map( static fn( string $part ): string => ucfirst( $part ), explode( '-', $this->slug ) ) );
    }

    public function packageName(): string
    {
        return $this->vendor . '/' . $this->slug;
    }
}
