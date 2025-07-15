<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($title); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page  {
            margin: 0cm 0cm;
        }

    </style>
</head>
<body style="margin: 0cm 0cm;">
    <header style="position: fixed;right:0px;top:0px; color:gray; margin-top:-40px;">
        <?php echo e($title); ?>

    </header>
    <div style="text-align: center;margin: 0cm 0cm; padding: 0cm 0cm;">
        <img src="<?php echo e($headerImage); ?>" style="width:100%;height:auto;" />
    </div>

    <div style="text-align: center;margin: 0cm 0cm; padding: 0cm 0cm;position: fixed;top:50%;left:0cm; transform: translateY(-50%);">
        <img src="<?php echo e($contentImage); ?>" style="width:100%;height:auto;" />
    </div>

    <div style="text-align: center;margin: 0cm 0cm; padding: 0cm 0cm;position: fixed;right:0px; bottom:0px;">
        <img src="<?php echo e($footerImage); ?>" style="width:100%;height:auto;" />
    </div>
    <footer style="position: fixed;right:0px; bottom:-20px; color:gray;">
       PrintGen - <?php echo e(\Carbon\Carbon::now()->format('d/m/Y')); ?>

    </footer>
</body>
</html>
<?php /**PATH C:\Projects\tools\resources\views/pdf/default.blade.php ENDPATH**/ ?>