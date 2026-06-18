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
}

$url_cancel = "motivos";
$method_cancel = "GET";
$fields_cancel = array();

$getBook_cancel = CurlController::request($url_cancel, $method_cancel, $fields_cancel);

if ($getBook_cancel->status == 200) {

    $motivo = [];

    foreach ($getBook_cancel->results as $key => $value) {


        $motivo = [
            'id' => $value->id_motivo,
            'motivo' => urldecode($value->title_motivo)
        ];

        $motivo_cancel[] = $motivo;
    }
}

?>


<!-- Main Content: Focused Cancellation Dialog -->
<main class="flex-grow flex items-center justify-center pt-20 pb-16 px-margin-mobile">
    <div
        class="w-full max-w-[560px] bg-surface-container-lowest rounded-xl shadow-[0_4px_12px_0_rgba(0,0,0,0.05)] border border-outline-variant overflow-hidden animate-in fade-in zoom-in duration-300">
        <!-- Warning Header -->
        <div class="bg-error-container/20 p-stack-lg flex items-center gap-stack-md border-b border-error-container/30">
            <div class="bg-error-container text-error rounded-full p-2 flex items-center justify-center">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">warning</span>
            </div>
            <h2 class="font-headline-md text-headline-md text-on-surface">Confirmar Cancelación</h2>
        </div>
        <div class="p-stack-lg space-y-stack-lg">
            <!-- Question Section -->
            <div class="space-y-stack-sm">
                <p class="font-body-lg text-body-lg text-on-surface-variant">
                    ¿Estás seguro de que deseas cancelar tu cita para el <span
                        class="font-bold text-on-surface"><?php echo $date_book; ?> a las
                        <?php echo $time_book; ?></span>?
                </p>
                <p class="font-label-md text-label-md text-outline">
                    Esta acción no se puede deshacer una vez confirmada.
                </p>
            </div>
            <!-- Input Section -->
            <form id="reservationForm" method="POST">
                <div class="space-y-stack-sm">
                    <input type="hidden" name="id" value="<?php echo $num_book; ?>">
                    <label class="font-label-md text-label-md text-on-surface block" for="cancellation_reason">
                        Motivo de la cancelación (Opcional)
                    </label>
                    <select
                        class="w-full h-12 px-4 rounded-xl border border-outline-variant bg-surface focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-body-md text-body-md outline-none appearance-none"
                        id="cancellation_reason"
                        name="id_cancel">
                        <option value="" disabled selected>Por favor, selecciona un motivo de cancelación...</option>
                        <?php foreach ($motivo_cancel as $key => $value): ?>
                            <option value="<?php echo $value['id']; ?>"><?php echo $value['motivo']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Action Section -->
                <div class="flex flex-col md:flex-row-reverse gap-stack-md pt-stack-sm">
                    <button
                        type="submit"
                        class="w-full md:flex-1 bg-[#EF4444] text-white py-3 px-stack-md rounded-lg font-label-md text-label-md hover:bg-[#DC2626] active:scale-95 transition-all shadow-sm">
                        Confirmar Cancelación
                    </button>
                    <button
                        type="button"
                        class="w-full md:flex-1 border border-outline-variant text-secondary py-3 px-stack-md rounded-lg font-label-md text-label-md hover:bg-surface-container-low active:scale-95 transition-all">
                        Volver atrás
                    </button>
                </div>
                <?php
                require_once "controllers/books.controller.php";
                $books = new BooksController();
                $books->cancelBook();
                ?>
            </form>
        </div>
        <!-- Footer Decorative Element -->
        <div class="h-1 bg-gradient-to-r from-error/50 via-error to-error/50"></div>
    </div>
</main>


</body>

</html>