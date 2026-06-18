$(document).ready(function () {

    /*=============================================
    Variables globales
    =============================================*/

    let selectedDate = null;
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let reservationsDatabase = {};
    let filterTable = "";

    /*=============================================
    Capturar reservas existentes
    =============================================*/

    if ($("#reservationsDatabase").length > 0) {

        reservationsDatabase = JSON.parse($("#reservationsDatabase").val());
        //console.log("reservationsDatabase", reservationsDatabase);
    }

    // Escuchar cambios en el filtro de mesas
    $(document).on('change', '#filterTable', function () {
        filterTable = $(this).val();
        loadSelectedDateReservations();
    });

    /*=============================================
    Función que  genera el calendario
    =============================================*/

    function generateCalendar() {

        /*=============================================
        Desplazamiento entre meses
        =============================================*/

        const calendarContainer = $('#reservationCalendar');

        const monthNames = [
            "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
        ];

        // Limpiar calendario
        calendarContainer.empty();

        // Header del calendario
        const header = $(`
            <div class="calendar-header">
                <button type="button" class="calendar-nav-btn" id="prevMonth">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h6 class="mb-0">${monthNames[currentMonth]} ${currentYear}</h6>
                <button type="button" class="calendar-nav-btn" id="nextMonth">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        `);

        calendarContainer.append(header);

        /*=============================================
        Desplazamiento entre días
        =============================================*/

        const dayNames = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"];

        // Días de la semana
        const weekHeader = $('<div class="calendar-grid mb-2"></div>');

        dayNames.forEach(day => {

            weekHeader.append(`<div class="text-center fw-bold py-2">${day}</div>`)
        })

        calendarContainer.append(weekHeader);

        // Grid de días
        const daysGrid = $('<div class="calendar-grid" id="calendarDays"></div>');

        calendarContainer.append(daysGrid);

        // Generar días del mes
        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const today = new Date();

        // Días vacíos del mes anterior
        for (let i = 0; i < firstDay; i++) {
            daysGrid.append('<div class="calendar-day disabled"></div>');
        }

        // Días del mes actual
        for (let day = 1; day <= daysInMonth; day++) {

            const date = new Date(currentYear, currentMonth, day);
            const isToday = date.toDateString() === today.toDateString();
            const isPast = date < today && !isToday;
            const dateKey = date.toISOString().split('T')[0];
            const dayReservations = reservationsDatabase[dateKey] || [];
            const hasReservations = dayReservations.length > 0;

            let classes = 'calendar-day';
            if (isToday) classes += ' today';
            if (isPast) classes += ' disabled';
            if (hasReservations) classes += ' has-reservations';

            let dayContent = `${day}`;

            if (hasReservations && dayReservations.length > 1) {

                dayContent += `<span class="reservation-count">${dayReservations.length}</span>`;
            }

            const dayElement = $(`
                <div class="${classes}" data-date="${date.toISOString()}">
                    ${dayContent}
                </div>
            `);

            daysGrid.append(dayElement);
        }

    }

    /*=============================================
    Función para los eventos
    =============================================*/

    function setupEventListeners() {

        // Retroceder Meses
        $(document).on('click', '#prevMonth', function () {

            currentMonth--;

            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }

            generateCalendar();

        })

        // Avanzar meses
        $(document).on('click', '#nextMonth', function () {

            currentMonth++;

            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }

            generateCalendar();

        })

        // Selección de fecha
        $(document).on('click', '.calendar-day:not(.disabled)', function () {
            const dateStr = $(this).data('date');
            if (dateStr) {
                const date = new Date(dateStr);
                selectDate(date);
            }
        });

        // Limpiar formulario
        $('#clearForm').on('click', function () {
            clearReservationForm();
        });

    }

    /*=============================================
    Función para seleccionar dia
    =============================================*/
    function selectDate(date) {

        selectedDate = date;

        // Actualizar visual del calendario
        $('.calendar-day').removeClass('selected');
        $(`.calendar-day[data-date="${date.toISOString()}"]`).addClass('selected');

        $("#date_book").val(date.toLocaleDateString());

        loadSelectedDateReservations();

    }

    /*=============================================
   Función Para mostrar las reservas del día
   =============================================*/

    function loadSelectedDateReservations() {

        const container = $('#todayReservations');
        const headerTitle = container.closest('.card').find('.card-header h5');

        if (!selectedDate) return;

        const dateKey = selectedDate.toISOString().split('T')[0];
        const dayReservations = reservationsDatabase[dateKey] || [];
        const isToday = selectedDate.toDateString() === new Date().toDateString();

        // Actualizar título del panel
        const dateStr = selectedDate.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })

        headerTitle.html(`<i class="fas fa-list me-2"></i>Reservas - ${dateStr}`);

        if (dayReservations.length === 0) {
            container.html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-calendar-check fa-2x mb-2"></i>
                    <p>No hay reservas para este día</p>
                </div>
            `);
            return;

        }

        container.empty();

        // Ordenar reservas por hora
        let sortedReservations = dayReservations.sort((a, b) => {
            return a.time.localeCompare(b.time);
        });

        // Aplicar filtro de mesa si existe
        if (filterTable !== "") {
            sortedReservations = sortedReservations.filter(reservation => reservation.table === filterTable);
        }

        if (sortedReservations.length === 0) {
            container.html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-filter fa-2x mb-2"></i>
                    <p>No hay reservas para esta especialista en este día</p>
                </div>
            `);
            return;
        }

        sortedReservations.forEach(reservation => {

            const item = $(`
                <div class="reservation-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${reservation.customerClient}</h6>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>${reservation.time}
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-table me-1"></i>${reservation.table}
                            </small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">${reservation.phone}</small>
                            <br>
                            ${reservation.confirmado == 1
                                ? '<span class="badge bg-success">Confirmada</span>'
                                : reservation.confirmado == 2
                                    ? '<span class="badge bg-danger">Cancelada</span>'
                                    : `
                            <button type="button" 
                                    class="btn btn-sm p-0 border-0 alignment-baseline" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalCambiarEstado" 
                                    data-id="${reservation.num_book}">
                                <span class="badge bg-warning text-dark style="cursor: pointer;">
                                    Pendiente 🔄
                                </span>
                            </button>`
                            }
                            <br>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-1 btn-ver-cotizacion"
                                    data-servicios='${reservation.servicios}'>
                                <i class="fas fa-receipt me-1"></i>Ver cotización
                            </button>
                        </div>
                    </div>
                </div>
            `);

            container.append(item);
        });
    }


    // 1. Capturamos el elemento del modal
const modalCambiarEstado = document.getElementById('modalCambiarEstado');

// 2. Escuchamos el evento que se dispara justo antes de que el modal se muestre en pantalla
modalCambiarEstado.addEventListener('show.bs.modal', function (event) {
    // 'event.relatedTarget' es el botón exacto que el usuario presionó
    const botonQueDisparoElModal = event.relatedTarget;
    
    // Extraemos el valor del atributo 'data-id' (que contiene el num_book)
    const reservationId = botonQueDisparoElModal.getAttribute('data-id');
    
    // Buscamos el input hidden dentro del modal
    const inputHidden = modalCambiarEstado.querySelector('#modalReservationId');
    
    // Le asignamos el num_book al atributo 'value' del input oculto
    inputHidden.value = reservationId;

    // (Opcional) Si quieres verificar en la consola del navegador que se asignó bien:
    console.log("ID de reserva cargado en el modal:", inputHidden.value);
});

const selectEstado = document.getElementById('selectEstado');
const contenedorMotivo = document.getElementById('contenedorMotivo');
const selectMotivo = document.getElementById('selectMotivo');

// Escuchar los cambios en el select de Estado
selectEstado.addEventListener('change', function() {
    if (this.value === '2') { // '2' es Cancelar
        // Mostrar el contenedor quitando 'd-none' y hacer el campo obligatorio
        contenedorMotivo.classList.remove('d-none');
        selectMotivo.setAttribute('required', 'true');
    } else {
        // Ocultar el contenedor, quitar la obligatoriedad y resetear su valor
        contenedorMotivo.classList.add('d-none');
        selectMotivo.removeAttribute('required');
        selectMotivo.value = ""; 
    }
});

// RECOMENDACIÓN: Resetear todo cuando el modal se vuelva a abrir
modalCambiarEstado.addEventListener('show.bs.modal', function (event) {
    // ... tu código anterior para asignar el id ...
    
    // Dejar los selects como al principio cada vez que se abra el modal
    selectEstado.value = "";
    contenedorMotivo.classList.add('d-none');
    selectMotivo.removeAttribute('required');
    selectMotivo.value = "";
});


    /*=============================================
    Función Global
    =============================================*/

    function initializeReservationSystem() {

        generateCalendar();
        setupEventListeners();
        loadSelectedDateReservations();

        // Seleccionar fecha de hoy por defecto
        const today = new Date();
        selectDate(today);

    }

    /*=============================================
    Inicializamos las funciones del sistema
    =============================================*/
    initializeReservationSystem();

    /*=============================================
    Limpiar el formulario
    =============================================*/
    function clearReservationForm() {
        $('#reservationForm')[0].reset();
    }

})

$(document).on("click", ".btn-ver-cotizacion", function () {
    const serviciosRaw = $(this).attr("data-servicios");
    const servicios = JSON.parse(serviciosRaw || "[]");
    const tbody = $("#cotizacion-tbody");
    tbody.empty();

    let total = 0;

    servicios.forEach(s => {
        total += parseInt(s.precio);
        tbody.append(`
            <tr>
                <td>${s.nombre}</td>
                <td class="text-end fw-bold">$${parseInt(s.precio).toLocaleString('es-CO')}</td>
            </tr>
        `);
    });

    $("#cotizacion-total").text("$" + total.toLocaleString('es-CO'));
    $("#modalCotizacion").modal("show");
});

let tablesDatabase = {};
if ($("#tablesDatabase").length > 0) {
    tablesDatabase = JSON.parse($("#tablesDatabase").val());
    //console.log("tablesDatabase", tablesDatabase);
}

/*=============================================
Traer tarjeta de horas disponibles
=============================================*/


$("#id_table_book").on("change", function () {
    const idEspecialista = $(this).val();
    poblarHoras(idEspecialista);
    renderCotizador(idEspecialista);
    actualizarTarjetaYHoras();
});

$(document).on("click", "#calendarDays .calendar-day", function () {
    if ($(this).hasClass("disabled")) return;
    $("#calendarDays .calendar-day").removeClass("selected");
    $(this).addClass("selected");
    actualizarTarjetaYHoras();
});



function poblarHoras(idEspecialista) {
    const select = $("#time_book");
    select.empty().append('<option value="">Seleccionar hora</option>');

    if (!idEspecialista || !tablesDatabase[idEspecialista]) return;

    const especialista = tablesDatabase[idEspecialista][0];

    especialista.horas.forEach(hora => {
        const label = new Date(`2000-01-01T${hora}:00`)
            .toLocaleTimeString('es-CO', { hour: 'numeric', minute: '2-digit', hour12: true });
        select.append(`<option value="${hora}">${label}</option>`);
    });
}

// ─── Utilidades de tiempo ───────────────────────────────────────────
function parseTime12(str) {
    const [time, meridiem] = str.trim().split(' ');
    let [h, m] = time.split(':').map(Number);
    if (meridiem === 'PM' && h !== 12) h += 12;
    if (meridiem === 'AM' && h === 12) h = 0;
    return h * 60 + m;
}

function parseTime24(str) {
    const [h, m] = str.split(':').map(Number);
    return h * 60 + m;
}

// ─── Obtener fecha seleccionada del calendario ───────────────────────
function getFechaSeleccionada() {
    const diaSeleccionado = document.querySelector('#calendarDays .calendar-day.selected');
    if (!diaSeleccionado) return null;
    const isoDate = diaSeleccionado.getAttribute('data-date'); // "2026-04-10T05:00:00.000Z"
    return isoDate.split('T')[0]; // → "2026-04-10"
}

// ─── Estado del especialista según reservas de hoy ──────────────────
function getEstadoEspecialista(nombre, reservations) {
    const ahora = new Date();
    const hoyKey = ahora.toISOString().split('T')[0];
    const minAhora = ahora.getHours() * 60 + ahora.getMinutes();

    const reservasHoy = (reservations[hoyKey] || []).filter(r => r.table === nombre);
    if (!reservasHoy.length) return { estado: 'libre', reserva: null };

    const enTurno = reservasHoy.find(r => {
        const inicio = parseTime12(r.time);
        return minAhora >= inicio && minAhora < inicio + 60;
    });

    if (enTurno) return { estado: 'en_turno', reserva: enTurno };
    return { estado: 'reservado', reserva: reservasHoy[0] };
}

// ─── Bloquear horas ocupadas en el select ───────────────────────────
function bloquearHorasOcupadas(nombreEspecialista, fechaSeleccionada) {
    const reservations = JSON.parse(document.getElementById('reservationsDatabase').value);
    const selectHora = document.getElementById('time_book');

    Array.from(selectHora.options).forEach(opt => {
        opt.disabled = false;
        opt.textContent = opt.textContent.replace(' (ocupado)', '');
    });

    if (!fechaSeleccionada) return;

    const reservasDelDia = (reservations[fechaSeleccionada] || [])
        .filter(r => r.table === nombreEspecialista);

    reservasDelDia.forEach(r => {
        const minReserva = parseTime12(r.time);
        Array.from(selectHora.options).forEach(opt => {
            if (!opt.value) return;
            if (parseTime24(opt.value) === minReserva) {
                opt.disabled = true;
                opt.textContent += ' (ocupado)';
            }
        });
    });
}

// ─── Renderizar tarjeta ──────────────────────────────────────────────
function renderTarjeta(especialista, estado, reserva) {
    const badgeCfg = {
        libre: { clase: 'badge-libre', texto: 'Libre' },
        reservado: { clase: 'badge-reservado', texto: 'Reservado' },
        en_turno: { clase: 'badge-turno', texto: 'En turno' },
    };

    const { clase, texto } = badgeCfg[estado];
    const enTurno = estado === 'en_turno';

    const infoExtra = reserva ? `
        <p class="tarjeta-desc" style="font-size:12px; opacity:.85; margin:0;">
            ${enTurno ? 'Atendiendo a' : 'Próximo turno:'}
            ${reserva.customerClient} · ${reserva.time}
        </p>` : '';

    return `
        <div class="tarjeta-especialista">
            <div class="tarjeta-top">
                <img src="${especialista.imagen}"
                     alt="${especialista.especialista}"
                     class="tarjeta-img"
                     onerror="this.style.display='none'">
                <span class="tarjeta-nombre">${especialista.especialista}</span>
                <span class="badge ${clase}">${texto}</span>
            </div>
            <p class="tarjeta-desc">Información_ Hola: ${especialista.descripcion}</p>
            ${infoExtra}
         
        </div>`;
}

// ─── Función central: actualizar tarjeta + horas ─────────────────────
function actualizarTarjetaYHoras() {
    const tablesDatabase = JSON.parse(document.getElementById('tablesDatabase').value);
    const reservations = JSON.parse(document.getElementById('reservationsDatabase').value);
    const contenedor = document.getElementById('contenedor-tarjeta');
    const selectEsp = document.getElementById('id_table_book');
    const fechaElegida = getFechaSeleccionada();

    if (!selectEsp.value) { contenedor.innerHTML = ''; return; }

    const especialista = tablesDatabase[selectEsp.value]?.[0];
    if (!especialista) { contenedor.innerHTML = ''; return; }

    // Tarjeta: estado según HOY (no la fecha del calendario)
    const { estado, reserva } = getEstadoEspecialista(especialista.especialista, reservations);
    contenedor.innerHTML = renderTarjeta(especialista, estado, reserva);

    // Horas: bloqueadas según la fecha elegida en el calendario
    bloquearHorasOcupadas(especialista.especialista, fechaElegida);

    const btn = contenedor.querySelector('.tarjeta-btn:not(:disabled)');
    if (btn) {
        btn.addEventListener('click', () => {
            //console.log('Turno para especialista id:', especialista.id);
            // Tu lógica AJAX aquí
        });
    }
}



// En el JS del formulario, tras el SweetAlert de éxito
function openWhatsAppConfirmation(phone, clientName, date, time, specialist, numBook) {
    const msg = encodeURIComponent(
        `✅ Confirmación de Reserva\n\n` +
        `👤 Cliente: ${clientName}\n` +
        `📅 Fecha: ${date}\n` +
        `🕐 Hora: ${time}\n` +
        `💆 Especialista: ${specialist}\n` +
        `🔢 N° Reserva: #${numBook}\n\n` +
        `¡Te esperamos! 🙏`
    );
    window.open(`https://wa.me/${phone}?text=${msg}`, '_blank');
}


//---------------------------
// Función para agregar servicios a la reserva
//---------------------------
//serviciosActivos = {};

function parsearServicios(serviciosStr) {
    try {
        const servicios = JSON.parse(serviciosStr);
        return servicios.map(s => {
            const parts = s.descripcion.split("^");
            return {
                nombre: parts[0],
                id: parts[1],
                precio: parseInt(parts[2]),
                categoria: parts[3],
                imagen: parts[4]
            };
        });
    } catch (e) {
        return [];
    }
}

function renderCotizador(idEspecialista) {
    const especialista = tablesDatabase[idEspecialista]?.[0];
    const contenedor = $("#cotizador-servicios");
    contenedor.empty();
    serviciosActivos = {};
    actualizarTotal();

    if (!especialista || !especialista.servicios) return;

    const servicios = parsearServicios(especialista.servicios);

    servicios.forEach(s => {
        contenedor.append(`
            <div class="cotizador-item d-flex align-items-center gap-2 p-2 border rounded mb-2" id="item_${s.id}">
                <img src="${s.imagen}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;" 
                     onerror="this.src='https://placehold.co/40x40'">
                <span class="flex-grow-1">${s.nombre}</span>
                <span class="fw-bold">$${s.precio.toLocaleString('es-CO')}</span>
                <button type="button" class="btn btn-success btn-sm btn-agregar" 
                        data-id="${s.id}" data-precio="${s.precio}" data-nombre="${s.nombre}">
                    <i class="fas fa-plus"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm btn-remover d-none" 
                        data-id="${s.id}">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        `);
    });
}

// Agregar servicio
$(document).on("click", ".btn-agregar", function () {
    const id = $(this).data("id").toString();
    const precio = parseInt($(this).data("precio"));
    const nombre = $(this).data("nombre");

    serviciosActivos[id] = { id, nombre, precio };

    // Intercambiar botones
    $(this).addClass("d-none");
    $(`#item_${id} .btn-remover`).removeClass("d-none");

    actualizarTotal();
});

// Remover servicio
$(document).on("click", ".btn-remover", function () {
    const id = $(this).data("id").toString();

    delete serviciosActivos[id];

    // Intercambiar botones
    $(this).addClass("d-none");
    $(`#item_${id} .btn-agregar`).removeClass("d-none");

    actualizarTotal();
});

function actualizarTotal() {
    const serviciosSeleccionados = Object.values(serviciosActivos);
    const total = serviciosSeleccionados.reduce((sum, s) => sum + s.precio, 0);

    $("#cotizador-total").text("$" + total.toLocaleString('es-CO'));
    $("#servicios_book").val(JSON.stringify(serviciosSeleccionados));
}

