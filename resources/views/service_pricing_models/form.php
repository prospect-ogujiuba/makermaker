<?php
/**
 * ServicePricingModel Form View
 * 
 * This view displays a form for creating/editing ServicePricingModel.
 * Add your form fields and functionality here.
 */

$tabs = tr_form_tabs(\MakerMaker\Models\ServicePricingModel::class);
$tabs->setFooter($form->submit('Save Changes'));
$tabs->render();
?>