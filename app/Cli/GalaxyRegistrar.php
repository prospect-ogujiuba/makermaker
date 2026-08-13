<?php
namespace Maker\MakerMaker\Cli;

use Maker\MakerMaker\Generator\GeneratorException;

final class GalaxyRegistrar
{
    private const COMMAND = '\\Maker\\MakerMaker\\Cli\\MakerResourceGalaxyCommand::class';

    public function register( string $typeRocketPath ): bool
    {
        $root = realpath( $typeRocketPath );
        if ( $root === false || ! is_dir( $root ) || is_link( $typeRocketPath ) ) {
            throw new GeneratorException( 'TypeRocket path is unavailable or symbolic.' );
        }

        $config = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'galaxy.php';
        if ( ! is_file( $config ) || is_link( $config ) || ! is_writable( $config ) ) {
            throw new GeneratorException( 'TypeRocket Galaxy configuration is unavailable or not writable.' );
        }

        $contents = file_get_contents( $config );
        if ( ! is_string( $contents ) ) {
            throw new GeneratorException( 'Unable to read TypeRocket Galaxy configuration.' );
        }
        if ( str_contains( $contents, self::COMMAND ) ) {
            return false;
        }

        $updated = $this->insertCommand( $contents );

        $temporary = $config . '.makermaker-' . bin2hex( random_bytes( 6 ) );
        try {
            if ( file_put_contents( $temporary, $updated, LOCK_EX ) === false || ! chmod( $temporary, fileperms( $config ) & 0777 ) ) {
                throw new GeneratorException( 'Unable to stage Galaxy configuration.' );
            }
            if ( ! rename( $temporary, $config ) ) {
                throw new GeneratorException( 'Unable to publish Galaxy configuration atomically.' );
            }
        } finally {
            if ( is_file( $temporary ) ) {
                @unlink( $temporary );
            }
        }

        return true;
    }

    private function insertCommand( string $contents ): string
    {
        if ( ! preg_match( "/'commands'\\s*=>\\s*\\[/", $contents, $match, PREG_OFFSET_CAPTURE ) ) {
            throw new GeneratorException( 'Galaxy commands list is not in a supported configuration format.' );
        }

        $opening = $match[0][1] + strlen( $match[0][0] ) - 1;
        $length = strlen( $contents );
        $depth = 1;
        $quote = null;
        $escaped = false;
        $lineComment = false;
        $blockComment = false;
        $closing = null;

        for ( $index = $opening + 1; $index < $length; $index++ ) {
            $character = $contents[$index];
            $next = $index + 1 < $length ? $contents[$index + 1] : '';
            if ( $lineComment ) {
                if ( $character === "\n" ) { $lineComment = false; }
                continue;
            }
            if ( $blockComment ) {
                if ( $character === '*' && $next === '/' ) { $blockComment = false; $index++; }
                continue;
            }
            if ( $quote !== null ) {
                if ( $escaped ) { $escaped = false; continue; }
                if ( $character === '\\' ) { $escaped = true; continue; }
                if ( $character === $quote ) { $quote = null; }
                continue;
            }
            if ( $character === '/' && $next === '/' ) { $lineComment = true; $index++; continue; }
            if ( $character === '#' ) { $lineComment = true; continue; }
            if ( $character === '/' && $next === '*' ) { $blockComment = true; $index++; continue; }
            if ( $character === "'" || $character === '"' ) { $quote = $character; continue; }
            if ( $character === '[' ) { $depth++; continue; }
            if ( $character === ']' && --$depth === 0 ) { $closing = $index; break; }
        }

        if ( $closing === null ) {
            throw new GeneratorException( 'Galaxy commands list is not in a supported configuration format.' );
        }

        $before = substr( $contents, 0, $closing );
        $after = substr( $contents, $closing );
        $lineStart = strrpos( $before, "\n" );
        $closingIndent = $lineStart === false ? '' : substr( $before, $lineStart + 1 );
        $multiline = str_contains( substr( $contents, $opening, $closing - $opening ), "\n" );
        if ( $multiline ) {
            $entryIndent = $closingIndent . '    ';
            $separator = str_ends_with( $before, "\n" ) ? '' : "\n";
            $insertion = $separator . $entryIndent . self::COMMAND . ",\n" . $closingIndent;
        } else {
            $inner = trim( substr( $contents, $opening + 1, $closing - $opening - 1 ) );
            $insertion = ( $inner === '' ? '' : ', ' ) . self::COMMAND;
        }

        return $before . $insertion . $after;
    }
}
