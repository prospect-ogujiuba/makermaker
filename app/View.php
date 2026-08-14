<?php
namespace Maker\MakerMaker;

final class View extends \TypeRocket\Template\View
{
    public function init(): void
    {
        $this->setFolder( dirname( __DIR__ ) . '/resources/views' );
    }
}
