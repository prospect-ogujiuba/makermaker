<?php
/**
 * ServicePricingModel Index View
 * 
 * This view displays a list of servicePricingModels.
 * Add your index/list functionality here.
 */

$table = tr_index_setup(\MakerMaker\Models\ServicePricingModel::class);
$table->render();
?>