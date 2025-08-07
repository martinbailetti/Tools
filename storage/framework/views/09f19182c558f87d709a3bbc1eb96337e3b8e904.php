<?php


$previewBorder = 0.05;
// Función para construir el estilo CSS
$styleAttributes = [];

// Agregar propiedades de estilo si están definidas
if (isset($width)) $styleAttributes[] = "width: {$width}cm";
if (isset($height)) $styleAttributes[] = "height: {$height}cm";
if (isset($marginTop)) $styleAttributes[] = "margin-top: {$marginTop}cm";
if (isset($marginLeft)) $styleAttributes[] = "margin-left: {$marginLeft}cm";

$styleAttributes[] = "position: relative";
$styleAttributes[] = "overflow: hidden";

// Combinar todos los estilos
$styleString = implode('; ', $styleAttributes);
?>

<div style="<?php echo e($styleString); ?>" <?php echo e($attributes); ?>>
    <?php echo e($slot); ?>


    <?php if($preview): ?>
    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['border' => ''.e($previewBorder).'cm dashed black','position' => 'absolute','width' => ''.e($width-$previewBorder*2).'cm','height' => ''.e($height-$previewBorder*2).'cm','top' => '0cm','left' => '0cm']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['border' => ''.e($previewBorder).'cm dashed black','position' => 'absolute','width' => ''.e($width-$previewBorder*2).'cm','height' => ''.e($height-$previewBorder*2).'cm','top' => '0cm','left' => '0cm']); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
    <?php endif; ?>
</div>
<?php /**PATH C:\Projects\tools\resources\views/components/page.blade.php ENDPATH**/ ?>