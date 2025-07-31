<?php
$width = $layout['width']-($layout['margin-in']+$layout['margin-out']);
$height = $layout['height']-($layout['margin-in']+$layout['margin-out']);

$versoWhiteLayerWidth = $width - $layout['verso']['margin'] * 2;
$versoWhiteLayerHeight = $height - $layout['verso']['margin'] * 2;


$versoMainLayerMargin = $layout['verso']['margin']+ $layout['verso']['border-margin'];

$versoMainLayerWidth = $versoWhiteLayerWidth - $layout['verso']['border-margin']*2;
$versoMainLayerHeight = $versoWhiteLayerHeight - $layout['verso']['border-margin']*2;

$versoImageLayerMargin = $versoMainLayerMargin+ $layout['verso']['image-margin'];

$versoImageLayerWidth = $versoMainLayerWidth - $layout['verso']['image-margin']*2;
$versoImageLayerHeight = $versoMainLayerHeight - $layout['verso']['image-margin']*2;


$versoText1Top = $height* $layout['verso']['text-y-percentage'];
$versoText2Top = $versoText1Top+ $layout['verso']['primary-font-size']+ $layout['verso']['text-margin'];

$rectoBlackLayerWidth = $width - $layout['recto']['margin'] * 2;
$rectoBlackLayerHeight = $height - $layout['recto']['margin'] * 2;

$rectoImageLayerWidth = $width - $layout['recto']['margin']*2  - $layout['recto']['image-margin']*2;
$rectoImageLayerHeight = $height - $layout['recto']['margin']*2  - $layout['recto']['image-margin']*2;

$rectoImageLayerMargin = $layout['recto']['margin']+ $layout['recto']['image-margin'];
$rectoTextTop = $height/2 - $layout['recto']['font-size'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo e($title); ?></title>
    <style>
        @page  {
            margin: 0cm 0cm;
        }

        @font-face {
            font-family: 'Zen';
            src: url('<?php echo e(resource_path('fonts/ZenAntique-Regular.ttf')); ?>') format('truetype');
        }

        @font-face {
            font-family: 'NotoSerif';
            src: url('<?php echo e(resource_path('fonts/NotoSerif-Regular.ttf')); ?>') format('truetype');
        }

        @font-face {
            font-family: 'NotoSerifItalic';
            src: url('<?php echo e(resource_path('fonts/NotoSerif-Italic.ttf')); ?>') format('truetype');
        }



        .page-break {
            page-break-after: always;
        }

    </style>
</head>
<body style="margin: 0cm 0cm;height:100%;">


    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <div style="width:<?php echo e($width); ?>cm;height:<?php echo e($height); ?>cm;top:<?php echo e($layout['margin-out']); ?>cm;left:<?php echo e($layout['margin-in']); ?>cm;background-color:black;margin:0cm 0cm;overflow:hidden;position:relative; ">
        <div style="position:absolute;width:<?php echo e($versoWhiteLayerWidth); ?>cm;height:<?php echo e($versoWhiteLayerHeight); ?>cm;top:<?php echo e($layout['verso']['margin']); ?>cm;left:<?php echo e($layout['verso']['margin']); ?>cm;background-color:white;"> </div>

        <div style="position:absolute; width:<?php echo e($versoMainLayerWidth); ?>cm;height:<?php echo e($versoMainLayerHeight); ?>cm;top:<?php echo e($versoMainLayerMargin); ?>cm;left:<?php echo e($versoMainLayerMargin); ?>cm; background-color: black;">
        </div>
        <div style="position:absolute; width:<?php echo e($versoImageLayerWidth); ?>cm;height:<?php echo e($versoImageLayerHeight); ?>cm;top:<?php echo e($versoImageLayerMargin); ?>cm;left:<?php echo e($versoImageLayerMargin); ?>cm; background-image: url('<?php echo e($item['image']['url']); ?>'); background-size: cover; background-position: center;">
        </div>

        <div style="position:absolute;  width:<?php echo e($versoMainLayerWidth); ?>cm;height:<?php echo e($versoMainLayerHeight); ?>cm;top:<?php echo e($versoMainLayerMargin); ?>cm;left:<?php echo e($versoMainLayerMargin); ?>cm; background-color: black; opacity:0.8;">
        </div>
        <div style="position:absolute; width:<?php echo e($versoMainLayerWidth); ?>cm;left:<?php echo e($versoMainLayerMargin); ?>cm;top:<?php echo e($versoText1Top); ?>cm;line-height:<?php echo e($layout['verso']['primary-font-size']); ?>cm;font-size:<?php echo e($layout['verso']['primary-font-size']); ?>cm; color:white; font-family: 'NotoSerif'; text-align:center;">
            <?php echo htmlspecialchars_decode($item['es'], ENT_QUOTES); ?>

        </div>
        <div style="position:absolute; width:<?php echo e($versoMainLayerWidth); ?>cm;left:<?php echo e($versoMainLayerMargin); ?>cm;top:<?php echo e($versoText2Top); ?>cm;line-height:<?php echo e($layout['verso']['secondary-font-size']); ?>cm;font-size:<?php echo e($layout['verso']['secondary-font-size']); ?>cm; color:white; font-family: 'NotoSerifItalic'; text-align:center;">
            <?php echo htmlspecialchars_decode($item['sy2'], ENT_QUOTES); ?>

        </div>
    </div>

    <div style="width:<?php echo e($width); ?>cm;height:<?php echo e($height); ?>cm;top:<?php echo e($layout['margin-out']); ?>cm;left:<?php echo e($layout['margin-in']); ?>cm;margin:0cm 0cm;overflow:hidden;position:relative; ">
        <div style="position:absolute;width:<?php echo e($rectoBlackLayerWidth); ?>cm;height:<?php echo e($rectoBlackLayerHeight); ?>cm;top:<?php echo e($layout['recto']['margin']); ?>cm;left:<?php echo e($layout['recto']['margin']); ?>cm;background-color:black;"> </div>
        <div style="position:absolute; width:<?php echo e($rectoImageLayerWidth); ?>cm;height:<?php echo e($rectoImageLayerHeight); ?>cm;top:<?php echo e($rectoImageLayerMargin); ?>cm;left:<?php echo e($rectoImageLayerMargin); ?>cm; background-image: url('<?php echo e($item['image']['url']); ?>'); background-size: cover; background-position: center;">
        </div>


        <div style="position:absolute; width:<?php echo e($rectoImageLayerWidth); ?>cm;left:<?php echo e($rectoImageLayerMargin); ?>cm;top:<?php echo e($rectoTextTop); ?>cm;font-size:<?php echo e($layout['recto']['font-size']); ?>cm;line-height:<?php echo e($layout['recto']['font-size']); ?>cm; color:black; font-family: 'Zen'; text-align:center;transform: translate(-0.1cm, -0.1cm);">
            <?php echo e($item['sym']); ?>

        </div>
        <div style="position:absolute; width:<?php echo e($rectoImageLayerWidth); ?>cm;left:<?php echo e($rectoImageLayerMargin); ?>cm;top:<?php echo e($rectoTextTop); ?>cm;font-size:<?php echo e($layout['recto']['font-size']); ?>cm;line-height:<?php echo e($layout['recto']['font-size']); ?>cm; color:black; font-family: 'Zen'; text-align:center;transform: scale(1.03, 1.03);">
            <?php echo e($item['sym']); ?>

        </div>
        <div style="position:absolute; width:<?php echo e($rectoImageLayerWidth); ?>cm;left:<?php echo e($rectoImageLayerMargin); ?>cm;top:<?php echo e($rectoTextTop); ?>cm;font-size:<?php echo e($layout['recto']['font-size']); ?>cm;line-height:<?php echo e($layout['recto']['font-size']); ?>cm; color:black; font-family: 'Zen'; text-align:center;transform: scale(0.97, 0.97);">
            <?php echo e($item['sym']); ?>

        </div>
        <div style="position:absolute; width:<?php echo e($rectoImageLayerWidth); ?>cm;left:<?php echo e($rectoImageLayerMargin); ?>cm;top:<?php echo e($rectoTextTop); ?>cm;font-size:<?php echo e($layout['recto']['font-size']); ?>cm;line-height:<?php echo e($layout['recto']['font-size']); ?>cm; color:white; font-family: 'Zen'; text-align:center;">
            <?php echo e($item['sym']); ?>

        </div>
    </div>
    
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>
</html>
<?php /**PATH C:\Projects\tools\resources\views/pdf/colorbook_margin.blade.php ENDPATH**/ ?>