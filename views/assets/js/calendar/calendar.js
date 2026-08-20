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
        $("#modalFechaSeleccionada").text(date.toLocaleDateString('es-ES', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        }));

        loadSelectedDateReservations();
        // Abrir el modal de reserva
        const modalReserva = new bootstrap.Modal(document.getElementById('modalNuevaReserva'));
        modalReserva.show();

        window.onerror = function(msg, src, line) {
    
};

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
                                class="btn btn-sm p-0 border-0 alignment-baseline btn-gestionar-reserva" 
                                data-id="${reservation.num_book}"
                                data-cliente="${reservation.customerClient}"
                                data-especialista="${reservation.table}"
                                data-fecha="${reservation.date_book}"
                                data-id-table="${reservation.idTable}"
                                data-hora="${reservation.time}">
                                <span class="badge bg-warning text-dark" style="cursor: pointer;">
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



    /*=============================================
    Función Global
    =============================================*/

    function initializeReservationSystem() {

        generateCalendar();
        setupEventListeners();
        loadSelectedDateReservations();

        // Seleccionar fecha de hoy por defecto SIN abrir el modal
    const today = new Date();
    selectedDate = today;
    $('.calendar-day').removeClass('selected');
    $(`.calendar-day[data-date="${today.toISOString()}"]`).addClass('selected');
    $("#date_book").val(today.toLocaleDateString());
    loadSelectedDateReservations();

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
        select.append(`<option value="${hora}" data-label="${label}">${label}</option>`);
    });
}

// ─── Utilidades de tiempo ───────────────────────────────────────────
function parseTime12(str) {
    if (!str) return null;

    // Normaliza "3:00 p. m." → "3:00 PM"
    const normalized = str
        .replace(/\s*p\.\s*m\.?/gi, ' PM')
        .replace(/\s*a\.\s*m\.?/gi, ' AM')
        .trim();

    const match = normalized.match(/(\d{1,2}):(\d{2})\s*(AM|PM)/i);
    if (!match) return null;

    let h = parseInt(match[1]);
    const m = parseInt(match[2]);
    const period = match[3].toUpperCase();

    if (period === 'PM' && h !== 12) h += 12;
    if (period === 'AM' && h === 12) h = 0;

    return h * 60 + m;
}

function parseTime24(str) {
  if (!str) return null;
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
function bloquearHorasOcupadas_2(nombreEspecialista, fechaSeleccionada) {
    const reservations = JSON.parse(document.getElementById('reservationsDatabase').value);
    const selectHora = document.getElementById('time_book');
    
    // Reset general
    Array.from(selectHora.options).forEach(opt => {
        opt.disabled = false;
        const original = opt.getAttribute('data-label');
        if (original) opt.textContent = original;
      opt.textContent = opt.textContent.replace(' (ocupado)', '').replace(' (no disponible)', '');
    });

    if (!fechaSeleccionada) return;

    // ── 1. Bloquear por reservas existentes (excluyendo canceladas) ──
    const reservasDelDia = (reservations[fechaSeleccionada] || [])
        .filter(r => r.idTable === nombreEspecialista && String(r.confirmado) !== "2");
    reservasDelDia.forEach(r => {
        const minReserva = parseTime12(r.time);

        Array.from(selectHora.options).forEach(opt => {
            if (!opt.value) return;
            const minOpt = parseTime24(opt.value);
            if (minOpt === minReserva) {
                opt.disabled = true;
                opt.textContent += ' (ocupado)';
            }
        });
    });

    // ── 2. Bloquear horas pasadas si la fecha seleccionada es HOY ──
    //const hoy = new Date();
    const hoy = new Date().toISOString().split('T')[0];

    if (fechaSeleccionada === hoy) {
        const minutosAhora = new Date().getHours() * 60 + new Date().getMinutes();
        console.log("Minutos ahora:", minutosAhora);
        Array.from(selectHora.options).forEach(opt => {
            if (!opt.value) return;
            console.log("Valor de la opción:", opt.value);
            console.log("Minutos de la opción:", parseTime24(opt.value));
            if (parseTime24(opt.value) < minutosAhora) {
                opt.disabled = true;
                if (!opt.textContent.includes('(ocupado)')) opt.textContent += ' (no disponible)';
            }
        });
    }
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
                
            </div>
            <p class="tarjeta-desc">Información: ${especialista.descripcion}</p>
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
    bloquearHorasOcupadas_2(especialista.id, fechaElegida);
    const btn = contenedor.querySelector('.tarjeta-btn:not(:disabled)');
    if (btn) {
        btn.addEventListener('click', () => {
            console.log('Turno para especialista id:', especialista.id);
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
                imagen: parts[4],
                tiempo: parts[5]
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
                <!--<img src="${s.imagen}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;" 
                     onerror="this.src='https://placehold.co/40x40'">-->
                <div class="d-flex flex-column flex-grow-1">
                    <span class="fw-medium">${s.nombre}</span>
                    <small class="text-muted" style="font-size: 0.8em;">Tiempo Aprox.: ${s.tiempo}</small>
                </div>
                <span class="fw-bold">$${s.precio.toLocaleString('es-CO')}</span>
                <button type="button" class="btn btn-success btn-sm btn-agregar" 
                        data-id="${s.id}" data-precio="${s.precio}" data-nombre="${s.nombre}" data-tiempo="${s.tiempo}">
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
    const tiempo = $(this).data("tiempo");

    let tempo = 0;
    if      (tiempo == "30min")    tempo = 0.5;
    else if (tiempo == "1h")    tempo = 1;
    else if (tiempo == "1h:30min") tempo = 1.5;
    else if (tiempo == "2h")   tempo = 2;


    serviciosActivos[id] = { id, nombre, precio, tempo };

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
    const total       = serviciosSeleccionados.reduce((sum, s) => sum + s.precio, 0);
    const tiempoTotal = serviciosSeleccionados.reduce((sum, s) => {
        return sum + parseFloat(s.tempo || 0);
    }, 0);
    
    $("#cotizador-total").text("$" + total.toLocaleString('es-CO'));
    $("#servicios_book").val(JSON.stringify(serviciosSeleccionados));

    // Formatear tiempo
    const horas   = Math.floor(tiempoTotal);
    const minutos = (tiempoTotal % 1) * 60;

    let tiempoLabel = '';
    if (horas > 0 && minutos > 0) {
        tiempoLabel = `${horas}h ${minutos}min`;
    } else if (horas > 0) {
        tiempoLabel = `${horas}h`;
    } else if (minutos > 0) {
        tiempoLabel = `${minutos}min`;
    } else {
        tiempoLabel = '0 min';
    }

    $("#cotizador-tiempo").text(tiempoLabel);

    if (tiempoTotal > 1) {
        $("#alerta-tiempo").removeClass("d-none");
    } else {
        $("#alerta-tiempo").addClass("d-none");
    }
}


//-----------------------------------------------------
// Funciones para el modal de reprogramar
//-----------------------------------------------------

// Mostrar/ocultar paneles según selección
document.getElementById('selectEstado').addEventListener('change', function () {
    const val = this.value;
    document.getElementById('contenedorMotivo').classList.toggle('d-none', val !== '2');
    document.getElementById('contenedorReprogramar').classList.toggle('d-none', val !== '3');
});

// Función para obtener el ID de la mesa por nombre
function getIdTableByName(nombre) {
    return Object.keys(tablesDatabase).find(id => {
        const obj = tablesDatabase[id][0];
        return obj.especialista === nombre;
    }) || null;
}



// Cuando cambia la nueva fecha, rebloquear horas ocupadas
document.getElementById('reprNuevaFecha').addEventListener('change', function () {
    const idTable = document.getElementById('modalCambiarEstado')
        .querySelector('[data-bs-target="#modalCambiarEstado"]')?.getAttribute('data-id-table');
    bloquearHorasRepr(this.value);
});

// Poblar el select de horas del reprogramador con las horas del especialista
function poblarHorasRepr(idEspecialista, fechaActual) {
    const select = document.getElementById('reprNuevaHora');
    select.innerHTML = '<option value="">Seleccionar hora</option>';
    //console.log("objeto completo:", tablesDatabase[24][0]);
    //console.log("idTable encontrado:", idEspecialista);
    if (!idEspecialista || !tablesDatabase[idEspecialista]) return;

    const especialista = tablesDatabase[idEspecialista][0];
    especialista.horas.forEach(hora => {
        const label = new Date(`2000-01-01T${hora}:00`)
            .toLocaleTimeString('es-CO', { hour: 'numeric', minute: '2-digit', hour12: true });
        const opt = document.createElement('option');
        opt.value = hora;
        opt.textContent = label;
        opt.setAttribute('data-label', label);
        select.appendChild(opt);
    });
}

// Bloquear horas ocupadas en el select del reprogramador
function bloquearHorasRepr(fechaSeleccionada) {
    const select = document.getElementById('reprNuevaHora');
    const idTable = document.getElementById('modalCambiarEstado')
        ._triggerBtn?.getAttribute('data-id-table');

    // Reset
    Array.from(select.options).forEach(opt => {
        opt.disabled = false;
        const original = opt.getAttribute('data-label');
        if (original) opt.textContent = original;
    });

    if (!fechaSeleccionada) return;

    const reservations = JSON.parse(document.getElementById('reservationsDatabase').value);
    const nombreEspecialista = document.getElementById('reprEspecialista').textContent;

    const reservasDelDia = (reservations[fechaSeleccionada] || [])
        .filter(r => r.table === nombreEspecialista && String(r.confirmado) !== '2');

    reservasDelDia.forEach(r => {
        const minReserva = parseTime12(r.time);
        Array.from(select.options).forEach(opt => {
            if (!opt.value) return;
            if (parseTime24(opt.value) === minReserva) {
                opt.disabled = true;
                opt.textContent += ' (ocupado)';
            }
        });
    });

    // Bloquear horas pasadas si es hoy
    const hoy = new Date().toISOString().split('T')[0];
    if (fechaSeleccionada === hoy) {
        const minutosAhora = new Date().getHours() * 60 + new Date().getMinutes();
        Array.from(select.options).forEach(opt => {
            if (!opt.value) return;
            if (parseTime24(opt.value) < minutosAhora) {
                opt.disabled = true;
                if (!opt.textContent.includes('(ocupado)')) opt.textContent += ' (no disponible)';
            }
        });
    }
}



//--Al final del archivo, después de todo el código existente:
// ── Listener único para abrir modalCambiarEstado ──
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-gestionar-reserva');
    if (!btn) return;

    const id           = btn.getAttribute('data-id');
    const cliente      = btn.getAttribute('data-cliente');
    const especialista = btn.getAttribute('data-especialista');
    const fecha        = btn.getAttribute('data-fecha');
    const hora         = btn.getAttribute('data-hora');
    const idTable      = btn.getAttribute('data-id-table');

    // Guardar referencia para bloquearHorasRepr
    document.getElementById('modalCambiarEstado')._triggerBtn = btn;

    document.getElementById('modalReservationId').value = id;
    document.getElementById('reprCliente').textContent       = cliente;
    document.getElementById('reprEspecialista').textContent  = especialista;
    document.getElementById('reprFecha').textContent         = fecha;
    document.getElementById('reprHora').textContent          = hora;

    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('reprNuevaFecha').min   = hoy;
    document.getElementById('reprNuevaFecha').value = '';

    poblarHorasRepr(idTable, fecha);

    // Reset estado
    document.getElementById('selectEstado').value = '';
    document.getElementById('selectMotivo').value = '';
    document.getElementById('selectMotivo').removeAttribute('required');
    document.getElementById('contenedorMotivo').classList.add('d-none');
    document.getElementById('contenedorReprogramar').classList.add('d-none');

    new bootstrap.Modal(document.getElementById('modalCambiarEstado')).show();
});
