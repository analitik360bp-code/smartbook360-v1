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

?>


<main class="flex-grow flex items-center justify-center p-margin-mobile md:p-margin-desktop relative overflow-hidden">
    <!-- Confetti effect script will target this -->
    <canvas class="confetti-canvas" id="confetti"></canvas>
    <div class="w-full max-w-container-max flex flex-col items-center justify-center text-center">
        <!-- Success Card Container -->
        <div
            class="bg-surface-container-lowest rounded-xl shadow-[0_4px_12px_0_rgba(0,0,0,0.05)] border border-outline-variant p-8 md:p-12 max-w-2xl w-full transform transition-all duration-700 ease-out translate-y-0 opacity-100 scale-100">
            <!-- Icon Animation Section -->
            <div class="relative mb-stack-lg flex justify-center">
                <div class="absolute w-24 h-24 bg-primary/10 rounded-full success-check-pulse"></div>
                <div
                    class="relative w-24 h-24 bg-primary text-on-primary rounded-full flex items-center justify-center shadow-lg">
                    <span class="material-symbols-outlined text-[48px]"
                        style="font-variation-settings: 'FILL' 1;">check_circle</span>
                </div>
            </div>
            <!-- Message Content -->
            <h1
                class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface mb-stack-sm">
                ¡Cita Confirmada!
            </h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant mb-stack-lg max-w-md mx-auto">
                Tu cita con <span class="font-semibold text-primary"><?php echo $title_table; ?></span> ha sido
                registrada exitosamente.
            </p>
            <!-- Appointment Details Glassmorphism Card -->
            <div class="bg-surface-container-low rounded-lg p-6 mb-stack-lg text-left border border-outline-variant/50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md">
                    <div class="flex items-start gap-stack-sm">
                        <span class="material-symbols-outlined text-primary"
                            data-icon="calendar_today">calendar_today</span>
                        <div>
                            <p class="font-label-sm text-label-sm text-on-surface-variant">Fecha</p>
                            <p class="font-body-md text-body-md font-semibold"><?php echo $date_book; ?></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-stack-sm">
                        <span class="material-symbols-outlined text-primary" data-icon="schedule">schedule</span>
                        <div>
                            <p class="font-label-sm text-label-sm text-on-surface-variant">Hora</p>
                            <p class="font-body-md text-body-md font-semibold"><?php echo $time_book; ?></p>
                        </div>
                    </div>
                    
                </div>
            </div>
            <!-- Actions -->
            
            <div class="mt-stack-lg pt-stack-lg border-t border-outline-variant">
                <p class="font-label-sm text-label-sm text-on-surface-variant">
                    Se ha enviado un correo de confirmación a <span class="font-semibold"><?php echo $email_book; ?></span>
                    </p>
            </div>
        </div>
        <!-- Contextual Illustration (Desktop Only) -->
        <div class="hidden lg:block absolute -right-24 top-1/2 -translate-y-1/2 opacity-20 pointer-events-none">
            <img alt="Professional healthcare confirmation" class="w-[400px] h-[400px] object-cover rounded-full"
                data-alt="A sophisticated digital artwork of a modern medical office with soft, diffused sunlight filtering through large windows. The scene features minimalist furniture and healthy green plants, creating an atmosphere of calm and professional clinical hygiene. The color palette is dominated by soft whites and clinical blues, maintaining a high-key light mode aesthetic that feels airy and welcoming."
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAIlWDngSNTXil7czAafdL-a3xxuNESDYVzoD6mTYndi5FaQciZ-0LPaFV2aqj5099tGdWsLP5rwMy9oTMoRDMT1JSSTFr2yc0K-0BiL-3jgULnSW_RaNb2niPxAxMjdOR3jsHi5pUf5rKuDnfhR4eZuxxLGD8EosW353aE8ZpXqWJc4thtCTxDr-XRzZ2NtkzsI_BPRo4ZPSXAMdPUDOoje51hF7t7fr5APjxAKXafY5d-xQLQQv2oWb4kGTH1-K6nurivzGnoEmjt" />
        </div>
    </div>
</main>
<!-- Bottom Navigation Bar (Mobile only) -->
<nav
    class="md:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center h-16 px-4 pb-safe bg-surface-container dark:bg-surface-dim shadow-[0_-4px_12px_0_rgba(0,0,0,0.05)] dark:shadow-none">
    <button
        class="flex flex-col items-center justify-center bg-secondary-container dark:bg-secondary-fixed text-on-secondary-container dark:text-on-secondary-fixed rounded-full px-4 py-1 active:scale-95 transition-transform duration-150">
        <span class="material-symbols-outlined" data-icon="calendar_today">calendar_today</span>
        <span class="font-label-sm text-label-sm">Appointments</span>
    </button>
    <button
        class="flex flex-col items-center justify-center text-on-surface-variant dark:text-on-secondary-fixed-variant hover:bg-surface-variant dark:hover:bg-inverse-surface active:scale-95 transition-transform duration-150">
        <span class="material-symbols-outlined" data-icon="history">history</span>
        <span class="font-label-sm text-label-sm">History</span>
    </button>
    <button
        class="flex flex-col items-center justify-center text-on-surface-variant dark:text-on-secondary-fixed-variant hover:bg-surface-variant dark:hover:bg-inverse-surface active:scale-95 transition-transform duration-150">
        <span class="material-symbols-outlined" data-icon="settings">settings</span>
        <span class="font-label-sm text-label-sm">Settings</span>
    </button>
</nav>
<script>
    // Simple confetti effect
    const canvas = document.getElementById('confetti');
    const ctx = canvas.getContext('2d');
    let particles = [];

    function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    window.addEventListener('resize', resize);
    resize();

    class Particle {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = -10;
            this.size = Math.random() * 8 + 4;
            this.speedY = Math.random() * 3 + 1;
            this.speedX = Math.random() * 4 - 2;
            this.rotation = Math.random() * 360;
            this.rotationSpeed = Math.random() * 5;
            this.color = ['#2563EB', '#60A5FA', '#10B981', '#34D399'][Math.floor(Math.random() * 4)];
        }
        update() {
            this.y += this.speedY;
            this.x += this.speedX;
            this.rotation += this.rotationSpeed;
        }
        draw() {
            ctx.save();
            ctx.translate(this.x, this.y);
            ctx.rotate(this.rotation * Math.PI / 180);
            ctx.fillStyle = this.color;
            ctx.fillRect(-this.size / 2, -this.size / 2, this.size, this.size);
            ctx.restore();
        }
    }

    function initConfetti() {
        for (let i = 0; i < 50; i++) {
            particles.push(new Particle());
        }
    }

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        for (let i = 0; i < particles.length; i++) {
            particles[i].update();
            particles[i].draw();
            if (particles[i].y > canvas.height) {
                particles.splice(i, 1);
                i--;
            }
        }
        if (particles.length > 0) requestAnimationFrame(animate);
    }

    // Trigger on load
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            initConfetti();
            animate();
        }, 500);
    });
</script>
</body>

</html>