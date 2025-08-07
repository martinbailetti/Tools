<?php


$width=$layout['width']-($layout['margin-in']+$layout['margin-out']);
$height=$layout['height']-2*$layout['margin-out'];
$versoWhiteLayerWidth=$width - $layout['verso']['margin'] * 2;
$versoWhiteLayerHeight=$height - $layout['verso']['margin'] * 2;
$versoMainLayerMargin=$layout['verso']['margin']+ $layout['verso']['border-margin'];
$versoMainLayerWidth=$versoWhiteLayerWidth - $layout['verso']['border-margin']*2;
$versoMainLayerHeight=$versoWhiteLayerHeight - $layout['verso']['border-margin']*2;
$versoImageLayerMargin=$versoMainLayerMargin+ $layout['verso']['image-margin'];
$versoImageLayerWidth=$versoMainLayerWidth - $layout['verso']['image-margin']*2;
$versoImageLayerHeight=$versoMainLayerHeight - $layout['verso']['image-margin']*2;

$versoText1Top=$layout['verso']['text-1-top'];
$versoText2Top=$layout['verso']['text-2-top'];


$rectoBlackLayerWidth=$width - $layout['recto']['margin'] * 2;
$rectoBlackLayerHeight=$height - $layout['recto']['margin'] * 2;
$rectoImageLayerWidth=$width - $layout['recto']['margin']*2 - $layout['recto']['image-margin']*2;
$rectoImageLayerHeight=$height - $layout['recto']['margin']*2 - $layout['recto']['image-margin']*2;
$rectoImageLayerMargin=$layout['recto']['margin']+ $layout['recto']['image-margin'];
$rectoTextTop=$layout['recto']['text-top'];


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

        p {
            margin: 0;
            padding: 0;
        }

    </style>



    <style>
        p {
            margin: 0;
            padding: 0;
        }

        .ql-align-center {
            text-align: center;
        }

        .ql-align-left {
            text-align: left;
        }

        .ql-align-right {
            text-align: right;
        }

    </style>
</head>
<body style="margin: 0cm 0cm;height:100%;">


    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.page','data' => ['preview' => ''.e($preview).'','width' => ''.e($width).'','height' => ''.e($height).'','marginTop' => ''.e($layout['margin-out']).'','marginLeft' => ''.e($layout['margin-in']).'']]); ?>
<?php $component->withName('page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['preview' => ''.e($preview).'','width' => ''.e($width).'','height' => ''.e($height).'','marginTop' => ''.e($layout['margin-out']).'','marginLeft' => ''.e($layout['margin-in']).'']); ?>

        <?php if(!empty($layout['page1']['background-url'])): ?>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['backgroundImage' => ''.e($layout['page1']['background-url']).'','backgroundSize' => 'cover','backgroundPosition' => 'center','width' => ''.e($width).'cm','height' => ''.e($height).'cm','top' => '0cm','left' => '0cm','position' => 'absolute']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['backgroundImage' => ''.e($layout['page1']['background-url']).'','backgroundSize' => 'cover','backgroundPosition' => 'center','width' => ''.e($width).'cm','height' => ''.e($height).'cm','top' => '0cm','left' => '0cm','position' => 'absolute']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($width - $layout['page1']['text-margin-x']*2).'cm','left' => ''.e($layout['page1']['text-margin-x']).'cm','top' => ''.e($layout['page1']['text-top']).'cm','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($width - $layout['page1']['text-margin-x']*2).'cm','left' => ''.e($layout['page1']['text-margin-x']).'cm','top' => ''.e($layout['page1']['text-top']).'cm','textAlign' => 'center']); ?>
            <?php echo App\Helpers\FontHelper::addFontFamilyStylesForPdf($layout['page1']['text']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>


        <?php if(!empty($layout['page1']['image-url']) && ($layout['page1']['image-height'] > 0 || $layout['page1']['image-width'] > 0)): ?>
        <?php
        $imgHeight = ($layout['page1']['image-height'] > 0) ? $layout['page1']['image-height'].'cm' : 'auto';
        $imgWidth = ($layout['page1']['image-width'] > 0) ? $layout['page1']['image-width'].'cm' : 'auto';
        ?>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($layout['page1']['image-top']).'cm','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($layout['page1']['image-top']).'cm','textAlign' => 'center']); ?>
            <img src="<?php echo e($layout['page1']['image-url']); ?>" style="width: <?php echo e($imgWidth); ?>; height: <?php echo e($imgHeight); ?>;" />
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <?php endif; ?>


     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.page-break','data' => []]); ?>
<?php $component->withName('page-break'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.page','data' => ['preview' => ''.e($preview).'','width' => ''.e($width).'','height' => ''.e($height).'','marginTop' => ''.e($layout['margin-out']).'','marginLeft' => ''.e($layout['margin-in']).'']]); ?>
<?php $component->withName('page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['preview' => ''.e($preview).'','width' => ''.e($width).'','height' => ''.e($height).'','marginTop' => ''.e($layout['margin-out']).'','marginLeft' => ''.e($layout['margin-in']).'']); ?>

        <?php if(!empty($layout['page2']['background-url'])): ?>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['backgroundImage' => ''.e($layout['page2']['background-url']).'','backgroundSize' => 'cover','backgroundPosition' => 'center','width' => ''.e($width).'cm','height' => ''.e($height).'cm','top' => '0cm','left' => '0cm','position' => 'absolute']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['backgroundImage' => ''.e($layout['page2']['background-url']).'','backgroundSize' => 'cover','backgroundPosition' => 'center','width' => ''.e($width).'cm','height' => ''.e($height).'cm','top' => '0cm','left' => '0cm','position' => 'absolute']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($width - $layout['page2']['text-margin-x']*2).'cm','left' => ''.e($layout['page2']['text-margin-x']).'cm','top' => ''.e($layout['page2']['text-top']).'cm','textAlign' => 'left']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($width - $layout['page2']['text-margin-x']*2).'cm','left' => ''.e($layout['page2']['text-margin-x']).'cm','top' => ''.e($layout['page2']['text-top']).'cm','textAlign' => 'left']); ?>
            <?php echo App\Helpers\FontHelper::addFontFamilyStylesForPdf($layout['page2']['text']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if(!empty($layout['page2']['image-url']) && ($layout['page2']['image-height'] > 0 || $layout['page2']['image-width'] > 0)): ?>
        <?php
        $imgHeight = ($layout['page2']['image-height'] > 0) ? $layout['page2']['image-height'].'cm' : 'auto';
        $imgWidth = ($layout['page2']['image-width'] > 0) ? $layout['page2']['image-width'].'cm' : 'auto';
        ?>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($layout['page2']['image-top']).'cm','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($layout['page2']['image-top']).'cm','textAlign' => 'center']); ?>
            <img src="<?php echo e($layout['page2']['image-url']); ?>" style="width: <?php echo e($imgWidth); ?>; height: <?php echo e($imgHeight); ?>;" />
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <?php endif; ?>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.page-break','data' => []]); ?>
<?php $component->withName('page-break'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.page','data' => ['preview' => ''.e($preview).'','width' => ''.e($width).'','height' => ''.e($height).'','marginTop' => ''.e($layout['margin-out']).'','marginLeft' => ''.e($layout['margin-in']).'']]); ?>
<?php $component->withName('page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['preview' => ''.e($preview).'','width' => ''.e($width).'','height' => ''.e($height).'','marginTop' => ''.e($layout['margin-out']).'','marginLeft' => ''.e($layout['margin-in']).'']); ?>

        <?php if(!empty($layout['page3']['background-url'])): ?>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['backgroundImage' => ''.e($layout['page3']['background-url']).'','backgroundSize' => 'cover','backgroundPosition' => 'center','width' => ''.e($width).'cm','height' => ''.e($height).'cm','top' => '0cm','left' => '0cm','position' => 'absolute']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['backgroundImage' => ''.e($layout['page3']['background-url']).'','backgroundSize' => 'cover','backgroundPosition' => 'center','width' => ''.e($width).'cm','height' => ''.e($height).'cm','top' => '0cm','left' => '0cm','position' => 'absolute']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($width - $layout['page3']['text-margin-x']*2).'cm','left' => ''.e($layout['page3']['text-margin-x']).'cm','top' => ''.e($layout['page3']['text-top']).'cm','textAlign' => 'left']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($width - $layout['page3']['text-margin-x']*2).'cm','left' => ''.e($layout['page3']['text-margin-x']).'cm','top' => ''.e($layout['page3']['text-top']).'cm','textAlign' => 'left']); ?>
            <?php echo App\Helpers\FontHelper::addFontFamilyStylesForPdf($layout['page3']['text']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <?php if(!empty($layout['page3']['image-url']) && ($layout['page3']['image-height'] > 0 || $layout['page3']['image-width'] > 0)): ?>
        <?php
        $imgHeight = ($layout['page3']['image-height'] > 0) ? $layout['page3']['image-height'].'cm' : 'auto';
        $imgWidth = ($layout['page3']['image-width'] > 0) ? $layout['page3']['image-width'].'cm' : 'auto';
        ?>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($layout['page3']['image-top']).'cm','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($layout['page3']['image-top']).'cm','textAlign' => 'center']); ?>
            <img src="<?php echo e($layout['page3']['image-url']); ?>" style="width: <?php echo e($imgWidth); ?>; height: <?php echo e($imgHeight); ?>;" />
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <?php endif; ?>
        <?php if(!empty($layout['page3']['image-url']) && $layout['page3']['image-height'] > 0): ?>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($layout['page3']['image-top']).'cm','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($layout['page3']['image-top']).'cm','textAlign' => 'center']); ?>
            <img src="<?php echo e($layout['page3']['image-url']); ?>" style="width: <?php echo e($layout['page3']['image-width']); ?>; height: <?php echo e($layout['page3']['image-height']); ?>cm;" />
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <?php endif; ?>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.page-break','data' => []]); ?>
<?php $component->withName('page-break'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>


    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.page','data' => ['preview' => ''.e($preview).'','width' => ''.e($width).'','height' => ''.e($height).'','marginTop' => ''.e($layout['margin-out']).'','marginLeft' => ''.e($layout['margin-in']).'']]); ?>
<?php $component->withName('page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['preview' => ''.e($preview).'','width' => ''.e($width).'','height' => ''.e($height).'','marginTop' => ''.e($layout['margin-out']).'','marginLeft' => ''.e($layout['margin-in']).'']); ?>
        <?php if(!empty($layout['page4']['background-url'])): ?>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['backgroundImage' => ''.e($layout['page4']['background-url']).'','backgroundSize' => 'cover','backgroundPosition' => 'center','width' => ''.e($width).'cm','height' => ''.e($height).'cm','top' => '0cm','left' => '0cm','position' => 'absolute']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['backgroundImage' => ''.e($layout['page4']['background-url']).'','backgroundSize' => 'cover','backgroundPosition' => 'center','width' => ''.e($width).'cm','height' => ''.e($height).'cm','top' => '0cm','left' => '0cm','position' => 'absolute']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($width - $layout['page4']['text-margin-x']*2).'cm','left' => ''.e($layout['page4']['text-margin-x']).'cm','top' => ''.e($layout['page4']['text-top']).'cm','textAlign' => 'left']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($width - $layout['page4']['text-margin-x']*2).'cm','left' => ''.e($layout['page4']['text-margin-x']).'cm','top' => ''.e($layout['page4']['text-top']).'cm','textAlign' => 'left']); ?>
            <?php echo App\Helpers\FontHelper::addFontFamilyStylesForPdf($layout['page4']['text']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if(!empty($layout['page4']['image-url']) && ($layout['page4']['image-height'] > 0 || $layout['page4']['image-width'] > 0)): ?>
        <?php
        $imgHeight = ($layout['page4']['image-height'] > 0) ? $layout['page4']['image-height'].'cm' : 'auto';
        $imgWidth = ($layout['page4']['image-width'] > 0) ? $layout['page4']['image-width'].'cm' : 'auto';
        ?>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($layout['page4']['image-top']).'cm','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($layout['page4']['image-top']).'cm','textAlign' => 'center']); ?>
            <img src="<?php echo e($layout['page4']['image-url']); ?>" style="width: <?php echo e($imgWidth); ?>; height: <?php echo e($imgHeight); ?>;" />
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <?php endif; ?>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.page-break','data' => []]); ?>
<?php $component->withName('page-break'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>



    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.page','data' => ['preview' => ''.e($preview).'','width' => ''.e($width).'','height' => ''.e($height).'','marginTop' => ''.e($layout['margin-out']).'','marginLeft' => ''.e($layout['margin-in']).'']]); ?>
<?php $component->withName('page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['preview' => ''.e($preview).'','width' => ''.e($width).'','height' => ''.e($height).'','marginTop' => ''.e($layout['margin-out']).'','marginLeft' => ''.e($layout['margin-in']).'']); ?>

        <?php if(!empty($layout['page5']['background-url'])): ?>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['backgroundImage' => ''.e($layout['page5']['background-url']).'','backgroundSize' => 'cover','backgroundPosition' => 'center','width' => ''.e($width).'cm','height' => ''.e($height).'cm','top' => '0cm','left' => '0cm','position' => 'absolute']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['backgroundImage' => ''.e($layout['page5']['background-url']).'','backgroundSize' => 'cover','backgroundPosition' => 'center','width' => ''.e($width).'cm','height' => ''.e($height).'cm','top' => '0cm','left' => '0cm','position' => 'absolute']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($width - $layout['page5']['text-margin-x']*2).'cm','left' => ''.e($layout['page5']['text-margin-x']).'cm','top' => ''.e($layout['page5']['text-top']).'cm','textAlign' => 'left']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($width - $layout['page5']['text-margin-x']*2).'cm','left' => ''.e($layout['page5']['text-margin-x']).'cm','top' => ''.e($layout['page5']['text-top']).'cm','textAlign' => 'left']); ?>
            <?php echo App\Helpers\FontHelper::addFontFamilyStylesForPdf($layout['page5']['text']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if(!empty($layout['page5']['image-url']) && ($layout['page5']['image-height'] > 0 || $layout['page5']['image-width'] > 0)): ?>
        <?php
        $imgHeight = ($layout['page5']['image-height'] > 0) ? $layout['page5']['image-height'].'cm' : 'auto';
        $imgWidth = ($layout['page5']['image-width'] > 0) ? $layout['page5']['image-width'].'cm' : 'auto';
        ?>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($layout['page5']['image-top']).'cm','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($layout['page5']['image-top']).'cm','textAlign' => 'center']); ?>
            <img src="<?php echo e($layout['page5']['image-url']); ?>" style="width: <?php echo e($imgWidth); ?>; height: <?php echo e($imgHeight); ?>;" />
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <?php endif; ?>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>



    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.page-break','data' => []]); ?>
<?php $component->withName('page-break'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.page','data' => ['preview' => ''.e($preview).'','width' => ''.e($width).'','height' => ''.e($height).'','marginTop' => ''.e($layout['margin-out']).'','marginLeft' => ''.e($layout['margin-in']).'']]); ?>
<?php $component->withName('page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['preview' => ''.e($preview).'','width' => ''.e($width).'','height' => ''.e($height).'','marginTop' => ''.e($layout['margin-out']).'','marginLeft' => ''.e($layout['margin-in']).'']); ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($versoWhiteLayerWidth).'cm','height' => ''.e($versoWhiteLayerHeight).'cm','top' => ''.e($layout['verso']['margin']).'cm','left' => ''.e($layout['verso']['margin']).'cm','backgroundColor' => 'white']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($versoWhiteLayerWidth).'cm','height' => ''.e($versoWhiteLayerHeight).'cm','top' => ''.e($layout['verso']['margin']).'cm','left' => ''.e($layout['verso']['margin']).'cm','backgroundColor' => 'white']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($versoMainLayerWidth).'cm','height' => ''.e($versoMainLayerHeight).'cm','top' => ''.e($versoMainLayerMargin).'cm','left' => ''.e($versoMainLayerMargin).'cm','backgroundColor' => 'black']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($versoMainLayerWidth).'cm','height' => ''.e($versoMainLayerHeight).'cm','top' => ''.e($versoMainLayerMargin).'cm','left' => ''.e($versoMainLayerMargin).'cm','backgroundColor' => 'black']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($versoImageLayerWidth).'cm','height' => ''.e($versoImageLayerHeight).'cm','top' => ''.e($versoImageLayerMargin).'cm','left' => ''.e($versoImageLayerMargin).'cm','backgroundImage' => ''.e($item['image']['url'] ?? '').'','backgroundSize' => 'cover','backgroundPosition' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($versoImageLayerWidth).'cm','height' => ''.e($versoImageLayerHeight).'cm','top' => ''.e($versoImageLayerMargin).'cm','left' => ''.e($versoImageLayerMargin).'cm','backgroundImage' => ''.e($item['image']['url'] ?? '').'','backgroundSize' => 'cover','backgroundPosition' => 'center']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($versoMainLayerWidth).'cm','height' => ''.e($versoMainLayerHeight).'cm','top' => ''.e($versoMainLayerMargin).'cm','left' => ''.e($versoMainLayerMargin).'cm','backgroundColor' => 'black','opacity' => '0.8']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($versoMainLayerWidth).'cm','height' => ''.e($versoMainLayerHeight).'cm','top' => ''.e($versoMainLayerMargin).'cm','left' => ''.e($versoMainLayerMargin).'cm','backgroundColor' => 'black','opacity' => '0.8']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>


        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($versoMainLayerWidth).'cm','left' => ''.e($versoMainLayerMargin).'cm','top' => ''.e($versoText1Top).'cm','lineHeight' => ''.e($layout['verso']['primary-font-size']).'cm','fontSize' => ''.e($layout['verso']['primary-font-size']).'cm','color' => 'white','fontFamily' => ''.e($layout['verso']['text-1-font-family']).'','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($versoMainLayerWidth).'cm','left' => ''.e($versoMainLayerMargin).'cm','top' => ''.e($versoText1Top).'cm','lineHeight' => ''.e($layout['verso']['primary-font-size']).'cm','fontSize' => ''.e($layout['verso']['primary-font-size']).'cm','color' => 'white','fontFamily' => ''.e($layout['verso']['text-1-font-family']).'','textAlign' => 'center']); ?>
            <?php echo $item['es']; ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($versoMainLayerWidth).'cm','left' => ''.e($versoMainLayerMargin).'cm','top' => ''.e($versoText2Top).'cm','lineHeight' => ''.e($layout['verso']['secondary-font-size']).'cm','fontSize' => ''.e($layout['verso']['secondary-font-size']).'cm','color' => 'white','fontFamily' => ''.e($layout['verso']['text-2-font-family']).'','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($versoMainLayerWidth).'cm','left' => ''.e($versoMainLayerMargin).'cm','top' => ''.e($versoText2Top).'cm','lineHeight' => ''.e($layout['verso']['secondary-font-size']).'cm','fontSize' => ''.e($layout['verso']['secondary-font-size']).'cm','color' => 'white','fontFamily' => ''.e($layout['verso']['text-2-font-family']).'','textAlign' => 'center']); ?>
            <?php echo $item['sy2']; ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>


     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>


    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.page-break','data' => []]); ?>
<?php $component->withName('page-break'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.page','data' => ['preview' => ''.e($preview).'','width' => ''.e($width).'','height' => ''.e($height).'','marginTop' => ''.e($layout['margin-out']).'','marginLeft' => ''.e($layout['margin-in']).'']]); ?>
<?php $component->withName('page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['preview' => ''.e($preview).'','width' => ''.e($width).'','height' => ''.e($height).'','marginTop' => ''.e($layout['margin-out']).'','marginLeft' => ''.e($layout['margin-in']).'']); ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($rectoBlackLayerWidth).'cm','height' => ''.e($rectoBlackLayerHeight).'cm','top' => ''.e($layout['recto']['margin']).'cm','left' => ''.e($layout['recto']['margin']).'cm','backgroundColor' => 'black']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($rectoBlackLayerWidth).'cm','height' => ''.e($rectoBlackLayerHeight).'cm','top' => ''.e($layout['recto']['margin']).'cm','left' => ''.e($layout['recto']['margin']).'cm','backgroundColor' => 'black']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','height' => ''.e($rectoImageLayerHeight).'cm','top' => ''.e($rectoImageLayerMargin).'cm','left' => ''.e($rectoImageLayerMargin).'cm','backgroundImage' => ''.e($item['image']['url'] ?? '').'','backgroundSize' => 'cover','backgroundPosition' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','height' => ''.e($rectoImageLayerHeight).'cm','top' => ''.e($rectoImageLayerMargin).'cm','left' => ''.e($rectoImageLayerMargin).'cm','backgroundImage' => ''.e($item['image']['url'] ?? '').'','backgroundSize' => 'cover','backgroundPosition' => 'center']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <!-- Texto con efecto de sombra múltiple -->
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'black','fontFamily' => ''.e($layout['recto']['text-font-family']).'','textAlign' => 'center','transform' => 'translate(-0.1cm, -0.1cm)']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'black','fontFamily' => ''.e($layout['recto']['text-font-family']).'','textAlign' => 'center','transform' => 'translate(-0.1cm, -0.1cm)']); ?>
            <?php echo e($item['sym'] ?? ''); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'black','fontFamily' => ''.e($layout['recto']['text-font-family']).'','textAlign' => 'center','transform' => 'scale(1.03, 1.03)']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'black','fontFamily' => ''.e($layout['recto']['text-font-family']).'','textAlign' => 'center','transform' => 'scale(1.03, 1.03)']); ?>
            <?php echo e($item['sym'] ?? ''); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'black','fontFamily' => ''.e($layout['recto']['text-font-family']).'','textAlign' => 'center','transform' => 'scale(0.97, 0.97)']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'black','fontFamily' => ''.e($layout['recto']['text-font-family']).'','textAlign' => 'center','transform' => 'scale(0.97, 0.97)']); ?>
            <?php echo e($item['sym'] ?? ''); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'white','fontFamily' => ''.e($layout['recto']['text-font-family']).'','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'white','fontFamily' => ''.e($layout['recto']['text-font-family']).'','textAlign' => 'center']); ?>
            <?php echo e($item['sym'] ?? ''); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>
</html>
<?php /**PATH C:\Projects\tools\resources\views/pdf/colorbook_margin.blade.php ENDPATH**/ ?>