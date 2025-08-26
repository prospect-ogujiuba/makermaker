<?php

/**
 * ServiceComplexity Form View
 * 
 * This view displays a form for creating/editing ServiceComplexity.
 * Add your form fields and functionality here.
 */
// Simple form with auto-detected fields
// Auto-configured tabs with smart field distribution
$tabs = tr_form_tabs(\MakerMaker\Models\ServiceComplexity::class);
$tabs->setFooter($form->submit('Save Changes'));
$tabs->render();
