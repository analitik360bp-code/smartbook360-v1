<!-- date_filter.php -->
<?php
$dateFrom = $_GET['date_from'] ?? date('Y-m-01'); // Primer día del mes actual
$dateTo   = $_GET['date_to']   ?? date('Y-m-d');  // Hoy

?>
<?php if ($modules[0]->title_page == 'DashBoard'): ?>
<div class="col-12 mb-4">
    <div class="card rounded shadow-sm">
        <div class="card-body py-3">
            <form method="GET" action="" class="d-flex align-items-end gap-3 flex-wrap">

                <!-- Preservar otros parámetros GET que ya existan (ej: idPage) -->
                <?php foreach ($_GET as $key => $val): ?>
                    <?php if (!in_array($key, ['date_from', 'date_to'])): ?>
                        <input type="hidden" name="<?php echo htmlspecialchars($key) ?>" 
                               value="<?php echo htmlspecialchars($val) ?>">
                    <?php endif ?>
                <?php endforeach ?>

                <div>
                    <label class="form-label small text-muted mb-1">Desde</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="<?php echo $dateFrom ?>">
                </div>

                <div>
                    <label class="form-label small text-muted mb-1">Hasta</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="<?php echo $dateTo ?>">
                </div>

                <div>
                    <button type="submit" class="btn btn-sm text-white"
                            style="background-color: rgb(32,178,170);">
                        <i class="bi bi-funnel-fill me-1"></i> Filtrar
                    </button>
                </div>

                <?php if (isset($_GET['date_from'])): ?>
                <div>
                    <a href="?" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Limpiar
                    </a>
                </div>
                <?php endif ?>

            </form>
        </div>
    </div>
</div>
<?php endif ?>