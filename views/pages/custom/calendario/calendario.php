<?php 

if($_SESSION["admin"]->id_office_admin > 0){

     /*=============================================
    Traer mesas
    =============================================*/
    $url = "tables?linkTo=id_office_table,activo_table&equalTo=".$_SESSION["admin"]->id_office_admin.",1";
    $method = "GET";
    $fields = array();

    $getTables = CurlController::request($url,$method,$fields);

    if($getTables->status == 200){

        $tables = $getTables->results;
        $tablesDatabase = [];
       //echo "<pre>tables: "; print_r($tables); echo "</pre>";
        foreach ($tables as $keys => $val) {
                // Generar array de horas entre entrada y salida
                $horaEntrada = (int) substr($val->entrada_table, 0, 2);
                $horaSalida  = (int) substr($val->salida_table, 0, 2);
                $horas = [];

                for ($h = $horaEntrada; $h <= $horaSalida; $h++) {
                    $horas[] = str_pad($h, 2, "0", STR_PAD_LEFT) . ":00";
                }
            $tablesbase = [
                'id' => $val->id_table,
                'estado' => $val->status_table,
                'especialista' => $val->title_table,
                'descripcion' => $val->description_table,
                'imagen' => $val->image_table,
                'entrada' => $val->entrada_table,
                'salida' => $val->salida_table,
                'servicios' => $val->servicio_table,
                'horas' => $horas
            ];

            $tablesDatabase[$val->id_table][] = $tablesbase;
           
        }

    }else{

       $tables = array(); 
       $tablesDatabase = null;
    }

    /*=============================================
    Traer reservas
    =============================================*/

    $url = "relations?rel=books,tables&type=book,table&linkTo=id_office_book&equalTo=".$_SESSION["admin"]->id_office_admin;

    $getBooks = CurlController::request($url,$method,$fields);

    if($getBooks->status == 200){

        $books = $getBooks->results;
        //echo "<pre>books: "; print_r($books); echo "</pre>";
        /*=============================================
        Organizar la data de las reservas por fechas en un JSON
        =============================================*/
   
        $reservationsDatabase = [];
       
        foreach ($books as $key => $value) {
            $dateBook = $value->date_book;

            $reservation = [
                'id' => $value->id_book,
                'customerClient' => $value->client_book,
                'time' => TemplateController::formatDate(6, $value->time_book),
                'table' => urldecode($value->title_table),
                'phone' => $value->phone_book,
                'confirmado'  => $value->confirm_book,
                'num_book' => $value->num_book,
                'servicios' => $value->servicios_book

            ];

            $reservationsDatabase[$dateBook][] = $reservation;
        }
        //echo "<pre>print_r($reservationsDatabase);</pre>";

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

    }else{

        $books = array();
        $reservationsDatabase = null;
    }

}else{

  echo '<script>
    setTimeout(()=>{

        $("#myOffices").modal("show");

    },100);
  </script>';
}


 ?>

<?php if (!empty($tables)): ?>

    <?php if (!empty($books)): ?>

        <input type="hidden" id="reservationsDatabase" value='<?php echo json_encode($reservationsDatabase)  ?>'>
        
    <?php endif ?>

    <input type="hidden" id="tablesDatabase" value='<?php echo json_encode($tablesDatabase)  ?>'>

    <link rel="stylesheet" href="/views/assets/css/calendar/calendar.css"> 

    <div class="container-fluid py-3 p-lg-4">
        
        <div class="row">
            
            <!--==============================
              Breadcrumb
             ================================-->
            <div class="col-12 mb-3 position-relative">
                <div class="d-lg-flex justify-content-lg-between mt-2">
                    <div class="text-capitalize h5 ps-2">Gestión de Reservas</div>
                    <div class="pe-0">
                        <ul class="nav justify-content-lg-end">
                            <li class="nav-item">
                                <a class="nav-link py-0 px-0 text-dark" href="/">Inicio</a>
                            </li>
                            <li class="nav-item ps-3">/</li>
                            <li class="nav-item">
                                <a class="nav-link py-0 disabled text-capitalize" href="#">Reservas</a>
                            </li> 
                        </ul>
                    </div>
                </div>
            </div>

            <!--==============================
              Panel de Reservas
             ================================-->
            
            <!-- Calendario y Formulario -->
            <div class="col-12 col-lg-8 mb-3 rounded">
                <div class="card rounded reservation-card">
                    <div class="card-header reservation-header rounded-top">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Nueva Reserva</h5>
                    </div>
                    <div class="card-body">
                        
                        <!-- Calendario -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="reservation-subtitle">Seleccionar Fecha</h6>
                                <div id="reservationCalendar" class="reservation-calendar"></div>
                            </div>
                        </div>

                        <!-- Formulario de Reserva -->
                        <form id="reservationForm" method="POST">

                            <input type="hidden" id="date_book" name="date_book" >
                            <div class="row">
                                
                                <!-- Información del Cliente -->
                                <div class="col-12 col-md-6 mb-3">
                                    <h6 class="reservation-subtitle">Información del Cliente</h6>
                                    
                                    <div class="mb-3">
                                        <label for="customerName" class="form-label">Nombre Completo *</label>
                                        <input type="text" class="form-control reservation-input" id="client_book" name="client_book" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="customerPhone" class="form-label">Teléfono *</label>
                                        <input type="tel" class="form-control reservation-input" id="phone_book" name="phone_book" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="customerEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control reservation-input" id="email_book" name="email_book">
                                    </div>
                                    
                                </div>

                                <!-- Detalles de la Reserva -->
                                <div class="col-12 col-md-6 mb-3">
                                    <h6 class="reservation-subtitle">Detalles de la Reserva</h6>
                                    

                                    <div class="mb-3">
                                        <label for="tableId" class="form-label">Selección de Especialista *</label>
                                        <select class="form-select reservation-input" id="id_table_book" name="id_table_book"  required>
                                            <option value="">Seleccionar</option>

                                            <?php foreach ($tables as $key => $value): ?>

                                                <option value="<?php echo $value->id_table ?>"><?php echo urldecode($value->title_table) ?>
                                                                                                    
                                            <?php endforeach ?>

                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="reservationTime" class="form-label">Hora *</label>
                                        <select class="form-select reservation-input" id="time_book" name="time_book" required>
                                            <option value="">Seleccionar hora</option>
                                                                                       
                                        </select>
                                    </div>

                                    <div id="contenedor-tarjeta"></div>
                                    
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Servicios *</label>
                                        <div id="cotizador-servicios">
                                            <p class="text-muted small">Selecciona un especialista para ver los servicios.</p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                            <span class="fw-bold">Total:</span>
                                            <span class="fw-bold fs-5" id="cotizador-total">$0</span>
                                        </div>
                                    </div>
                                    <input type="hidden" id="servicios_book" name="servicios_book">
                                </div>

                                <!-- Comentarios Especiales -->
                                <div class="col-12 mb-3">
                                    <label for="specialRequests" class="form-label">Comentarios Especiales</label>
                                    <textarea class="form-control reservation-input" id="description_book" name="description_book" rows="3" placeholder="Alergias, celebraciones, preferencias especiales..."></textarea>
                                </div>

                                <!-- Botones de Acción -->
                                <div class="col-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button type="button" class="btn btn-secondary reservation-btn-secondary rounded" id="clearForm">
                                            <i class="fas fa-times me-1"></i>Limpiar
                                        </button>
                                        <button type="submit" class="btn backColor reservation-btn-primary rounded" id="saveReservation">
                                            <i class="fas fa-save me-1"></i>Guardar Reserva
                                        </button>
                                    </div>
                                </div>

                                <?php 

                                require_once "controllers/books.controller.php";
                                $books = new BooksController();
                                $books->manageBooks();
                                ?>

                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <!-- Panel de Reservas del Día -->
            <div class="col-12 col-lg-4 mb-3">
                <div class="card rounded reservation-card">
                    <div class="card-header reservation-header rounded-top d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Reservas de Hoy</h5>
                    </div>

                    <div class="card-header reservation-header rounded-top d-flex justify-content-between align-items-center">
                        <select id="filterTable" class="form-select form-select-sm w-auto">
                            <option value="">Todas las especialistas</option>
                            <?php foreach ($tables as $key => $value): ?>
                                <option value="<?php echo urldecode($value->title_table) ?>"><?php echo urldecode($value->title_table) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="card-body">
                        <div id="todayReservations" class="today-reservations">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-calendar-check fa-2x mb-2"></i>
                                <p>No hay reservas para hoy</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Modal de Cotización -->
        <div class="modal fade" id="modalCotizacion" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-receipt me-2"></i>Cotización
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Servicio</th>
                                    <th class="text-end">Precio</th>
                                </tr>
                            </thead>
                            <tbody id="cotizacion-tbody">
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td class="fw-bold">Total</td>
                                    <td class="text-end fw-bold fs-5" id="cotizacion-total">$0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- Modal de Confirmar -->
        <div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-labelledby="modalCambiarEstadoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCambiarEstadoLabel">Gestionar Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <form id="formActualizarEstado" method="POST">
                        <div class="modal-body">
                            <input type="hidden" id="modalReservationId" name="id">
                            
                            <div class="mb-3">
                                <label for="selectEstado" class="form-label">Selecciona el nuevo estado:</label>
                                <select class="form-select" id="selectEstado" name="confirmado" required>
                                    <option value="" disabled selected>-- Seleccionar acción --</option>
                                    <option value="1">Confirmar</option>
                                    <option value="2">Cancelar</option>
                                </select>
                            </div>

                            <div class="mb-3 d-none" id="contenedorMotivo">
                                <label for="selectMotivo" class="form-label">Motivo de la cancelación:</label>
                                <select class="form-select" id="selectMotivo" name="motivo_cancelacion">
                                    <option value="" disabled selected>-- Seleccionar motivo --</option>
                                    <?php foreach ($motivo_cancel as $key => $value): ?>
                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['motivo']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            <?php 
                                require_once "controllers/books.controller.php";
                                $books = new BooksController();
                                $books->confirmBook_Admin();
                            ?>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    <script src="/views/assets/js/calendar/calendar.js"></script>

<?php else: include "views/pages/welcome/welcome.php" ?>

<?php endif ?>  
