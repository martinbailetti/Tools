<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documento PDF</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.5;font-size:16px">
    <header style="position: fixed;right:0px;top:0px; color:gray; margin-top:-40px;">
        <?php echo e(\Carbon\Carbon::now()->format('d/m/Y')); ?>

    </header>
    <footer style="position: fixed;right:0px; bottom:-20px; color:gray;">
        SMI2000
    </footer>
    <table style="width: 100%; border-bottom: 1px solid #000; padding-bottom: 10px;">
        <tr>
            <td><img src="<?php echo e(public_path("/images/logo.png")); ?>" style="width: auto; height: 100px;" alt="Logo"></td>
            <td style="font-weight:bold;text-transform:uppercase;font-size:30px">Reporte de Tarea</td>
        </tr>
    </table>
    <table width="100%" style="margin-top: 20px;">
        <tr>
            <td style="colspan: 2">
                <strong>Asunto:</strong>
                <span><?php echo e($task->subject); ?></span>
            </td>

        </tr>
        <tr>
            <td width="50%">
                <strong>Cliente:</strong>
                <span><?php echo e($task->client_name); ?></span>
            </td>
            <td width="50%">
                <strong>Contacto:</strong>
                <span><?php echo e($task->client_contact); ?></span>
            </td>

        </tr>
        <tr>
            <td width="50%">
                <strong>Fecha y Hora:</strong>
                <span><?php echo e($task->task_datetime); ?></span>
            </td>
            <td width="50%">
                <strong>Horas:</strong>
                <span><?php echo e($task->task_hours); ?></span>
            </td>

        </tr>
        <tr>
            <td width="50%">
                <strong>Responsable:</strong>
                <span><?php echo e($task->user_name); ?></span>
            </td>
            <td width="50%">
                <strong>Identificador:</strong>
                <span>TASK_<?php echo e($task->id); ?></span>
            </td>

        </tr>
        <?php if(count($machines) > 0): ?>
        <tr>
            <td colspan="2">
                <strong>Máquina:</strong><br />
                <?php $__currentLoopData = $machines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $machine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span><?php echo e($machine->machine); ?></span><br />
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </td>
        </tr>
        <?php endif; ?>




    </table>

    <strong>Descripción:</strong>
    <div><?php echo nl2br(e($task->description)); ?></div>

    <div style="page-break-after: always;"></div>
    <?php if(count($files) > 0): ?>


    <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
    $ext = strtolower(pathinfo($file->path, PATHINFO_EXTENSION));
    ?>
    <?php if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])): ?>
    <div style="text-align: center; margin: 50px 0;">
        <img src="<?php echo e(public_path($file->path)); ?>" style="max-width:80%;width:auto;max-height:800px;height:auto" />
    </div>
    <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\Projects\eTasks\tasks_api\resources\views/pdf.blade.php ENDPATH**/ ?>