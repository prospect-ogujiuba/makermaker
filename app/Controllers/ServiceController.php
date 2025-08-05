<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\Service;
use TypeRocket\Controllers\WPPostController;

class ServiceController extends WPPostController
{
    protected $modelClass = Service::class;
}