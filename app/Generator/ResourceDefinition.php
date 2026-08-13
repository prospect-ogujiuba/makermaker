<?php
namespace Maker\MakerMaker\Generator;

final class ResourceDefinition
{
    private const RESERVED_IDENTIFIERS = [
        '__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case',
        'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do',
        'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif',
        'endswitch', 'endwhile', 'enum', 'eval', 'exit', 'extends', 'final', 'finally',
        'fn', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include',
        'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'match',
        'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'readonly',
        'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try',
        'unset', 'use', 'var', 'while', 'xor', 'yield', 'yield_from',
        // Contextual/type/literal names PHP prohibits for class-like declarations.
        'self', 'parent', 'true', 'false', 'null', 'int', 'float', 'bool', 'string',
        'void', 'iterable', 'object', 'mixed', 'never', 'resource', 'numeric',
    ];

    public readonly string $name;
    public readonly string $namespace;
    private readonly string $plural;

    /** @param array<string, bool> $options */
    public function __construct( string $name, string $namespace, private readonly array $options = [], string $plural = '' )
    {
        $name = trim( $name );
        $namespace = trim( trim( $namespace ), '\\' );
        $plural = trim( $plural );

        if ( ! self::isIdentifier( $name ) || ! preg_match( '/^[A-Z][A-Za-z0-9]{0,63}$/', $name ) ) {
            throw new GeneratorException( 'Resource name must be a non-reserved PascalCase PHP class name of at most 64 characters.' );
        }
        $segments = explode( '\\', $namespace );
        if ( count( $segments ) < 2 || array_filter( $segments, static fn( string $segment ): bool => ! self::isIdentifier( $segment ) ) ) {
            throw new GeneratorException( 'Namespace must contain at least two valid, non-reserved PHP namespace segments.' );
        }
        if ( $plural !== '' && ! preg_match( '/^[a-z][a-z0-9]*(?:_[a-z0-9]+)*$/', $plural ) ) {
            throw new GeneratorException( 'Plural resource name must be lowercase snake_case.' );
        }
        $this->plural = $plural;

        $allowed = [ 'migration', 'views', 'factory', 'tests' ];
        foreach ( $options as $option => $enabled ) {
            if ( ! in_array( $option, $allowed, true ) || ! is_bool( $enabled ) ) {
                throw new GeneratorException( 'Unknown resource generation option: ' . $option . '.' );
            }
        }

        $this->name = $name;
        $this->namespace = $namespace;
    }

    public function enabled( string $option ): bool
    {
        return $this->options[$option] ?? false;
    }

    public function key(): string
    {
        return strtolower( preg_replace( '/(?<!^)[A-Z]/', '_$0', $this->name ) ?? $this->name );
    }

    public function pluralKey(): string
    {
        if ( $this->plural !== '' ) {
            return $this->plural;
        }

        $key = $this->key();
        if ( preg_match( '/[^aeiou]y$/', $key ) ) {
            return substr( $key, 0, -1 ) . 'ies';
        }
        if ( preg_match( '/(?:s|x|z|ch|sh)$/', $key ) ) {
            return $key . 'es';
        }
        return $key . 's';
    }

    private static function isIdentifier( string $identifier ): bool
    {
        return preg_match( '/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier ) === 1
            && ! in_array( strtolower( $identifier ), self::RESERVED_IDENTIFIERS, true );
    }
}
