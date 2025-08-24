<?php

// Main Testimonial resource
$testimonial = tr_resource_pages('Testimonial@\MakerMaker\Controllers\Web\TestimonialController', 'Testimonials')
    ->setIcon('testimonial')
    ->setPosition(2);