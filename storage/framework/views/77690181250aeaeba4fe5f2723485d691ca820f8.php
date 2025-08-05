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


$firstPageTitleLine1Top=$height* $layout['page1']['title-line-1-y-percentage'] - $layout['page1']['title-line-1-font-size'];
$firstPageTitleLine2Top=$firstPageTitleLine1Top + $layout['page1']['title-line-1-font-size'] + $layout['page1']['title-line-2-margin'];
$firstPageTitleLine3Top=$firstPageTitleLine2Top + $layout['page1']['title-line-2-font-size'] + $layout['page1']['title-line-3-margin'];
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
                font-family: 'ZenAntiqueRegular';
                src: url('<?php echo e(public_path('fonts/ZenAntique-Regular.ttf')); ?>') format('truetype');
            }

            @font-face {
                font-family: 'NotoSerif';
                src: url('<?php echo e(public_path('fonts/NotoSerif-Regular.ttf')); ?>') format('truetype');
            }

            @font-face {
                font-family: 'NotoSerifBold';
                src: url('<?php echo e(public_path('fonts/NotoSerifSC-Bold.ttf')); ?>') format('truetype');
            }

            @font-face {
                font-family: 'NotoSerifItalic';
                src: url('<?php echo e(public_path('fonts/NotoSerif-Italic.ttf')); ?>') format('truetype');
            }

        </style>


        <style>
            p {
                margin: 0;
                padding: 0;
            }


        </style>
    </head>
    <body style="margin: 0cm 0cm;height:100%;">
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['fontFamily' => '\'NotoSerif\'','width' => ''.e($width).'cm','height' => ''.e($height).'cm','marginTop' => ''.e($layout['margin-out']).'cm','marginLeft' => ''.e($layout['margin-in']).'cm','overflow' => 'hidden','position' => 'relative']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['fontFamily' => '\'NotoSerif\'','width' => ''.e($width).'cm','height' => ''.e($height).'cm','marginTop' => ''.e($layout['margin-out']).'cm','marginLeft' => ''.e($layout['margin-in']).'cm','overflow' => 'hidden','position' => 'relative']); ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['fontSize' => ''.e($layout['page1']['title-line-1-font-size']).'cm','lineHeight' => ''.e($layout['page1']['title-line-1-font-size']).'cm','position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($firstPageTitleLine1Top).'cm','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['fontSize' => ''.e($layout['page1']['title-line-1-font-size']).'cm','lineHeight' => ''.e($layout['page1']['title-line-1-font-size']).'cm','position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($firstPageTitleLine1Top).'cm','textAlign' => 'center']); ?>
                <?php echo htmlspecialchars_decode($layout['page1']['title-line-1'] ?? '', ENT_QUOTES); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['fontSize' => ''.e($layout['page1']['title-line-2-font-size']).'cm','lineHeight' => ''.e($layout['page1']['title-line-2-font-size']).'cm','position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($firstPageTitleLine2Top).'cm','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['fontSize' => ''.e($layout['page1']['title-line-2-font-size']).'cm','lineHeight' => ''.e($layout['page1']['title-line-2-font-size']).'cm','position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($firstPageTitleLine2Top).'cm','textAlign' => 'center']); ?>
                <?php echo htmlspecialchars_decode($layout['page1']['title-line-2'] , ENT_QUOTES); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['fontSize' => ''.e($layout['page1']['title-line-3-font-size']).'cm','lineHeight' => ''.e($layout['page1']['title-line-3-font-size']).'cm','position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($firstPageTitleLine3Top).'cm','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['fontSize' => ''.e($layout['page1']['title-line-3-font-size']).'cm','lineHeight' => ''.e($layout['page1']['title-line-3-font-size']).'cm','position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($firstPageTitleLine3Top).'cm','textAlign' => 'center']); ?>
                <?php echo htmlspecialchars_decode($layout['page1']['title-line-3'] , ENT_QUOTES); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($height * $layout['page1']['logo-y-percentage']).'cm','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($height * $layout['page1']['logo-y-percentage']).'cm','textAlign' => 'center']); ?>
                <img src="<?php echo e($layout['page1']['logo-image']); ?>" style="width: auto; height: <?php echo e($layout['page1']['logo-height']); ?>cm;" />
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
        <div style="page-break-after: always;"></div>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['fontFamily' => '\'NotoSerif\'','fontSize' => ''.e($layout['page2']['text-font-size']).'cm','lineHeight' => ''.e($layout['page2']['text-font-size']).'cm','width' => ''.e($width).'cm','height' => ''.e($height).'cm','marginTop' => ''.e($layout['margin-out']).'cm','marginLeft' => ''.e($layout['margin-in']).'cm','overflow' => 'hidden','position' => 'relative']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['fontFamily' => '\'NotoSerif\'','fontSize' => ''.e($layout['page2']['text-font-size']).'cm','lineHeight' => ''.e($layout['page2']['text-font-size']).'cm','width' => ''.e($width).'cm','height' => ''.e($height).'cm','marginTop' => ''.e($layout['margin-out']).'cm','marginLeft' => ''.e($layout['margin-in']).'cm','overflow' => 'hidden','position' => 'relative']); ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($width - $layout['page2']['margin-x']*2).'cm','left' => ''.e($layout['page2']['margin-x']).'cm','top' => ''.e($height - $layout['page2']['margin-bottom'] - $layout['margin-out']).'cm','transform' => 'translateY(-100%)']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($width - $layout['page2']['margin-x']*2).'cm','left' => ''.e($layout['page2']['margin-x']).'cm','top' => ''.e($height - $layout['page2']['margin-bottom'] - $layout['margin-out']).'cm','transform' => 'translateY(-100%)']); ?>

                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['margin' => '0 0 '.e($layout['page2']['text-y-space']).'cm 0']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['margin' => '0 0 '.e($layout['page2']['text-y-space']).'cm 0']); ?>
                    <?php echo $layout['page2']['text-block-1']; ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['margin' => '0 0 '.e($layout['page2']['text-y-space']).'cm 0']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['margin' => '0 0 '.e($layout['page2']['text-y-space']).'cm 0']); ?>
                    <?php echo $layout['page2']['text-block-2']; ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['margin' => '0 0 '.e($layout['page2']['text-y-space']).'cm 0']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['margin' => '0 0 '.e($layout['page2']['text-y-space']).'cm 0']); ?>
                    <?php echo $layout['page2']['text-block-3']; ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => []]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
                    <?php echo $layout['page2']['text-block-4']; ?>

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
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <div style="page-break-after: always;"></div>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['fontFamily' => '\'NotoSerif\'','width' => ''.e($width).'cm','height' => ''.e($height).'cm','marginTop' => ''.e($layout['margin-out']).'cm','marginLeft' => ''.e($layout['margin-in']).'cm','overflow' => 'hidden','position' => 'relative']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['fontFamily' => '\'NotoSerif\'','width' => ''.e($width).'cm','height' => ''.e($height).'cm','marginTop' => ''.e($layout['margin-out']).'cm','marginLeft' => ''.e($layout['margin-in']).'cm','overflow' => 'hidden','position' => 'relative']); ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($height * $layout['page3']['image-y-percentage']).'cm','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($width).'cm','top' => ''.e($height * $layout['page3']['image-y-percentage']).'cm','textAlign' => 'center']); ?>
                <img src="<?php echo e($layout['page3']['image'] ?? ''); ?>" style="width: <?php echo e($width - $layout['page3']['margin']*2); ?>cm; height:auto;" />
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
        <div style="page-break-after: always;"></div>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['backgroundImage' => ''.e($layout['page4']['image']).'','backgroundSize' => 'cover','backgroundPosition' => 'center','width' => ''.e($width).'cm','height' => ''.e($height).'cm','marginTop' => ''.e($layout['margin-out']).'cm','marginLeft' => ''.e($layout['margin-in']).'cm','overflow' => 'hidden','position' => 'relative']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['backgroundImage' => ''.e($layout['page4']['image']).'','backgroundSize' => 'cover','backgroundPosition' => 'center','width' => ''.e($width).'cm','height' => ''.e($height).'cm','marginTop' => ''.e($layout['margin-out']).'cm','marginLeft' => ''.e($layout['margin-in']).'cm','overflow' => 'hidden','position' => 'relative']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <div style="page-break-after: always;"></div>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['fontFamily' => '\'NotoSerif\'','backgroundImage' => ''.e($layout['page5']['image']).'','backgroundSize' => 'cover','backgroundPosition' => 'center','width' => ''.e($width).'cm','height' => ''.e($height).'cm','marginTop' => ''.e($layout['margin-out']).'cm','marginLeft' => ''.e($layout['margin-in']).'cm','overflow' => 'hidden','position' => 'relative']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['fontFamily' => '\'NotoSerif\'','backgroundImage' => ''.e($layout['page5']['image']).'','backgroundSize' => 'cover','backgroundPosition' => 'center','width' => ''.e($width).'cm','height' => ''.e($height).'cm','marginTop' => ''.e($layout['margin-out']).'cm','marginLeft' => ''.e($layout['margin-in']).'cm','overflow' => 'hidden','position' => 'relative']); ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['fontSize' => ''.e($layout['page5']['header-font-size']).'cm','lineHeight' => ''.e($layout['page5']['header-font-size']).'cm','fontFamily' => '\'NotoSerifBold\'','textAlign' => 'center','position' => 'absolute','width' => ''.e($width - $layout['page5']['margin']*2).'cm','height' => ''.e($height).'cm','top' => ''.e($layout['page5']['header-y']).'cm','left' => ''.e($layout['page5']['margin']).'cm']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['fontSize' => ''.e($layout['page5']['header-font-size']).'cm','lineHeight' => ''.e($layout['page5']['header-font-size']).'cm','fontFamily' => '\'NotoSerifBold\'','textAlign' => 'center','position' => 'absolute','width' => ''.e($width - $layout['page5']['margin']*2).'cm','height' => ''.e($height).'cm','top' => ''.e($layout['page5']['header-y']).'cm','left' => ''.e($layout['page5']['margin']).'cm']); ?>
                <?php echo $layout['page5']['header-text']; ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['fontSize' => ''.e($layout['page5']['text-font-size']).'cm','lineHeight' => ''.e($layout['page5']['text-font-size']).'cm','textAlign' => 'center','position' => 'absolute','width' => ''.e($width - $layout['page5']['margin']*2).'cm','height' => ''.e($height).'cm','top' => ''.e($layout['page5']['text-1-y']).'cm','left' => ''.e($layout['page5']['margin']).'cm']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['fontSize' => ''.e($layout['page5']['text-font-size']).'cm','lineHeight' => ''.e($layout['page5']['text-font-size']).'cm','textAlign' => 'center','position' => 'absolute','width' => ''.e($width - $layout['page5']['margin']*2).'cm','height' => ''.e($height).'cm','top' => ''.e($layout['page5']['text-1-y']).'cm','left' => ''.e($layout['page5']['margin']).'cm']); ?>
                <?php echo $layout['page5']['text-1-content']; ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['fontSize' => ''.e($layout['page5']['text-font-size']).'cm','lineHeight' => ''.e($layout['page5']['text-font-size']).'cm','textAlign' => 'center','position' => 'absolute','width' => ''.e($width - $layout['page5']['margin']*2).'cm','height' => ''.e($height).'cm','top' => ''.e($layout['page5']['text-2-y']).'cm','left' => ''.e($layout['page5']['margin']).'cm']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['fontSize' => ''.e($layout['page5']['text-font-size']).'cm','lineHeight' => ''.e($layout['page5']['text-font-size']).'cm','textAlign' => 'center','position' => 'absolute','width' => ''.e($width - $layout['page5']['margin']*2).'cm','height' => ''.e($height).'cm','top' => ''.e($layout['page5']['text-2-y']).'cm','left' => ''.e($layout['page5']['margin']).'cm']); ?>
                <?php echo $layout['page5']['text-2-content']; ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['fontSize' => ''.e($layout['page5']['text-font-size']).'cm','lineHeight' => ''.e($layout['page5']['text-font-size']).'cm','textAlign' => 'center','position' => 'absolute','width' => ''.e($width - $layout['page5']['margin']*2).'cm','height' => ''.e($height).'cm','top' => ''.e($layout['page5']['text-3-y']).'cm','left' => ''.e($layout['page5']['margin']).'cm']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['fontSize' => ''.e($layout['page5']['text-font-size']).'cm','lineHeight' => ''.e($layout['page5']['text-font-size']).'cm','textAlign' => 'center','position' => 'absolute','width' => ''.e($width - $layout['page5']['margin']*2).'cm','height' => ''.e($height).'cm','top' => ''.e($layout['page5']['text-3-y']).'cm','left' => ''.e($layout['page5']['margin']).'cm']); ?>
                <?php echo $layout['page5']['text-3-content']; ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['fontSize' => ''.e($layout['page5']['footer-font-size']).'cm','lineHeight' => ''.e($layout['page5']['footer-font-size']).'cm','fontFamily' => '\'NotoSerifBold\'','textAlign' => 'center','position' => 'absolute','width' => ''.e($width - $layout['page5']['margin']*2).'cm','height' => ''.e($height).'cm','top' => ''.e($layout['page5']['footer-y']).'cm','left' => ''.e($layout['page5']['margin']).'cm']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['fontSize' => ''.e($layout['page5']['footer-font-size']).'cm','lineHeight' => ''.e($layout['page5']['footer-font-size']).'cm','fontFamily' => '\'NotoSerifBold\'','textAlign' => 'center','position' => 'absolute','width' => ''.e($width - $layout['page5']['margin']*2).'cm','height' => ''.e($height).'cm','top' => ''.e($layout['page5']['footer-y']).'cm','left' => ''.e($layout['page5']['margin']).'cm']); ?>
                <?php echo $layout['page5']['footer-text']; ?>

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

        <div style="page-break-after: always;"></div>

        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['width' => ''.e($width).'cm','height' => ''.e($height).'cm','marginTop' => ''.e($layout['margin-out']).'cm','marginLeft' => ''.e($layout['margin-in']).'cm','backgroundColor' => 'black','overflow' => 'hidden','position' => 'relative']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['width' => ''.e($width).'cm','height' => ''.e($height).'cm','marginTop' => ''.e($layout['margin-out']).'cm','marginLeft' => ''.e($layout['margin-in']).'cm','backgroundColor' => 'black','overflow' => 'hidden','position' => 'relative']); ?>

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
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($versoMainLayerWidth).'cm','left' => ''.e($versoMainLayerMargin).'cm','top' => ''.e($versoText1Top).'cm','lineHeight' => ''.e($layout['verso']['primary-font-size']).'cm','fontSize' => ''.e($layout['verso']['primary-font-size']).'cm','color' => 'white','fontFamily' => 'NotoSerif','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($versoMainLayerWidth).'cm','left' => ''.e($versoMainLayerMargin).'cm','top' => ''.e($versoText1Top).'cm','lineHeight' => ''.e($layout['verso']['primary-font-size']).'cm','fontSize' => ''.e($layout['verso']['primary-font-size']).'cm','color' => 'white','fontFamily' => 'NotoSerif','textAlign' => 'center']); ?>
                <?php echo $item['es']; ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($versoMainLayerWidth).'cm','left' => ''.e($versoMainLayerMargin).'cm','top' => ''.e($versoText2Top).'cm','lineHeight' => ''.e($layout['verso']['secondary-font-size']).'cm','fontSize' => ''.e($layout['verso']['secondary-font-size']).'cm','color' => 'white','fontFamily' => 'NotoSerifItalic','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($versoMainLayerWidth).'cm','left' => ''.e($versoMainLayerMargin).'cm','top' => ''.e($versoText2Top).'cm','lineHeight' => ''.e($layout['verso']['secondary-font-size']).'cm','fontSize' => ''.e($layout['verso']['secondary-font-size']).'cm','color' => 'white','fontFamily' => 'NotoSerifItalic','textAlign' => 'center']); ?>
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

        <div style="page-break-after: always;"></div>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['width' => ''.e($width).'cm','height' => ''.e($height).'cm','marginTop' => ''.e($layout['margin-out']).'cm','marginLeft' => ''.e($layout['margin-in']).'cm','overflow' => 'hidden','position' => 'relative']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['width' => ''.e($width).'cm','height' => ''.e($height).'cm','marginTop' => ''.e($layout['margin-out']).'cm','marginLeft' => ''.e($layout['margin-in']).'cm','overflow' => 'hidden','position' => 'relative']); ?>

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
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'black','fontFamily' => 'ZenAntiqueRegular','textAlign' => 'center','transform' => 'translate(-0.1cm, -0.1cm)']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'black','fontFamily' => 'ZenAntiqueRegular','textAlign' => 'center','transform' => 'translate(-0.1cm, -0.1cm)']); ?>
                <?php echo e($item['sym'] ?? ''); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'black','fontFamily' => 'ZenAntiqueRegular','textAlign' => 'center','transform' => 'scale(1.03, 1.03)']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'black','fontFamily' => 'ZenAntiqueRegular','textAlign' => 'center','transform' => 'scale(1.03, 1.03)']); ?>
                <?php echo e($item['sym'] ?? ''); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'black','fontFamily' => 'ZenAntiqueRegular','textAlign' => 'center','transform' => 'scale(0.97, 0.97)']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'black','fontFamily' => 'ZenAntiqueRegular','textAlign' => 'center','transform' => 'scale(0.97, 0.97)']); ?>
                <?php echo e($item['sym'] ?? ''); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.styled-div','data' => ['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'white','fontFamily' => 'ZenAntiqueRegular','textAlign' => 'center']]); ?>
<?php $component->withName('styled-div'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['position' => 'absolute','width' => ''.e($rectoImageLayerWidth).'cm','left' => ''.e($rectoImageLayerMargin).'cm','top' => ''.e($rectoTextTop).'cm','fontSize' => ''.e($layout['recto']['font-size']).'cm','lineHeight' => ''.e($layout['recto']['font-size']).'cm','color' => 'white','fontFamily' => 'ZenAntiqueRegular','textAlign' => 'center']); ?>
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