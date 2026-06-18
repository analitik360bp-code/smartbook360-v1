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
    $description_table = $getBook->results[0]->description_table;
    $image_table = $getBook->results[0]->image_table;
    

    $jsonServicios = json_decode($servicios_book, true);

    if ($jsonServicios) {
        $nombreServicio = [];
        $total = count($jsonServicios);

        for ($i = 0; $i < $total; $i++) {
            $nombre_servicio = $jsonServicios[$i]['nombre'];
            $nombreServicio[] = $nombre_servicio;
        }
        $resultado_final = implode(', ', $nombreServicio);
    } else {
        $resultado_final = "No hay servicios seleccionados";
    }
}



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
                        <img alt="<?php echo $title_table; ?>" class="w-full h-full object-cover"
                            src="<?php echo $image_table; ?>" />
                    </div>
                    <div class="space-y-stack-sm flex-grow">
                        <div>
                            <h2 class="font-headline-md text-headline-md text-on-surface">Esp:
                                <?php echo $title_table; ?>
                            </h2>
                            <p class="text-primary font-medium"><?php echo $resultado_final; ?></p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-outline-variant/50">
                            <div class="flex items-start gap-3">
                                <div class="bg-secondary-container p-2 rounded-lg text-on-secondary-container">
                                    <span class="material-symbols-outlined"
                                        data-icon="calendar_today">calendar_today</span>
                                </div>
                                <div>
                                    <p class="text-label-sm font-label-sm text-on-surface-variant uppercase">Fecha</p>
                                    <p class="text-body-md font-body-md"><?php echo $date_book; ?></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="bg-secondary-container p-2 rounded-lg text-on-secondary-container">
                                    <span class="material-symbols-outlined" data-icon="schedule">schedule</span>
                                </div>
                                <div>
                                    <p class="text-label-sm font-label-sm text-on-surface-variant uppercase">Hora</p>
                                    <p class="text-body-md font-body-md"><?php echo $time_book; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <!-- Action Buttons -->
        <form id="reservationForm" method="POST">
            <div class="flex flex-col md:flex-row gap-4 pt-stack-sm">
                <input type="hidden" name="id" value="<?php echo $num_book; ?>">

                <button type="submit"
                    class="flex-grow md:flex-[2] bg-primary text-on-primary py-4 rounded-xl font-label-md text-label-md hover:bg-primary-container transition-all active:scale-[0.98] shadow-lg shadow-primary/20 flex items-center justify-center gap-2" 
                    <?php if ($confirm_book == '1') { ?>
                        disabled
                    <?php } ?>
                >
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
<!-- BottomNavBar (Mobile Only) -->
<nav
    class="md:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center h-16 bg-surface-container dark:bg-surface-dim px-4 pb-safe shadow-[0_-4px_12px_0_rgba(0,0,0,0.05)]">
    <button
        class="flex flex-col items-center justify-center bg-secondary-container dark:bg-secondary-fixed text-on-secondary-container dark:text-on-secondary-fixed rounded-full px-4 py-1 transition-transform active:scale-95 duration-150">
        <span class="material-symbols-outlined" data-icon="calendar_today">calendar_today</span>
        <span class="font-label-sm text-label-sm">Appointments</span>
    </button>
    <button
        class="flex flex-col items-center justify-center text-on-surface-variant dark:text-on-secondary-fixed-variant hover:bg-surface-variant transition-transform active:scale-95 duration-150">
        <span class="material-symbols-outlined" data-icon="history">history</span>
        <span class="font-label-sm text-label-sm">History</span>
    </button>
    <button
        class="flex flex-col items-center justify-center text-on-surface-variant dark:text-on-secondary-fixed-variant hover:bg-surface-variant transition-transform active:scale-95 duration-150">
        <span class="material-symbols-outlined" data-icon="settings">settings</span>
        <span class="font-label-sm text-label-sm">Settings</span>
    </button>
</nav>
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
</body>

</html>