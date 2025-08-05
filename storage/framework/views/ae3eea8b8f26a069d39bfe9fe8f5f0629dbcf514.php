<?php
    // Función para construir el estilo CSS
    $styleAttributes = [];

    // Agregar propiedades de estilo si están definidas
    if (isset($width)) $styleAttributes[] = "width: {$width}";
    if (isset($height)) $styleAttributes[] = "height: {$height}";
    if (isset($fontSize)) $styleAttributes[] = "font-size: {$fontSize}";
    if (isset($lineHeight)) $styleAttributes[] = "line-height: {$lineHeight}";
    if (isset($backgroundImage)) $styleAttributes[] = "background-image: url('{$backgroundImage}')";
    if (isset($backgroundColor)) $styleAttributes[] = "background-color: {$backgroundColor}";
    if (isset($top)) $styleAttributes[] = "top: {$top}";
    if (isset($left)) $styleAttributes[] = "left: {$left}";
    if (isset($color)) $styleAttributes[] = "color: {$color}";
    if (isset($fontFamily)) $styleAttributes[] = "font-family: {$fontFamily}";
    if (isset($marginTop)) $styleAttributes[] = "margin-top: {$marginTop}";
    if (isset($marginLeft)) $styleAttributes[] = "margin-left: {$marginLeft}";
    if (isset($marginRight)) $styleAttributes[] = "margin-right: {$marginRight}";
    if (isset($marginBottom)) $styleAttributes[] = "margin-bottom: {$marginBottom}";

    // Propiedades adicionales comunes
    if (isset($position)) $styleAttributes[] = "position: {$position}";
    if (isset($textAlign)) $styleAttributes[] = "text-align: {$textAlign}";
    if (isset($backgroundSize)) $styleAttributes[] = "background-size: {$backgroundSize}";
    if (isset($backgroundPosition)) $styleAttributes[] = "background-position: {$backgroundPosition}";
    if (isset($margin)) $styleAttributes[] = "margin: {$margin}";
    if (isset($padding)) $styleAttributes[] = "padding: {$padding}";
    if (isset($overflow)) $styleAttributes[] = "overflow: {$overflow}";
    if (isset($opacity)) $styleAttributes[] = "opacity: {$opacity}";
    if (isset($transform)) $styleAttributes[] = "transform: {$transform}";
    if (isset($zIndex)) $styleAttributes[] = "z-index: {$zIndex}";

    // Combinar todos los estilos
    $styleString = implode('; ', $styleAttributes);
?>

<div style="<?php echo e($styleString); ?>" <?php echo e($attributes); ?>>
    <?php echo e($slot); ?>

</div>
<?php /**PATH C:\Projects\tools\resources\views/components/styled-div.blade.php ENDPATH**/ ?>