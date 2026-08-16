<?php

$id = $_GET['id'] ?? null;

$url = "relations?rel=books,tables&type=book,table&linkTo=num_book&equalTo=" . $_GET["id"];
$method = "GET";
$fields = array();

$getBook = CurlController::request($url, $method, $fields);

if ($getBook->status == 200) {

    $num_book = $getBook->results[0]->num_book;
    $date_book = $getBook->results[0]->date_book;
    $time_book = $getBook->results[0]->time_book;
    $client_book = $getBook->results[0]->client_book;
    $email_book = $getBook->results[0]->email_book;
    $phone_book = $getBook->results[0]->phone_book;
    $servicios_book = $getBook->results[0]->servicios_book;
    $description_book = $getBook->results[0]->description_book;
    $confirm_book = $getBook->results[0]->confirm_book;
    $title_table = $getBook->results[0]->title_table;
    $id_table_book = $getBook->results[0]->id_table_book;
    $description_table = $getBook->results[0]->description_table;
    $image_table = $getBook->results[0]->image_table;


    $jsonServicios = json_decode($servicios_book, true);

    //echo "JSON Servicios: " . print_r($getBook, true) . "<br>";

    if ($jsonServicios) {
        $nombreServicio = [];
        $total = count($jsonServicios);

        for ($i = 0; $i < $total; $i++) {
            $nombre_servicio = $jsonServicios[$i]['nombre'];
            $nombreServicio[] = urldecode($nombre_servicio);
        }
        $resultado_final = implode(', ', $nombreServicio);
    } else {
        $resultado_final = "No hay servicios seleccionados";
    }

    $url_table = "tables?linkTo=id_table&equalTo=" . $getBook->results[0]->id_table_book;
    $method = "GET";
    $fields = array();

    $getTable = CurlController::request($url_table, $method, $fields);

    if ($getTable->status == 200) {
        $entrada_table = $getTable->results[0]->entrada_table;
        $salida_table = $getTable->results[0]->salida_table;
    }


    $url_books = "books?linkTo=id_table_book&equalTo=" . $getBook->results[0]->id_table_book;
    $method = "GET";
    $fields = array();

    $getBooks = CurlController::request($url_books, $method, $fields);
    //echo "JSON Books: " . print_r($getBooks, true) . "<br>";
}

// Validar si la cita es en menos de 24 horas
$fechaHoraCita = strtotime($date_book . ' ' . $time_book);
$ahora = time();
$diferenciaHoras = ($fechaHoraCita - $ahora) / 3600;

$puedeReprogramar = round($diferenciaHoras) >= 24 && round($diferenciaHoras) > 0;


?>


<main class="flex-grow flex flex-col items-center justify-center px-margin-mobile py-stack-lg md:py-16">
    <div class="max-w-3xl w-full flex flex-col gap-stack-lg">
        <!-- Header Section -->
        <div class="text-center space-y-stack-sm">
            <div
                class="inline-flex items-center gap-2 px-3 py-1 bg-tertiary-fixed text-on-tertiary-fixed rounded-full shadow-sm animate-pulse">
            </div>
            <h1
                class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface max-w-xl mx-auto">
                Por favor, confirma tu asistencia a la siguiente cita
            </h1>
        </div>
        <!-- Bento Card Layout -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter">
            <!-- Main Info Card -->
            <div
                class="md:col-span-12 lg:col-span-12 bg-surface-container-lowest rounded-xl p-6 md:p-8 shadow-[0_4px_12px_0_rgba(0,0,0,0.05)] border border-outline-variant/30 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full -mr-16 -mt-16"></div>
                <div class="flex flex-col md:flex-row gap-6 items-start relative z-10">
                    <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-primary-container shrink-0">
                        <img alt="<?php echo urldecode($title_table); ?>" class="w-full h-full object-cover"
                            src="<?php echo urldecode($image_table); ?>" />
                    </div>
                    <div class="space-y-stack-sm flex-grow">
                        <div>
                            <h2 class="font-headline-md text-headline-md text-on-surface">Esp:
                                <?php echo urldecode($title_table); ?>
                            </h2>
                            <p class="text-primary font-medium"><?php echo urldecode($resultado_final); ?></p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-outline-variant/50">
                            <div class="flex items-start gap-3">
                                <div class="bg-secondary-container p-2 rounded-lg text-on-secondary-container">
                                    <span class="material-symbols-outlined"
                                        data-icon="calendar_today">calendar_today</span>
                                </div>
                                <div>
                                    <p class="text-label-sm font-label-sm text-on-surface-variant uppercase">Fecha</p>
                                    <p class="text-body-md font-body-md"><?php echo urldecode($date_book); ?></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="bg-secondary-container p-2 rounded-lg text-on-secondary-container">
                                    <span class="material-symbols-outlined" data-icon="schedule">schedule</span>
                                </div>
                                <div>
                                    <p class="text-label-sm font-label-sm text-on-surface-variant uppercase">Hora</p>
                                    <p class="text-body-md font-body-md"><?php echo urldecode($time_book); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php if ($puedeReprogramar): ?>
            <div class="mt-4">
                <p class="text-sm text-amber-600 font-medium mb-3">
                    ⚠️ Podrás reagendar tu cita solo una vez antes de 24 horas de la cita programada.
                </p>
                <button onclick="document.getElementById('formReprogramar').classList.toggle('hidden')"
                    class="w-full py-2 px-4 rounded-lg border border-primary text-primary font-medium hover:bg-primary/10 transition">
                    📅 Reprogramar cita
                </button>

                <form id="formReprogramar" method="POST" class="hidden mt-4 text-left">
                    <input type="hidden" name="id" value="<?= $num_book ?>">
                    <input type="hidden" name="id_table_book" value="<?= $getBook->results[0]->id_table_book ?>">

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Fecha</label>
                        <input type="date" name="nueva_fecha" id="reprNuevaFecha" required min="<?= date('Y-m-d') ?>"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Hora</label>
                        <select name="nueva_hora" id="reprNuevaHora" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Primero selecciona una fecha</option>
                        </select>
                    </div>

                    <?php
                        if(isset($_POST["id"]) && isset($_POST["nueva_fecha"]) && isset($_POST["nueva_hora"])) {
                            require_once "controllers/books.controller.php";
                            $books = new BooksController();
                            $books->reprogramBook_Usuario();
                        }
                    ?>

                    <button type="submit"
                        class="w-full py-2 px-4 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition">
                        Confirmar Reprogramación
                    </button>
                    <script>
                        // Horas del especialista (entrada a salida en intervalos de 1 hora)
                        const horasEspecialista = [];
                        <?php
                            $entradaMin = strtotime($entrada_table);
                            $salidaMin = strtotime($salida_table);
                            for ($t = $entradaMin; $t < $salidaMin; $t += 3600) {
                                echo "horasEspecialista.push('" . date('H:i', $t) . "');";
                            }
                            ?>

                            // Todas las reservas del especialista (no canceladas)
                            const reservasEspecialista = {};
                            <?php
                            foreach ($getBooks->results as $r) {
                                if ($r->confirm_book == 2)
                                    continue; // excluir canceladas
                                $fecha = $r->date_book;
                                $hora = substr($r->time_book, 0, 5); // "15:00:00" → "15:00"
                                echo "if (!reservasEspecialista['$fecha']) reservasEspecialista['$fecha'] = [];";
                                echo "reservasEspecialista['$fecha'].push('$hora');";
                            }
                        ?>
                    </script>

                </form>
            </div>
        <?php else: ?>
            <p class="text-sm text-gray-400 mt-4">
                La reprogramación solo está disponible antes de las 24 horas previas a la cita.
            </p>
        <?php endif; ?>
        <!-- Action Buttons -->
        <form id="reservationForm" method="POST">
            <div class="flex flex-col md:flex-row gap-4 pt-stack-sm">
                <input type="hidden" name="id" value="<?php echo $num_book; ?>">

                <button type="submit"
                    class="flex-grow md:flex-[2] bg-primary text-on-primary py-4 rounded-xl font-label-md text-label-md hover:bg-primary-container transition-all active:scale-[0.98] shadow-lg shadow-primary/20 flex items-center justify-center gap-2"
                    <?php if ($confirm_book == '1') { ?> disabled <?php } ?>>
                    <span class="material-symbols-outlined" data-icon="check_circle">check_circle</span>
                    <?php if ($confirm_book == '1' || $confirm_book == '2') { ?>
                        Cita Confirmada o Cancelada
                    <?php } else { ?>
                        Confirmar Cita
                    <?php } ?>
                </button>

                <button onclick="window.location.href='/confirmacion/cancel?id=<?php echo $num_book; ?>';" type="button"
                    class="flex-grow md:flex-1 border-2 border-outline text-on-surface py-4 rounded-xl font-label-md text-label-md hover:bg-surface-container-high transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined" data-icon="close">close</span>
                    Cancelar Cita
                </button>
            </div>
            <?php
            require_once "controllers/books.controller.php";
            $books = new BooksController();
            $books->getBookById();
            ?>
            <!-- Additional Help/Info -->
            <div
                class="bg-surface-container-low/50 rounded-lg p-4 flex items-center gap-4 border border-outline-variant/20">
                <span class="material-symbols-outlined text-on-surface-variant" data-icon="info">info</span>
                <p class="text-label-sm text-on-surface-variant">
                    ¿Necesitas cambiar la fecha? Utiliza la opción de "Cancelar" y vuelve a agendar desde tu historial.
                </p>
            </div>
        </form>
    </div>
</main>

<script>
        // Micro-interaction for buttons
        document.querySelectorAll('button').forEach(button => {
            button.addEventListener('mousedown', () => {
                button.classList.add('opacity-80');
            });
            button.addEventListener('mouseup', () => {
                button.classList.remove('opacity-80');
            });
            button.addEventListener('mouseleave', () => {
                button.classList.remove('opacity-80');
            });
        });

    // Simple feedback for "Confirmar" action
    const confirmBtn = document.querySelector('button.bg-primary');
    confirmBtn.addEventListener('click', () => {
        confirmBtn.innerHTML = `
                <span class="material-symbols-outlined animate-spin" data-icon="sync">sync</span>
                Confirmando...
            `;
        confirmBtn.classList.add('pointer-events-none');

        setTimeout(() => {
            confirmBtn.innerHTML = `
                    <span class="material-symbols-outlined" data-icon="done_all">done_all</span>
                    ¡Cita Confirmada!
                `;
            confirmBtn.classList.remove('bg-primary');
            confirmBtn.classList.add('bg-green-600'); // Simple temporary state
        }, 1500);
    });
</script>

<script>
    document.getElementById('reprNuevaFecha').addEventListener('change', function () {
    const fechaSeleccionada = this.value; // "YYYY-MM-DD"
    const select = document.getElementById('reprNuevaHora');
    select.innerHTML = '<option value="">Seleccionar hora</option>';

    if (!fechaSeleccionada) return;

    const ahora        = new Date();
    const fechaHoy     = ahora.toISOString().split('T')[0];
    const minutosAhora = ahora.getHours() * 60 + ahora.getMinutes();

    const ocupadas = reservasEspecialista[fechaSeleccionada] || [];
    console.log("Reservas especi:", reservasEspecialista);
    console.log("Ocupadas:", ocupadas);

    horasEspecialista.forEach(hora => {
        const [h, m]     = hora.split(':').map(Number);
        const minutosOpt = h * 60 + m;

        const estaOcupada = ocupadas.includes(hora);
        const estaPasada  = fechaSeleccionada === fechaHoy && minutosOpt <= minutosAhora;

        const opt       = document.createElement('option');
        opt.value       = hora;

        // Formato 12h para mostrar
        const fecha12   = new Date(2000, 0, 1, h, m);
        opt.textContent = fecha12.toLocaleTimeString('es-CO', { 
            hour: 'numeric', minute: '2-digit', hour12: true 
        });

        if (estaOcupada) {
            opt.disabled     = true;
            opt.textContent += ' (ocupado)';
        } else if (estaPasada) {
            opt.disabled     = true;
            opt.textContent += ' (no disponible)';
        }

        select.appendChild(opt);
    });
});
</script>

</body>

</html>