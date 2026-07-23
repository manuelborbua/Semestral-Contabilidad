<?php $datos = $colaborador ?? $_POST; ?>
<form method="POST" class="formulario validar">
    <?php if (isset($id)): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>
    <div class="campo"><label for="nombre_completo">Nombre completo</label><input id="nombre_completo" name="nombre_completo" required value="<?= escapar($datos['nombre_completo'] ?? '') ?>"></div>
    <div class="campo"><label for="cedula">Cédula</label><input id="cedula" name="cedula" data-tipo="cedula" required value="<?= escapar($datos['cedula'] ?? '') ?>"></div>
    <div class="campo"><label for="estado_civil">Estado civil</label><select id="estado_civil" name="estado_civil" required><?php foreach (['Soltero','Soltera','Casado','Casada','Unido','Unida'] as $op): ?><option <?= ($datos['estado_civil'] ?? '') === $op ? 'selected' : '' ?>><?= $op ?></option><?php endforeach; ?></select></div>
    <div class="campo"><label for="cargo">Cargo</label><input id="cargo" name="cargo" required value="<?= escapar($datos['cargo'] ?? '') ?>"></div>
    <div class="campo"><label for="salario_base">Salario mensual</label><input type="number" min="0.01" step="0.01" id="salario_base" name="salario_base" required value="<?= escapar($datos['salario_base'] ?? '') ?>"></div>
    <div class="campo"><label for="anio_inicio">Año de inicio</label><input type="number" min="1900" max="<?= date('Y') ?>" id="anio_inicio" name="anio_inicio" required value="<?= escapar($datos['anio_inicio'] ?? date('Y')) ?>"></div>
    <div class="campo"><label for="tipo_declaracion">Tipo de declaración</label><select id="tipo_declaracion" name="tipo_declaracion"><option value="Individual" <?= ($datos['tipo_declaracion'] ?? '') !== 'Conjunta' ? 'selected' : '' ?>>Individual</option><option value="Conjunta" <?= ($datos['tipo_declaracion'] ?? '') === 'Conjunta' ? 'selected' : '' ?>>Conjunta</option></select></div>
    <button class="boton boton-primario" type="submit">Guardar</button>
    <a class="boton boton-secundario" href="index.php">Cancelar</a>
</form>
